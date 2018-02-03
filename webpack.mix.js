let mix = require('laravel-mix');

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

mix.styles([
        'public/styleResource/bower_components/bootstrap/dist/css/bootstrap.min.css'

    ], 'public/css/everythingSangitCSS.css')

.combine([
        'public/styleResource/bower_components/jquery/dist/jquery.min.js'

    ], 'public/js/everythingSangitLesuJS.js');
