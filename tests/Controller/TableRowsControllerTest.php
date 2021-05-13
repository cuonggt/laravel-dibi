<?php

namespace Cuonggt\Dibi\Tests\Controller;

use Cuonggt\Dibi\Tests\FeatureTestCase;
use Illuminate\Support\Facades\DB;

class TableRowsControllerTest extends FeatureTestCase
{
    public function test_it_can_select_from_empty_table(): void
    {
        $response = $this->get('/dibi/api/tables/package_tests/rows?sort_key=id&sort_dir=asc');

        $response->assertJson([
            'data' => [],
            'total' => 0,
        ]);
    }

    public function test_it_returns_list_of_rows_for_the_given_table(): void
    {
        DB::table('package_tests')->insert(
            ['name' => 'foo']
        );

        $response = $this->get('/dibi/api/tables/package_tests/rows?sort_key=id&sort_dir=asc');

        $response->assertJson([
            'total' => 1,
            'data' => [
                [
                    'id' => 1,
                    'name' => 'foo',
                ],
            ],
        ]);
    }
}
