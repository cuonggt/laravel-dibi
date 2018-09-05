<?php

namespace Cuonggt\Dibi\Tests\Controller;

class TablesControllerTest extends AbstractControllerTest
{
    public function test_ok()
    {
        $response = $this->get('/dibi/api/tables');

        $response->assertJson([
            ['name' => 'migrations'],
            ['name' => 'users'],
        ]);
    }
}
