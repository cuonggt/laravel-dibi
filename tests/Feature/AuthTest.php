<?php

namespace Cuonggt\Dibi\Tests\Feature;

use Cuonggt\Dibi\Dibi;
use Cuonggt\Dibi\Tests\IntegrationTest;
use Cuonggt\Dibi\Http\Middleware\Authenticate;

class AuthTest extends IntegrationTest
{
    public function test_authentication_callback_works()
    {
        $this->assertTrue(Dibi::check('cuong'));

        Dibi::auth(function ($request) {
            return $request === 'cuong';
        });

        $this->assertTrue(Dibi::check('cuong'));
        $this->assertFalse(Dibi::check('manh'));
        $this->assertFalse(Dibi::check(null));
    }

    public function test_authentication_middleware_can_pass()
    {
        Dibi::auth(function () {
            return true;
        });

        $middleware = new Authenticate;

        $response = $middleware->handle(
            new class {
            },
            function ($value) {
                return 'response';
            }
        );

        $this->assertSame('response', $response);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function test_authentication_middleware_responds_with_403_on_failure()
    {
        Dibi::auth(function () {
            return false;
        });

        $middleware = new Authenticate;

        $middleware->handle(
            new class {
            },
            function ($value) {
                return 'response';
            }
        );
    }
}
