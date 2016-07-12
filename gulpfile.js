require('gulp-util').env.production = true;
var elixir = require('laravel-elixir');
var path = require('path');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    var publicPath = elixir.config.publicPath;
    var cssOutputFolder = elixir.config.get('public.css.outputFolder');
    var jsOutputFolder = elixir.config.get('public.js.outputFolder');
    var fontOutputFolder = publicPath + '/fonts/';
    var imgOutputFolder = publicPath + '/img/';
    mix
        .sass([
            'bundle.scss'
        ], cssOutputFolder + '/bundle.css')
        .browserify([
            'bundle.js'
        ], jsOutputFolder + '/bundle.js')
        .browserSync({
            files: [
                'src/**/*.php',
                'resources/views/**/*.php',
                cssOutputFolder + '/**/*.css',
                jsOutputFolder + '/**/*.js',
            ],
            proxy: {
                target: '127.0.0.1'
            },
            startPath: '/project/terminal'
        })
        .copy(publicPath, path.normalize(__dirname + '/../../../../vendor/terminal'))
        .phpUnit();
});
