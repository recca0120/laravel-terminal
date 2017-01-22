let mix = require('laravel-mix').mix;
let path = require('path');

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
        $: path.normalize(`${__dirname}/node_modules/jquery/src/jquery.js`),
        jquery: path.normalize(`${__dirname}/node_modules/jquery/src/jquery.js`),
        jQuery: path.normalize(`${__dirname}/node_modules/jquery/src/jquery.js`),
        'window.jQuery': path.normalize(`${__dirname}/node_modules/jquery/src/jquery.js`),
        'jquery-ui/sortable': `jquery-ui/ui/widgets/sortable.js`,
        moment: path.normalize(`${__dirname}/node_modules/moment/moment.js`),
      }
    }
  })
  .js('resources/assets/js/terminal.js', 'public/js')
  .sass('resources/assets/sass/terminal.scss', 'public/css');
