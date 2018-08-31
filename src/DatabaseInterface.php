<?php

namespace Cuonggt\Dibi;

interface DatabaseInterface
{
    public function tables();

    public function table($table);

    public function columns($table);

    public function indexes($table);

    public function rows($table);

    public function updateRow($table, $params);
}
