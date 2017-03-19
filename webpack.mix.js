const mix = require('laravel-mix').mix;
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

mix.webpackConfig({
    resolve: {
      alias: {
        $: path.resolve(__dirname, 'node_modules/jquery/dist/jquery.min.js'),
        jQuery: path.resolve(__dirname, 'node_modules/jquery/dist/jquery.min.js'),
        jquery: path.resolve(__dirname, 'node_modules/jquery/dist/jquery.min.js'),
        'window.jQuery': path.resolve(__dirname, 'node_modules/jquery/dist/jquery.min.js'),
      },
    },
    module: {
        noParse: [
            /jquery/i
        ],
    },
  })
  .js(['resources/assets/js/app.js'], 'public/js/terminal.js')
  .sass('resources/assets/sass/app.scss', 'public/css/terminal.css');
