<?php

namespace Cuonggt\Dibi;

use InvalidArgumentException;

class DatabaseProviderFactory
{
    /**
     * Get the Dibi database instance for the given type.
     *
     * @param  string  $type
     * @param  string  $name
     * @return \Cuonggt\Dibi\AbstractDatabase
     */
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
