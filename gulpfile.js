var elixir = require('laravel-elixir');

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
        ], config.get('public.font.outputFolder'))
        .copy([
        ], config.get('public.img.outputFolder'))
        .copy([
        ], config.get('public.css.outputFolder'))
        .sass([
            'bundle.scss'
        ], config.get('public.css.outputFolder') + '/bundle.css')
        .browserify([
            'bundle.js'
        ], config.get('public.js.outputFolder') + '/bundle.js')
        .browserSync({
            files: [
                'src/**/*.php',
                'resources/views/**/*.php',
                config.get('public.css.outputFolder')+'/**/*.css',
                config.get('public.js.outputFolder')+'/**/*.js',
            ],
            proxy: {
                target: '127.0.0.1'
            },
            startPath: '/asf/terminal'
        })
        .phpUnit([
            'tests/**/*'
        ]);
});
