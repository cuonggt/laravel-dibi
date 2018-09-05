<?php

namespace Cuonggt\Dibi\Tests\Controller;

class TableColumnsControllerTest extends AbstractControllerTest
{
    public function test_it_returns_list_of_columns_for_the_given_table()
    {
        $response = $this->get('/dibi/api/tables/package_tests/columns');

        $response->assertJson([
            [
                'field' => 'id',
                'type' => 'int(10) unsigned',
                'nullable' => false,
                'key' => 'PRI',
                'default' => null,
                'extra' => 'auto_increment',
            ],
            [
                'field' => 'name',
                'type' => 'varchar(255)',
                'nullable' => false,
                'key' => '',
                'default' => null,
                'extra' => '',
            ],
        ]);
    }
}
