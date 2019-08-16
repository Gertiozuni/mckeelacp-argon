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

var pluginPath = 'resources/plugins/'

mix.js('resources/js/app.js', 'public/js')
	.sass('resources/sass/my.scss', 'public/css');

mix.js( pluginPath + 'lou-multi-select/js/jquery.multi-select.js', 'public/plugins/multi-select/multi-select.js')
	.sass( pluginPath + 'lou-multi-select/scss/multi-select.scss', 'public/plugins/multi-select/');

