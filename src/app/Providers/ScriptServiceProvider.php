<?php

namespace VCComponent\Laravel\Script\Providers;

use Illuminate\Support\ServiceProvider;
use VCComponent\Laravel\Script\Repositories\ScriptRepository;
use VCComponent\Laravel\Script\Repositories\ScriptRepositoryEloquent;

class ScriptServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
        $this->publishes([
            __DIR__ . '/../../config/script.php' => config_path('script.php'),
        ], 'config');
    }

    /**
     * Register any package services
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ScriptRepository::class, ScriptRepositoryEloquent::class);

    }
}
