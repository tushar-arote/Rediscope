<?php

namespace Rediscope;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RediscopeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //Rediscope::night();

        $this->registerRoutes();
        $this->registerAssetRoute();

        $this->loadViewsFrom(
            __DIR__ . '/../resources/views', 'rediscope'
        );

        $this->publishes([
            __DIR__ . '/../config/rediscope.php' => config_path('rediscope.php'),
        ], 'rediscope-config');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/rediscope.php', 'rediscope'
        );
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    private function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    /**
     * Register a route to serve the package's compiled frontend assets
     * directly, so consumers don't need a vendor:publish step.
     *
     * @return void
     */
    private function registerAssetRoute()
    {
        Route::get('vendor/rediscope/{path}', [
            \Rediscope\Http\Controllers\AssetController::class, 'index',
        ])->where('path', '.*')->name('rediscope.asset');
    }

    /**
     * Get the Telescope route group configuration array.
     *
     * @return array
     */
    private function routeConfiguration()
    {
        return [
            'namespace' => 'Rediscope\Http\Controllers',
            'prefix' => config('rediscope.path'),
            //'middleware' => config('rediscope.middleware'),
        ];
    }
}
