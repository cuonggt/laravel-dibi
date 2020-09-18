<?php

namespace Cuonggt\Dibi\Tests\Controller;

use Cuonggt\Dibi\Tests\FeatureTestCase;
use Cuonggt\Dibi\Http\Middleware\Authorize;
use Orchestra\Testbench\Http\Middleware\VerifyCsrfToken;

abstract class AbstractControllerTest extends FeatureTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([Authorize::class, VerifyCsrfToken::class]);
    }
}
