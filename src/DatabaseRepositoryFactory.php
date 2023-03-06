<?php

namespace Cuonggt\Dibi;

use Cuonggt\Dibi\Repositories\MysqlDatabaseRepository;
use Cuonggt\Dibi\Repositories\SqlsrvDatabaseRepository;
use Illuminate\Database\Connection;
use InvalidArgumentException;

class DatabaseRepositoryFactory
{
    /**
     * Create a database repository instance for the given database connection.
     *
     * @param  \Illuminate\Database\Connection  $provider
     * @return \Cuonggt\Dibi\Contracts\DatabaseRepository
     */
    public static function make(Connection $db)
    {
        switch ($db->getDriverName()) {
            case 'mysql':
                return new MysqlDatabaseRepository($db);
            case 'sqlsrv':
                return new SqlsrvDatabaseRepository($db);
            default:
                throw new InvalidArgumentException('Database driver ['.$db->getDriverName().'] is not supported.');
        }
    }
}
