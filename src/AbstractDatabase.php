<?php

namespace Cuonggt\Dibi;

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
        return $this->getRows($table, $params);
    }

    abstract protected function getTables();

    abstract protected function mapTableToObject($table);

    abstract protected function getColumns($table);

    abstract protected function mapColumnToObject($column);

    abstract protected function getRows($table, $params);
}
