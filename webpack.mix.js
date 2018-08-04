const fse = require('fs-extra');
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
    .ts('resources/assets/ts/app.ts', 'public/js/terminal.js')
    .sass('resources/assets/sass/app.scss', 'public/css/terminal.css');

mix.then(() => {
    try {
        fse.copyFileSync(path.resolve(__dirname, 'public/css/terminal.css'), path.resolve(publicPath, 'css/terminal.css'));
        fse.copyFileSync(path.resolve(__dirname, 'public/js/terminal.js'), path.resolve(publicPath, 'js/terminal.js'));
        fse.copyFileSync(path.resolve(__dirname, 'resources/views/index.blade.php'), path.resolve(publicPath, '../../laravel/resources/views/vendor/terminal/index.blade.php'));
    } catch (e) {
        console.error(e);
    }
});
