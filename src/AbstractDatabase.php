<?php

namespace Cuonggt\Dibi;

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

    public function rows($table, $params)
    {
        $query = $this->buildSelectQuery($table, $params);

        $total = DB::table($table)->count();

        $count = $query->count();

        $data = $query->orderBy($params['sorting'], $params['direction'])
                    ->take(config('dibi.limit', 100))
                    ->get();

        return compact('total', 'count', 'data');
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

    abstract protected function getTables();

    abstract protected function mapTableToObject($table);

    abstract protected function getColumns($table);

    abstract protected function mapColumnToObject($column);
}
