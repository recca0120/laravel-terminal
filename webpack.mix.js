const fs = require('fs');
const mix = require('laravel-mix');
const path = require('path');
const publicPath = path.resolve(__dirname, '../../../../vendor/terminal');

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
    .options({
        autoload: {
            jquery: ['$', 'window.jQuery', 'jQuery', 'CodeMirror'],
        },
        processCssUrls: false,
        publicPath: './',
    })
    .browserSync({
        files: [`${publicPath}/js/app.js`, `${publicPath}/css/app.css`],
    });

mix
    .js('resources/assets/js/app.js', 'public/js/terminal.js')
    .sass('resources/assets/sass/app.scss', 'public/css/terminal.css');

mix.then(() => {
    fs.copyFileSync('public/css/terminal.css', path.resolve(publicPath, 'css/terminal.css'));
    fs.copyFileSync('public/js/terminal.js', path.resolve(publicPath, 'js/terminal.js'));
});
