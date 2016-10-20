const gulpUtil = require('gulp-util');
gulpUtil.env.production = true;
const path = require('path');
const elixir = require('laravel-elixir');

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

elixir((mix) => {
    let webpackConfig = {
        resolve: {
            alias: {
                jquery: 'jquery/dist/jquery.js',
                $: 'jquery/dist/jquery.js',
                'jquery-ui/sortable': 'jquery-ui/ui/widgets/sortable.js',
                moment: 'moment/moment.js'
            }
        }
    };
    let publicPath = elixir.config.publicPath;
    let cssOutputFolder = elixir.config.get('public.css.outputFolder');
    let jsOutputFolder = elixir.config.get('public.js.outputFolder');
    let fontOutputFolder = `${publicPath}/fonts`;
    let imgOutputFolder = `${publicPath}/img`;
    mix
        .sass([
            'bundle.scss'
        ], `${cssOutputFolder}/bundle.css`)
        .webpack('bundle.js', jsOutputFolder, null, webpackConfig)
        .browserSync({
            files: [
                'src/**/*.php',
                'resources/views/**/*.php',
                `${cssOutputFolder}/**/*.css`,
                `${jsOutputFolder}/**/*.js`,
            ],
            proxy: {
                target: '127.0.0.1'
            },
            startPath: '/project/terminal'
        })
        .copy(publicPath, path.normalize(`${__dirname}/../../../../vendor/terminal`))
        .phpUnit();
});
