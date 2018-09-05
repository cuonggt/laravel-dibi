<?php

namespace Cuonggt\Dibi\Tests\Controller;

use Illuminate\Support\Facades\DB;

class TableRowsControllerTest extends AbstractControllerTest
{
    public function test_it_can_select_from_empty_table()
    {
        $response = $this->get('/dibi/api/tables/package_tests/rows?sorting=id&direction=asc');

        $response->assertJson([
            'total' => 0,
            'count' => 0,
            'data' => [],
        ]);
    }

    public function test_it_returns_list_of_rows_for_the_given_table()
    {
        DB::table('package_tests')->insert(
            ['name' => 'foo']
        );

        $response = $this->get('/dibi/api/tables/package_tests/rows?sorting=id&direction=asc');

        $response->assertJson([
            'total' => 1,
            'count' => 1,
            'data' => [
                [
                    'id' => 1,
                    'name' => 'foo',
                ],
            ],
        ]);
    }
}
