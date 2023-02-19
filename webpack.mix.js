const mix = require('laravel-mix');
const path = require('path');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    .js('resources/js/app.js', 'public')
    .vue({version: 2})
    .setPublicPath('public')
    .postCss('resources/css/app.css', 'public', [
        require('tailwindcss'),
    ])
    .alias({ '@': path.join(__dirname, 'resources/js/') })
    .version();
