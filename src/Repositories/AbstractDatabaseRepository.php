<?php

namespace Cuonggt\Dibi\Repositories;

use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Cuonggt\Dibi\Contracts\DatabaseRepository;

abstract class AbstractDatabaseRepository implements DatabaseRepository
{
    /**
     * The database name.
     *
     * @var string
     */
    protected $name;

    /**
     * Create a new database repository.
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
        return collect($this->rawTables())->map(function ($rawTable) {
            return $this->mapRawTableToObject($rawTable);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function table($tableName)
    {
        return $this->mapRawTableToObject($this->rawTableByName($tableName));
    }

    /**
     * {@inheritdoc}
     */
    public function columns($table)
    {
        return collect($this->rawColumns($table))->map(function ($rawColumn) {
            return $this->mapRawColumnToObject($rawColumn);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function indexes($table)
    {
        return collect($this->rawIndexes($table))->map(function ($rawIndex) {
            return $this->mapRawIndexToObject($rawIndex);
        });
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

        $query = $query->selectRaw('*, '.$this->generateTableKey($table).' as __id__');

        if (! empty($request->sort_key)) {
            $query = $query->orderBy(
                $request->sort_key,
                $request->input('sort_direction', 'asc')
            );
        }

        return $query->paginate($request->input('per_page', 15));
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
    protected function generateTableKey($table)
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
    protected function buildSelectQuery($table, $filterField = '', $filterOperator = '=', $filterValue = '')
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
     * Get list of raw tables.
     *
     * @return array
     */
    abstract protected function rawTables();

    /**
     * Get the raw table for the given name.
     *
     * @param  string  $table
     * @return object
     */
    abstract protected function rawTableByName($table);

    /**
     * Map the raw table object to a Dibi Table instance.
     *
     * @param  object  $rawTable
     * @return \Cuonggt\Dibi\Table
     */
    abstract protected function mapRawTableToObject($rawTable);

    /**
     * Get list of raw columns for the given table.
     *
     * @param  string  $table
     * @return array
     */
    abstract protected function rawColumns($table);

    /**
     * Map the raw column object to a Dibi Column instance.
     *
     * @param  object  $rawColumn
     * @return \Cuonggt\Dibi\Column
     */
    abstract protected function mapRawColumnToObject($rawColumn);

    /**
     * Get list of raw indexes for the given table.
     *
     * @param  string  $table
     * @return array
     */
    abstract protected function rawIndexes($table);

    /**
     * Map the raw index object to a Dibi TableIndex instance.
     *
     * @param  object  $index
     * @return \Cuonggt\Dibi\TableIndex
     */
    abstract protected function mapRawIndexToObject($index);

    /**
     * Get the primary key for the given table.
     *
     * @param  string  $table
     * @return array|string
     */
    abstract protected function getKeyName($table);
}
