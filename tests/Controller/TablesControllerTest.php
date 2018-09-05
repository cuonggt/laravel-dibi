<?php

namespace Cuonggt\Dibi\Tests\Controller;

class TablesControllerTest extends AbstractControllerTest
{
    public function test_it_can_listing_all_tables()
    {
        $response = $this->get('/dibi/api/tables');

        $response->assertJson([
            ['name' => 'migrations'],
            ['name' => 'package_tests'],
        ]);
    }

    public function test_it_returns_a_table_by_name()
    {
        $response = $this->get('/dibi/api/tables/package_tests');

        $response->assertJson([
            'name' => 'package_tests',
        ]);
    }
}
