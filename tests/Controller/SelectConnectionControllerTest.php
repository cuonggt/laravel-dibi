<?php

namespace Cuonggt\Dibi\Tests\Controller;

use Cuonggt\Dibi\Dibi;
use Cuonggt\Dibi\Tests\FeatureTestCase;
use Illuminate\Support\Facades\Cache;

class SelectConnectionControllerTest extends FeatureTestCase
{
    /**
     * Register a second usable connection alongside the default one.
     */
    protected function registerSecondConnection()
    {
        config(['database.connections.sqlite_other' => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]]);

        Dibi::registerDatabaseConnections(['sqlite', 'sqlite_other']);
    }

    public function test_it_remembers_the_selected_connection(): void
    {
        $this->registerSecondConnection();

        $this->post('/dibi/api/select-connection', ['connection' => 'sqlite_other'])->assertOk();

        $this->assertSame('sqlite_other', Cache::get('dibiConnection'));
    }

    public function test_the_selection_changes_which_connection_dibi_uses(): void
    {
        $this->registerSecondConnection();

        $this->assertSame(
            'sqlite',
            $this->get('/dibi')->viewData('dibiScriptVariables')['currentDatabaseConnection']
        );

        $this->post('/dibi/api/select-connection', ['connection' => 'sqlite_other'])->assertOk();

        Dibi::$databaseRepository = null;

        $this->assertSame(
            'sqlite_other',
            $this->get('/dibi')->viewData('dibiScriptVariables')['currentDatabaseConnection']
        );
    }

    public function test_it_accepts_the_only_connection_when_none_were_registered(): void
    {
        $this->assertSame(['sqlite'], Dibi::databaseConnections());

        $this->post('/dibi/api/select-connection', ['connection' => 'sqlite'])->assertOk();

        $this->assertSame('sqlite', Cache::get('dibiConnection'));
    }

    public function test_it_rejects_a_connection_that_was_not_registered(): void
    {
        $this->registerSecondConnection();

        $this->postJson('/dibi/api/select-connection', ['connection' => 'production'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['connection' => 'Invalid connection.']);
    }

    public function test_it_rejects_a_request_with_no_connection(): void
    {
        $this->postJson('/dibi/api/select-connection', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['connection' => 'Invalid connection.']);
    }

    public function test_it_leaves_the_current_selection_alone_when_rejecting(): void
    {
        $this->registerSecondConnection();

        $this->post('/dibi/api/select-connection', ['connection' => 'sqlite_other'])->assertOk();

        $this->postJson('/dibi/api/select-connection', ['connection' => 'production'])->assertStatus(422);

        $this->assertSame(
            'sqlite_other',
            Cache::get('dibiConnection'),
            'A rejected selection should not disturb the one already in effect.'
        );
    }

    public function test_it_rejects_a_connection_that_is_registered_but_never_configured(): void
    {
        Dibi::registerDatabaseConnections(['sqlite', 'never_configured']);

        $this->postJson('/dibi/api/select-connection', ['connection' => 'never_configured'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['connection' => 'Invalid connection.']);

        $this->assertNull(
            Cache::get('dibiConnection'),
            'Selecting it would leave every later request unable to connect.'
        );
    }

    public function test_the_dashboard_survives_a_selection_that_can_no_longer_be_used(): void
    {
        Dibi::registerDatabaseConnections(['sqlite', 'sqlite_other']);

        Cache::put('dibiConnection', 'sqlite_other');

        $variables = $this->get('/dibi')->assertOk()->viewData('dibiScriptVariables');

        $this->assertSame(
            'sqlite',
            $variables['currentDatabaseConnection'],
            'An unusable selection should fall back to the first available connection.'
        );

        $this->assertNull(
            Cache::get('dibiConnection'),
            'The unusable selection should be discarded so it stops being retried.'
        );
    }

    public function test_it_requires_authorization(): void
    {
        Dibi::auth(function () {
            return false;
        });

        $this->post('/dibi/api/select-connection', ['connection' => 'sqlite'])->assertForbidden();

        $this->assertNull(
            Cache::get('dibiConnection'),
            'An unauthorized request should not change the connection.'
        );

        Dibi::auth(function () {
            return true;
        });
    }
}
