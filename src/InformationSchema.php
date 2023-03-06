<?php

namespace Cuonggt\Dibi;

use JsonSerializable;

class InformationSchema implements JsonSerializable
{
    public $tables = [];

    public function __construct(array $tables = [])
    {
        $this->tables = $tables;
    }

    public function jsonSerialize()
    {
        return [
            'tables' => $this->tables,
        ];
    }
}
