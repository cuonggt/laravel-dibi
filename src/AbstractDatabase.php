<?php

namespace Cuonggt\Dibi;

use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

abstract class AbstractDatabase implements DatabaseInterface
{
    /**
     * The database name.
     *
     * @var string
     */
    protected $name;

    /**
     * Create a new Dibi database instance.
     *
     * @param  string   $name
     * @return void
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function tables()
    {
        return array_map([$this, 'mapTableToObject'], $this->getTables());
    }

    /**
     * {@inheritdoc}
     */
    public function table($table)
    {
        return $this->mapTableToObject($this->getTableByName($table));
    }

    /**
     * {@inheritdoc}
     */
    public function columns($table)
    {
        return array_map([$this, 'mapColumnToObject'], $this->getColumns($table));
    }

    /**
     * {@inheritdoc}
     */
    public function indexes($table)
    {
        return array_map([$this, 'mapIndexToObject'], $this->getIndexes($table));
    }

    /**
     * {@inheritdoc}
     */
    public function rows($table, Request $request)
    {
        $query = $this->buildSelectQuery(
            $table,
            $request->input('filter_field', ''),
            $request->input('filter_operator', '='),
            $request->input('filter_value', '')
        );

        $total = DB::table($table)->count();

        $count = $query->count();

        $currentPage = $request->input('page', 1);

        $perPage = $request->input('per_page', config('dibi.limit'));

        $query = $query->selectRaw('*, '.$this->generateTableKey($table).' as __id__');

        if (! empty($request->sort_key)) {
            $query = $query->orderBy(
                $request->sort_key,
                $request->input('sort_direction', 'asc')
            );
        }

        $data = $query->skip(($currentPage - 1) * $perPage)
            ->take($perPage)
            ->get();

        return compact('total', 'count', 'data');
    }

    /**
     * {@inheritdoc}
     */
    public function addRow($table, $row)
    {
        return DB::table($table)->insert($row);
    }

    /**
     * {@inheritdoc}
     */
    public function updateRow($table, $row, $column, $value = null)
    {
        $query = DB::table($table);

        foreach ($row as $k => $v) {
            if ($k != '__id__') {
                $query->where($k, $v);
            }
        }

        return $query->update([$column['field'] => $value]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteRow($table, $row)
    {
        $query = DB::table($table);

        foreach ($row as $k => $v) {
            if ($k != '__id__') {
                $query->where($k, $v);
            }
        }

        return $query->delete();
    }

    /**
     * Generate a virtual field name for the given table's primary key.
     *
     * @param  string  $table
     * @return string
     */
    public function generateTableKey($table)
    {
        $keyName = $this->getKeyName($table);

        if (! $keyName) {
            return '"'.Uuid::uuid4().'"';
        }

        if (is_array($keyName)) {
            $keys = [];

            foreach ($keyName as $v) {
                array_push($keys, $v, '"_"');
            }

            array_pop($keys);

            return 'concat('.implode(',', $keys).')';
        }

        return $keyName;
    }

    /**
     * Build select query.
     *
     * @param  string  $table
     * @param  string  $filterField
     * @param  string  $filterOperator
     * @param  string  $filterValue
     * @return array
     */
    public function buildSelectQuery($table, $filterField = '', $filterOperator = '=', $filterValue = '')
    {
        $query = DB::table($table);

        if (empty($filterField)) {
            return $query;
        }

        if ($filterOperator == 'IS NULL') {
            return $query->whereNull($filterField);
        }

        if ($filterOperator == 'IS NOT NULL') {
            return $query->whereNotNull($filterField);
        }

        if ($filterOperator == 'IN') {
            return $query->whereIn($filterField, array_filter(explode(',', $filterValue)));
        }

        if ($filterOperator == 'NOT IN') {
            return $query->whereNotIn($filterField, array_filter(explode(',', $filterValue)));
        }

        if ($filterOperator == 'LIKE') {
            return $query->where($filterField, 'LIKE', '%'.$filterValue.'%');
        }

        if ($filterOperator == 'NOT LIKE') {
            return $query->where($filterField, 'NOT LIKE', '%'.$filterValue.'%');
        }

        if (empty($filterValue)) {
            return $query;
        }

        return $query->where($filterField, $filterOperator, $filterValue);
    }

    /**
     * Get list of tables.
     *
     * @return array
     */
    abstract public function getTables();

    /**
     * Get the raw table for the given name.
     *
     * @param  string  $table
     * @return object
     */
    abstract public function getTableByName($table);

    /**
     * Map the raw table object to a Dibi Table instance.
     *
     * @param  object  $table
     * @return \Cuonggt\Dibi\Table
     */
    abstract public function mapTableToObject($table);

    /**
     * Get list of columns for the given table.
     *
     * @param  string  $table
     * @return array
     */
    abstract public function getColumns($table);

    /**
     * Map the raw column object to a Dibi Column instance.
     *
     * @param  object  $column
     * @return \Cuonggt\Dibi\Column
     */
    abstract public function mapColumnToObject($column);

    /**
     * Get list of indexes for the given table.
     *
     * @param  string  $table
     * @return array
     */
    abstract public function getIndexes($table);

    /**
     * Map the raw index object to a Dibi TableIndex instance.
     *
     * @param  object  $index
     * @return \Cuonggt\Dibi\TableIndex
     */
    abstract public function mapIndexToObject($index);

    /**
     * Get the primary key for the given table.
     *
     * @param  string  $table
     * @return array|string
     */
    abstract public function getKeyName($table);
}
