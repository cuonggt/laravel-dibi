const mix = require('laravel-mix');

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
    .js('resources/js/app.js', 'public/js')
    .extract([
        'vue',
        'vue-router',
        'lodash',
        'axios',
        'ant-design-vue',
    ])
    .setPublicPath('public')
    .copy('public', '../../Sites/dibi-app/public/vendor/dibi')
    .webpackConfig({
        resolve: {
            alias: {
                '@': path.resolve(__dirname, 'resources/js/'),
            },
        },
    })
    .version();
