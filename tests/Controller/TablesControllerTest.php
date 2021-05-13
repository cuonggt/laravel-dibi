<?php

namespace Cuonggt\Dibi\Tests\Controller;

use Cuonggt\Dibi\Tests\FeatureTestCase;

class TablesControllerTest extends FeatureTestCase
{
    public function test_it_returns_list_of_tables(): void
    {
        $response = $this->get('/dibi/api/tables');

        $response->assertJson([
            ['name' => 'migrations'],
            ['name' => 'package_tests'],
        ]);
    }

    public function test_it_returns_a_table_by_name(): void
    {
        $response = $this->get('/dibi/api/tables/package_tests');

        $response->assertJson([
            'columns' => [
                [
                    'column_name' => 'id',
                    'data_type' => 'int(10) unsigned',
                    'is_nullable' => false,
                    'key' => 'PRI',
                    'column_default' => null,
                    'extra' => 'auto_increment',
                ],
                [
                    'column_name' => 'name',
                    'data_type' => 'varchar(255)',
                    'is_nullable' => false,
                    'key' => '',
                    'column_default' => null,
                    'extra' => '',
                ],
            ],
            'indexes' => [
                [
                    'index_name' => 'PRIMARY',
                    'index_algorithm' => 'BTREE',
                    'is_unique' => true,
                    'column_name' => 'id',
                ],
            ],
            'info' => [
                'name' => 'package_tests',
            ],
        ]);
    }
}
