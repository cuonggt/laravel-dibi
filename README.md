# Dibi

<p align="center">
<a href="https://github.com/cuonggt/laravel-dibi/actions"><img src="https://github.com/cuonggt/laravel-dibi/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/cuonggt/laravel-dibi"><img src="https://poser.pugx.org/cuonggt/laravel-dibi/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/cuonggt/laravel-dibi"><img src="https://poser.pugx.org/cuonggt/laravel-dibi/license.svg" alt="License"></a>
</p>

Laravel Dibi is an elegant GUI database management tool for your Laravel applications. It provides a quick access for browsing your database on local/dev server without installing any other applications.

![screenshot](https://user-images.githubusercontent.com/8156596/93571550-d15f2f00-f9be-11ea-8ec5-d9abbe5a94cc.png)

## Installation

You may use Composer to install Dibi into your Laravel project:

    composer require cuonggt/laravel-dibi    

After installing Dibi, publish its assets using the `dibi:install` Artisan command:

    php artisan dibi:install
   
### Installing Only In Specific Environments

If you plan to only use Dibi to assist your local development, you may install Dibi using the `--dev` flag:

    composer require cuonggt/laravel-dibi --dev

After running `dibi:install`, you should remove the `DibiServiceProvider` service provider registration from your `app` configuration file. Instead, manually register the service provider in the `register` method of your `AppServiceProvider`:

```php
/**
 * Register any application services.
 *
 * @return void
 */
public function register()
{
    if ($this->app->isLocal()) {
        $this->app->register(\Cuonggt\Dibi\DibiServiceProvider::class);
        $this->app->register(DibiServiceProvider::class);
    }
}
```

You should also prevent the Dibi package from being auto-discovered by adding the following to your `composer.json` file:

    "extra": {
        "laravel": {
            "dont-discover": [
                "cuonggt/laravel-dibi"
            ]
        }
    },

### Configuration

After publishing Dibi's assets, its primary configuration file will be located at `config/dibi.php`. This configuration file allows you to configure your watcher options and each configuration option includes a description of its purpose, so be sure to thoroughly explore this file.

If desired, you may disable Dibi's data collection entirely using the `enabled` configuration option:

    'enabled' => env('DIBI_ENABLED', true),

### Dashboard Authorization

Dibi exposes a dashboard at `/dibi`. By default, you will only be able to access this dashboard in the `local` environment. Within your `app/Providers/DibiServiceProvider.php` file, there is a `gate` method. This authorization gate controls access to Dibi in **non-local** environments. You are free to modify this gate as needed to restrict access to your Dibi installation:

```php
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
            'cuong@gtk.vn',
        ]);
    });
}
```

> You should ensure you change your `APP_ENV` environment variable to `production` in your production environment. Otherwise, your Dibi installation will be publicly available.

## License

Laravel Dibi is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
