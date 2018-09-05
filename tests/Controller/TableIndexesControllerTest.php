<?php

namespace Cuonggt\Dibi\Tests\Controller;

class TableIndexesControllerTest extends AbstractControllerTest
{
    public function test_it_returns_list_of_indexes_for_the_given_table()
    {
        $response = $this->get('/dibi/api/tables/package_tests/indexes');

        $response->assertJson([
            [
                'nonUnique' => 0,
                'keyName' => 'PRIMARY',
                'seqInIndex' => 1,
                'columnName' => 'id',
                'collation' => 'A',
                'cardinality' => 0,
                'subPart' => null,
                'packed' => null,
                'comment' => '',
            ],
        ]);
    }
}
