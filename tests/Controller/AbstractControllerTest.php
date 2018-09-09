<?php

namespace Cuonggt\Dibi\Tests\Controller;

use Cuonggt\Dibi\Dibi;
use Cuonggt\Dibi\Tests\IntegrationTest;

abstract class AbstractControllerTest extends IntegrationTest
{
    public function setUp()
    {
        parent::setUp();

        $this->app['config']->set('app.key', 'base64:+HB8DVzRb0DPyT4aBv1ixg+htia3ICgsF/r7AIilD5w=');

        Dibi::auth(function () {
            return true;
        });
    }
}
