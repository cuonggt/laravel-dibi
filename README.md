# Dibi

<p align="center">
<a href="https://github.com/cuonggt/laravel-dibi/actions"><img src="https://github.com/cuonggt/laravel-dibi/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/cuonggt/laravel-dibi"><img src="https://poser.pugx.org/cuonggt/laravel-dibi/license.svg" alt="License"></a>
</p>

![screenshot](https://user-images.githubusercontent.com/8156596/115648495-b9360080-a34f-11eb-86c5-4da253046f86.png)

Laravel Dibi is an elegant GUI database management tool for your Laravel applications. It provides a quick access for browsing your database on local/dev server without installing any other applications.

## Installation

The package can be installed in your project using Composer:

```bash
composer require cuonggt/laravel-dibi
```

After installing Dibi, publish its assets using the `dibi:install` Artisan command:

```bash
php artisan dibi:install
```

Currently, Dibi only supports MySQL. I hope other DB engines like SQL Server, PostgreSQL, SQLite, etc will be supported in near future.

Dibi use connection config name `mysql` by default to connect. If you want to use other connection config name, specify it in the `.env` file:

```
DIBI_DB_CONNECTION = your_mysql
```

### Dashboard Authorization

Dibi exposes a dashboard at the `/dibi` URI. Within your `app/Providers/DibiServiceProvider.php` file, there is a `gate` method that controls access to the Dibi dashboard. By default, all visitors are restricted. You should modify this gate as needed to grant access to your Dibi dashboard:

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
    Gate::define('viewDibi', function ($user = null) {
        return in_array(optional($user)->email, [
            'cuong@gtk.vn',
        ]);
    });
}
```

### Upgrading Dibi

When upgrading to a new version of Dibi, you should re-publish Dibi's assets:

```bash
php artisan dibi:publish
```

To keep the assets up-to-date and avoid issues in future updates, you may add the `dibi:publish` command to the `post-update-cmd` scripts in your application's `composer.json` file:

```json
{
    "scripts": {
        "post-update-cmd": [
            "@php artisan dibi:publish --ansi"
        ]
    }
}
```

### Customizing Middleware

If needed, you can customize the middleware stack used by Dibi routes by updating your `config/dibi.php` file. If you have not published Dibi's confiugration file, you may do so using the `vendor:publish` Artisan command:

```
php artisan vendor:publish --tag=dibi-config
```

Once the configuration file has been published, you may edit Dibi's middleware by tweaking the `middleware` configuration option within this file:

```php
/*
|--------------------------------------------------------------------------
| Dibi Route Middleware
|--------------------------------------------------------------------------
|
| These middleware will be assigned to every Dibi route - giving you
| the chance to add your own middleware to this list or change any of
| the existing middleware. Or, you can simply stick with this list.
|
*/

'middleware' => [
    'web',
    EnsureUserIsAuthorized::class,
    EnsureUpToDateAssets::class
],
```

### Specific Environments Only Installation

If you plan to only use Dibi to assist your specific environments only development, you may install Dibi using the `--dev` flag:

```bash
composer require cuonggt/laravel-dibi --dev
php artisan dibi:install
```

After running `dibi:install`, you should remove the `DibiServiceProvider` service provider registration from your application's `config/app.php` configuration file. Instead, manually register Dibi's service providers in the `register` method of your `App\Providers\AppServiceProvider` class. We will ensure the current environment is one of specific environments before registering the providers:

```php
/**
 * Register any application services.
 *
 * @return void
 */
public function register()
{
    if ($this->app->environment('local', 'develop', 'staging')) {
        $this->app->register(\Cuonggt\Dibi\DibiServiceProvider::class);
        $this->app->register(DibiServiceProvider::class);
    }
}
```

Finally, you should also prevent the Dibi package from being auto-discovered by adding the following to your `composer.json` file:

```json
"extra": {
    "laravel": {
        "dont-discover": [
            "cuonggt/laravel-dibi"
        ]
    }
},
```

## License

Laravel Dibi is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
