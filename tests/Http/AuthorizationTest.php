<?php

namespace Cuonggt\Dibi\Tests\Http;

use Cuonggt\Dibi\Dibi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Cuonggt\Dibi\Tests\FeatureTestCase;
use Illuminate\Contracts\Auth\Authenticatable;
use Cuonggt\Dibi\DibiApplicationServiceProvider;
use Orchestra\Testbench\Http\Middleware\VerifyCsrfToken;

class AuthorizationTest extends FeatureTestCase
{
    protected function getPackageProviders($app)
    {
        return array_merge(
            parent::getPackageProviders($app),
            [DibiApplicationServiceProvider::class]
        );
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([VerifyCsrfToken::class]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Dibi::auth(null);
    }

    public function test_clean_dibi_installation_denies_access_by_default()
    {
        $this->get('/dibi/api/tables')
            ->assertStatus(403);
    }

    public function test_clean_dibi_installation_denies_access_by_default_for_any_auth_user()
    {
        $this->actingAs(new Authenticated);

        $this->get('/dibi/api/tables')
            ->assertStatus(403);
    }

    public function test_guests_gets_unauthorized_by_gate()
    {
        Dibi::auth(function (Request $request) {
            return Gate::check('viewDibi', [$request->user()]);
        });

        Gate::define('viewDibi', function ($user) {
            return true;
        });

        $this->get('/dibi/api/tables')
            ->assertStatus(403);
    }

    public function test_authenticated_user_gets_authorized_by_gate()
    {
        $this->actingAs(new Authenticated);

        Dibi::auth(function (Request $request) {
            return Gate::check('viewDibi', [$request->user()]);
        });

        Gate::define('viewDibi', function (Authenticatable $user) {
            return $user->getAuthIdentifier() === 'dibi-test';
        });

        $this->get('/dibi/api/tables')
            ->assertStatus(200);
    }

    public function test_guests_can_be_authorized()
    {
        Dibi::auth(function (Request $request) {
            return Gate::check('viewDibi', [$request->user()]);
        });

        Gate::define('viewDibi', function (?Authenticatable $user) {
            return true;
        });

        $this->get('/dibi/api/tables')
            ->assertStatus(200);
    }

    public function test_unauthorized_requests()
    {
        Dibi::auth(function () {
            return false;
        });

        $this->get('/dibi/api/tables')
            ->assertStatus(403);
    }

    public function test_authorized_requests()
    {
        Dibi::auth(function () {
            return true;
        });

        $this->get('/dibi/api/tables')
            ->assertSuccessful();
    }
}

class Authenticated implements Authenticatable
{
    public $email;

    public function getAuthIdentifierName()
    {
        return 'Dibi Test';
    }

    public function getAuthIdentifier()
    {
        return 'dibi-test';
    }

    public function getAuthPassword()
    {
        return 'secret';
    }

    public function getRememberToken()
    {
        return 'i-am-dibi';
    }

    public function setRememberToken($value)
    {
        //
    }

    public function getRememberTokenName()
    {
        //
    }
}
