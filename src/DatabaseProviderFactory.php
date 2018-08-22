<?php

namespace Cuonggt\Dibi;

use InvalidArgumentException;
use Cuonggt\Dibi\MysqlDatabase;

class DatabaseProviderFactory
{
    public function make($type, $name)
    {
        switch ($type) {
            case 'mysql':
                return new MysqlDatabase($name);
            default:
                throw new InvalidArgumentException('Database provider is not supported.');
        }
    }
}
