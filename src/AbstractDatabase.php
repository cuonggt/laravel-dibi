<?php

namespace Cuonggt\Dibi;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\DB;

abstract class AbstractDatabase
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function tables()
    {
        return array_map([$this, 'mapTableToObject'], $this->getTables());
    }

    public function table($table)
    {
        return $this->mapTableToObject($this->getTableByName($table));
    }

    public function columns($table)
    {
        return array_map([$this, 'mapColumnToObject'], $this->getColumns($table));
    }

    public function indexes($table)
    {
        return array_map([$this, 'mapIndexToObject'], $this->getIndexes($table));
    }

    public function rows($table, $params)
    {
        $query = $this->buildSelectQuery($table, $params);

        $total = DB::table($table)->count();

        $count = $query->count();

        $data = $query
                    ->selectRaw('*, '.$this->generateTableKey($table).' as __id__')
                    ->orderBy($params['sorting'], $params['direction'])
                    ->take(config('dibi.limit', 100))
                    ->get();

        return compact('total', 'count', 'data');
    }

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

    public function updateRow($table, $params)
    {
        $query = DB::table($table);

        foreach ($params['row'] as $k => $v) {
            if ($k != '__id__') {
                $query->where($k, $v);
            }
        }

        return $query->update([$params['column']['field'] => $params['value']]);
    }

    abstract protected function getTables();

    abstract protected function getTableByName($table);

    abstract protected function mapTableToObject($table);

    abstract protected function getColumns($table);

    abstract protected function mapColumnToObject($column);

    abstract protected function getIndexes($table);

    abstract protected function mapIndexToObject($index);

    abstract protected function getKeyName($table);
}
