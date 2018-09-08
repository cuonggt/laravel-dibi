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

    public function test_it_update_field_value_for_the_given_row()
    {
        DB::table('package_tests')->insert(
            ['name' => 'foo']
        );

        $response = $this->put('/dibi/api/tables/package_tests/rows', [
            'row' => [
                'id' => 1,
                'name' => 'foo',
            ],
            'column' => [
                'field' => 'name',
            ],
            'value' => 'foo (updated)',
        ]);

        $this->assertEquals($response->content(), 1);

        $this->assertDatabaseHas('package_tests', [
            'id' => 1,
            'name' => 'foo (updated)',
        ]);
    }

    public function test_it_add_a_row_to_the_given_table()
    {
        $response = $this->post('/dibi/api/tables/package_tests/rows', [
            'row' => [
                'name' => 'foo',
            ],
        ]);

        $this->assertDatabaseHas('package_tests', [
            'id' => 1,
            'name' => 'foo',
        ]);
    }

    public function test_it_delete_a_row_from_the_given_table()
    {
        DB::table('package_tests')->insert(
            ['name' => 'foo']
        );

        $response = $this->delete('/dibi/api/tables/package_tests/rows', [
            'row' => [
                'id' => 1,
                'name' => 'foo',
            ],
        ]);

        $this->assertDatabaseMissing('package_tests', [
            'id' => 1,
            'name' => 'foo',
        ]);
    }
}
