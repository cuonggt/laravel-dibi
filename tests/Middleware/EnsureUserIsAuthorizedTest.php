<?php

namespace Cuonggt\Dibi\Tests\Middleware;

use Cuonggt\Dibi\Dibi;
use Cuonggt\Dibi\Http\Middleware\EnsureUserIsAuthorized;
use Cuonggt\Dibi\Tests\FeatureTestCase;
use Illuminate\Http\Request;

class EnsureUserIsAuthorizedTest extends FeatureTestCase
{
    /**
     * The environment the test application booted in.
     *
     * @var string
     */
    protected $originalEnvironment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalEnvironment = $this->app['env'];
    }

    /**
     * Restore the gate and environment so this class cannot leak into others.
     *
     * Setting the environment to "production" is deliberately avoided throughout:
     * it re-enables CSRF verification and makes the migration rollback in teardown
     * prompt for confirmation. Every assertion below relies instead on the test
     * environment already not being "local", which is what the gate checks.
     */
    protected function tearDown(): void
    {
        Dibi::$authUsing = null;

        $this->app['env'] = $this->originalEnvironment;

        parent::tearDown();
    }

    /**
     * Every route Dibi exposes, each of which must pass the gate.
     *
     * @return array<string, array<string>>
     */
    protected function routes()
    {
        return [
            'dashboard' => ['get', '/dibi'],
            'nested dashboard view' => ['get', '/dibi/tables/package_tests'],
            'sql query' => ['post', '/dibi/api/sql-query'],
            'select connection' => ['post', '/dibi/api/select-connection'],
            'table rows' => ['post', '/dibi/api/tables/package_tests/rows/filter'],
        ];
    }

    public function test_the_default_middleware_stack_authorizes_every_request(): void
    {
        $this->assertContains(EnsureUserIsAuthorized::class, config('dibi.middleware'));
    }

    public function test_it_denies_requests_outside_the_local_environment_by_default(): void
    {
        Dibi::$authUsing = null;

        $this->assertNotSame('local', $this->app['env']);

        $this->assertFalse(Dibi::check(Request::create('/dibi')));
    }

    public function test_it_allows_requests_in_the_local_environment_by_default(): void
    {
        Dibi::$authUsing = null;

        $this->app['env'] = 'local';

        $this->assertTrue(Dibi::check(Request::create('/dibi')));
    }

    public function test_a_registered_callback_can_deny_where_the_default_would_allow(): void
    {
        $this->app['env'] = 'local';

        Dibi::auth(function () {
            return false;
        });

        $this->assertFalse(Dibi::check(Request::create('/dibi')));
    }

    public function test_a_registered_callback_can_allow_where_the_default_would_deny(): void
    {
        $this->assertNotSame('local', $this->app['env']);

        Dibi::auth(function () {
            return true;
        });

        $this->assertTrue(Dibi::check(Request::create('/dibi')));
    }

    public function test_it_passes_the_request_to_the_callback(): void
    {
        $received = null;

        Dibi::auth(function ($request) use (&$received) {
            $received = $request;

            return true;
        });

        $request = Request::create('/dibi');

        Dibi::check($request);

        $this->assertSame($request, $received);
    }

    public function test_it_forbids_every_route_when_the_gate_denies(): void
    {
        Dibi::auth(function () {
            return false;
        });

        foreach ($this->routes() as $label => [$method, $uri]) {
            $this->assertSame(
                403,
                $this->{$method}($uri)->getStatusCode(),
                "The {$label} route should be forbidden when the gate denies."
            );
        }
    }

    public function test_it_forbids_every_route_outside_the_local_environment_by_default(): void
    {
        Dibi::$authUsing = null;

        foreach ($this->routes() as $label => [$method, $uri]) {
            $this->assertSame(
                403,
                $this->{$method}($uri)->getStatusCode(),
                "The {$label} route should be forbidden with no gate registered outside the local environment."
            );
        }
    }

    public function test_it_allows_the_dashboard_when_the_gate_passes(): void
    {
        Dibi::auth(function () {
            return true;
        });

        $this->get('/dibi')->assertOk();
    }
}
