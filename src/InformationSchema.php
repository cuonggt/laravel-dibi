<?php

namespace Cuonggt\Dibi;

class InformationSchema
{
    public $tables = [];

    public function __construct(array $tables = [])
    {
        $this->tables = $tables;
    }
}
