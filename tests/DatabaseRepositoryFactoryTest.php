<?php

namespace Cuonggt\Dibi\Tests;

use Cuonggt\Dibi\DatabaseRepositoryFactory;
use Cuonggt\Dibi\Repositories\MysqlDatabaseRepository;
use Cuonggt\Dibi\Repositories\SqliteDatabaseRepository;
use Cuonggt\Dibi\Repositories\SqlsrvDatabaseRepository;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * The factory only reads the driver name, so these use real connection objects
 * rather than mocks. Laravel resolves PDO lazily, so no server is contacted.
 */
class DatabaseRepositoryFactoryTest extends FeatureTestCase
{
    /**
     * Register a connection for the given driver and return it.
     *
     * @return \Illuminate\Database\Connection
     */
    protected function connectionFor($driver)
    {
        config(["database.connections.factory_{$driver}" => [
            'driver' => $driver,
            'host' => '127.0.0.1',
            'database' => 'dibi_testing',
            'username' => 'dibi',
            'password' => '',
        ]]);

        return DB::connection("factory_{$driver}");
    }

    public function test_it_creates_a_mysql_repository_for_a_mysql_connection(): void
    {
        $this->assertInstanceOf(
            MysqlDatabaseRepository::class,
            DatabaseRepositoryFactory::make($this->connectionFor('mysql'))
        );
    }

    public function test_it_creates_a_sql_server_repository_for_a_sqlsrv_connection(): void
    {
        $this->assertInstanceOf(
            SqlsrvDatabaseRepository::class,
            DatabaseRepositoryFactory::make($this->connectionFor('sqlsrv'))
        );
    }

    public function test_it_creates_a_sqlite_repository_for_a_sqlite_connection(): void
    {
        $this->assertInstanceOf(
            SqliteDatabaseRepository::class,
            DatabaseRepositoryFactory::make(DB::connection('sqlite'))
        );
    }

    public function test_it_hands_the_connection_to_the_repository_it_creates(): void
    {
        $connection = $this->connectionFor('mysql');

        $this->assertSame($connection, DatabaseRepositoryFactory::make($connection)->db);
    }

    public function test_it_rejects_a_driver_it_does_not_support(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Database driver [pgsql] is not supported.');

        DatabaseRepositoryFactory::make($this->connectionFor('pgsql'));
    }

    public function test_the_repository_it_creates_reports_the_connection_database_name(): void
    {
        $repository = DatabaseRepositoryFactory::make($this->connectionFor('mysql'));

        $this->assertSame('dibi_testing', $repository->getName());
    }
}
