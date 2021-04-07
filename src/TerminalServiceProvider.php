<?php

namespace Recca0120\Terminal;

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class TerminalServiceProvider extends ServiceProvider
{
    /**
     * namespace.
     *
     * @var string
     */
    protected $namespace = 'Recca0120\Terminal\Http\Controllers';

    /**
     * Bootstrap any application services.
     *
     * @param Request $request
     * @param Router $router
     */
    public function boot(Request $request, Router $router)
    {
        $config = $this->app['config']['terminal'];
        if ($this->allowWhiteList($request, $config)) {
            $this->loadViewsFrom(__DIR__.'/../resources/views', 'terminal');
            $this->handleRoutes($router, $config);
        }

        if ($this->app->runningInConsole() === true) {
            $this->handlePublishes();
        }
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/terminal.php', 'terminal');

        $this->app->bind(Application::class, function ($app) {
            $config = $app['config']['terminal'];
            $artisan = new Application($app, $app['events'], $app->version());

            return $artisan->resolveCommands($config['commands']);
        });

        $this->app->bind(Kernel::class, function ($app) {
            $config = $app['config']['terminal'];

            return new Kernel($app[Application::class], array_merge($config, [
                'basePath' => $app->basePath(),
                'environment' => $app->environment(),
                'version' => $app->version(),
                'endpoint' => $app['url']->route(Arr::get($config, 'route.as').'endpoint'),
            ]));
        });
    }

    /**
     * register routes.
     *
     * @param Router $router
     * @param array $config
     */
    protected function handleRoutes(Router $router, $config = [])
    {
        if ($this->app->routesAreCached() === false) {
            $router->group(array_merge([
                'namespace' => $this->namespace,
            ], Arr::get($config, 'route', [])), function () {
                require __DIR__.'/../routes/web.php';
            });
        }
    }

    /**
     * handle publishes.
     */
    protected function handlePublishes()
    {
        $this->publishes([
            __DIR__.'/../config/terminal.php' => config_path('terminal.php'),
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/terminal'),
            __DIR__.'/../public' => public_path('vendor/terminal'),
        ], 'terminal');
    }

    /**
     * @param Request $request
     * @param $config
     * @return bool
     */
    private function allowWhiteList(Request $request, $config)
    {
        return in_array(
                $request->getClientIp(),
                Arr::get($config, 'whitelists', []),
                true
            ) || Arr::get($config, 'enabled');
    }
}
