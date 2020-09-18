<?php

namespace Cuonggt\Dibi;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class DibiApplicationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->authorization();
    }

    /**
     * Configure the Dibi authorization services.
     *
     * @return void
     */
    protected function authorization()
    {
        $this->gate();

        Dibi::auth(function ($request) {
            return app()->environment('local') ||
                   Gate::check('viewDibi', [$request->user()]);
        });
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
        Gate::define('viewDibi', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
