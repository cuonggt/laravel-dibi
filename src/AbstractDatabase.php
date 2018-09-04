<?php

namespace Cuonggt\Dibi;

use Ramsey\Uuid\Uuid;
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
    public function rows($table, $params)
    {
        $query = $this->buildSelectQuery($table, $params);

        $total = DB::table($table)->count();

        $count = $query->count();

        $data = $query->selectRaw('*, '.$this->generateTableKey($table).' as __id__')
            ->orderBy($params['sorting'], $params['direction'])
            ->take(config('dibi.limit', 100))
            ->get();

        return compact('total', 'count', 'data');
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
     * Get the key name for the given table
     *
     * @param  string  $table
     * @return string
     */
    protected function generateTableKey($table)
    {
        $keyName = $this->getKeyName($table);

        if (!$keyName) {
            return '"' . Uuid::uuid4() . '"';
        }

        if (is_array($keyName)) {
            $keys = [];

            foreach ($keyName as $v) {
                array_push($keys, $v, '"_"');
            }

            array_pop($keys);

            return 'concat(' . implode(',', $keys) . ')';
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
    protected function buildSelectQuery($table, $params)
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
    abstract protected function getTables();

    /**
     * Get the raw table for the given name.
     *
     * @param  string  $table
     * @return object
     */
    abstract protected function getTableByName($table);

    /**
     * Map the raw table object to a Dibi Table instance.
     *
     * @param  object  $table
     * @return \Cuonggt\Dibi\Table
     */
    abstract protected function mapTableToObject($table);

    /**
     * Get list of columns for the given table.
     *
     * @return array
     */
    abstract protected function getColumns($table);

    /**
     * Map the raw column object to a Dibi Column instance.
     *
     * @param  object  $column
     * @return \Cuonggt\Dibi\Column
     */
    abstract protected function mapColumnToObject($column);

    /**
     * Get list of indexes for the given table.
     *
     * @return array
     */
    abstract protected function getIndexes($table);

    /**
     * Map the raw index object to a Dibi TableIndex instance.
     *
     * @param  object  $index
     * @return \Cuonggt\Dibi\TableIndex
     */
    abstract protected function mapIndexToObject($index);

    /**
     * Get the key name for the given table.
     *
     * @return array
     */
    abstract protected function getKeyName($table);
}
