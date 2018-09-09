<?php

namespace Cuonggt\Dibi\Tests\Feature;

use Cuonggt\Dibi\MysqlDatabase;
use Cuonggt\Dibi\Tests\IntegrationTest;

class MysqlDatabaseTest extends IntegrationTest
{
    public function test_it_returns_list_of_tables()
    {
        $tables = (new MysqlDatabase('testdibi'))->getTables();

        $this->assertEquals($tables[0]->TABLE_NAME, 'migrations');
        $this->assertEquals($tables[1]->TABLE_NAME, 'package_tests');
    }

    public function test_it_returns_a_table_by_name()
    {
        $table = (new MysqlDatabase('testdibi'))->getTableByName('package_tests');

        $this->assertEquals($table->TABLE_NAME, 'package_tests');
    }
}
