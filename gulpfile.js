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
        .copy([
            // "resources/assets/vendor/jquery/dist/jquery.min.js",
            // "resources/assets/vendor/jquery-mousewheel/jquery.mousewheel.min.js",
        ], config.get('public.js.outputFolder'))
        .scripts([
            "jquery/dist/jquery.js",
            "jquery-mousewheel/jquery.mousewheel.js",
            "jquery.terminal/js/jquery.terminal-src.js",
            "jquery.terminal/js/unix_formatting.js",
            "jquery.terminal/js/xml_formatting.js",
            "jsvi/vi.js",
        ], config.get('public.js.outputFolder') + '/plugins.js', 'resources/assets/vendor')
        .scripts([
            "jquery.terminal/js/jquery.terminal-src.js",
            "jquery.terminal/js/unix_formatting.js",
            "jquery.terminal/js/xml_formatting.js",
            "jsvi/vi.js",
        ], config.get('public.js.outputFolder') + '/terminal.js', 'resources/assets/vendor')
        .sass([
            'app.scss'
        ], config.get('public.css.outputFolder') + '/app.css', {
            includePaths: ['resources/assets/vendor']
        })
        .coffee([
            '**/*.coffee'
        ], config.get('public.js.outputFolder') + '/app.js')
        .copy([
            'public'
        ], '../../../../vendor/terminal')
        .phpUnit([
            'tests/**/*'
        ]);
});
