var elixir = require('laravel-elixir');
var path = require('path');
require('laravel-elixir-browserify-official');

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
    mix
        .copy([
        ], elixir.config.get('public.font.outputFolder'))
        .copy([
        ], elixir.config.get('public.img.outputFolder'))
        .copy([
        ], elixir.config.get('public.css.outputFolder'))
        .sass([
            'bundle.scss'
        ], elixir.config.get('public.css.outputFolder') + '/bundle.css')
        .browserify([
            'bundle.js'
        ], elixir.config.get('public.js.outputFolder') + '/bundle.js')
        .browserSync({
            files: [
                'src/**/*.php',
                'resources/views/**/*.php',
                elixir.config.get('public.css.outputFolder')+'/**/*.css',
                elixir.config.get('public.js.outputFolder')+'/**/*.js',
            ],
            proxy: {
                target: '127.0.0.1'
            },
            startPath: '/project/terminal'
        })
        .phpUnit([
            'src/**/*.php',
            'tests/**/*'
        ], path.normalize('vendor/bin/phpunit') + ' --verbose');
});
