<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Cuonggt\Dibi\Dibi;
use Cuonggt\Dibi\DibiApplicationServiceProvider;

class DibiServiceProvider extends DibiApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // Dibi::useDatabaseConnectionName('custom_mysql');
    }

    /**
     * Register the Dibi gate.
     *
     * This gate determines who can access Dibi in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewDibi', function ($user = null) {
            return in_array(optional($user)->email, [
                //
            ]);
        });
    }
}
