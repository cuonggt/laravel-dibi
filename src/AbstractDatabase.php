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
        $query = $this->buildSelectQuery($table, $request->only(['field', 'operator', 'keyword']));

        $total = DB::table($table)->count();

        $count = $query->count();

        $currentPage = $request->input('page', 1);

        $perPage = $request->input('per_page', config('dibi.limit'));

        $data = $query->selectRaw('*, '.$this->generateTableKey($table).' as __id__')
            ->orderBy(
                $request->sort_key,
                $request->input('sort_direction', 'asc')
            )
            ->skip(($currentPage - 1) * $perPage)
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
     * @param  array  $params
     * @return array
     */
    public function buildSelectQuery($table, $params)
    {
        $query = DB::table($table);

        if (empty($params['field'])) {
            return $query;
        }

        if ($params['operator'] == 'IS NULL') {
            return $query->whereNull($params['field']);
        }

        if ($params['operator'] == 'IS NOT NULL') {
            return $query->whereNotNull($params['field']);
        }

        if (empty($params['keyword'])) {
            return $query;
        }

        if ($params['operator'] == 'IN') {
            return $query->whereIn($params['field'], array_filter(explode(',', $params['keyword'])));
        }

        return $query->where($params['field'], $params['operator'], $params['keyword']);
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
