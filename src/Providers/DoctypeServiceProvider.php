<?php

namespace Doctypes\Providers;

use Illuminate\Support\ServiceProvider;
use Doctypes\Console\Commands\InstallDoctypeCommand;
use Doctypes\Services\DoctypeGeneratorService;

class DoctypeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register the generator service
        $this->app->singleton(DoctypeGeneratorService::class);

        // Register config
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/doctypes.php',
            'doctypes'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallDoctypeCommand::class,
            ]);

            // Publish config
            $this->publishes([
                __DIR__ . '/../../config/doctypes.php' => $this->app->configPath('doctypes.php'),
            ], 'doctypes-config');

            // Publish migrations
            $this->publishes([
                __DIR__ . '/../../database/migrations' => $this->app->databasePath('migrations'),
            ], 'doctypes-migrations');

            // Publish views
            $this->publishes([
                __DIR__ . '/../../resource/js/features/doctypes' => $this->app->resourcePath('js/features/doctypes'),
            ], 'doctypes-views');
        }
    }
}