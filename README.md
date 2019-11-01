# Dibi

- [Introduction](#introduction)
- [Installation](#installation)
- [Dashboard](#dashboard)
- [Features on going](#features-on-going)
- [License](#license)

## Introduction

![screenshot](https://user-images.githubusercontent.com/8156596/68057862-29ca0700-fcef-11e9-84ff-c3e59026d13d.png)

Laravel Dibi is an elegant GUI database management tool for your Laravel applications. It provides a quick access for browsing your database on local/dev server without installing any other applications. Currently it uses [Ant Design](https://www.antdv.com/) for styling.

## Installation

You may use Composer to install Dibi into your Laravel project:

    composer require cuonggt/laravel-dibi    

After installing Dibi, publish its assets using the `vendor:publish` Artisan command:

    php artisan vendor:publish --provider="Cuonggt\Dibi\DibiServiceProvider"
   
## Dashboard

Dibi exposes a dashboard at `/dibi`.

## Features on going

- Edit inline table rows
- Add/delete row
- Multiple filter conditions
- Support RAW queries
- Use Tailwindcss instead of Ant Design (for more flexible render)
- (...)

## License

Laravel Dibi is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
