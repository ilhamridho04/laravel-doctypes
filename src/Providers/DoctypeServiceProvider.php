<?php

namespace Doctypes\Providers;

use Illuminate\Support\ServiceProvider;
use Doctypes\Services\DoctypeService;
use Doctypes\Services\DoctypeRenderer;
use Doctypes\Services\DoctypeValidator;

class DoctypeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('doctype', function ($app) {
            return new DoctypeService();
        });

        $this->app->singleton(DoctypeRenderer::class);
        $this->app->singleton(DoctypeValidator::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        // Publish config
        $this->publishes([
            __DIR__ . '/../../config/doctypes.php' => config_path('doctypes.php'),
        ], 'doctypes-config');

        // Publish Vue components (legacy structure)
        $this->publishes([
            __DIR__ . '/../../resources/js' => resource_path('js/doctypes'),
        ], 'doctypes-vue');

        // Publish Vue components (new feature structure)
        $this->publishes([
            __DIR__ . '/../../resource/js/features/doctypes' => resource_path('js/features/doctypes'),
        ], 'doctypes-vue-features');

        // Publish all Vue files together
        $this->publishes([
            __DIR__ . '/../../resources/js' => resource_path('js/doctypes'),
            __DIR__ . '/../../resource/js/features/doctypes' => resource_path('js/features/doctypes'),
        ], 'doctypes-frontend');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../../database/migrations/' => database_path('migrations'),
        ], 'doctypes-migrations');

        // Publish all assets
        $this->publishes([
            __DIR__ . '/../../config/doctypes.php' => config_path('doctypes.php'),
            __DIR__ . '/../../resources/js' => resource_path('js/doctypes'),
            __DIR__ . '/../../resource/js/features/doctypes' => resource_path('js/features/doctypes'),
            __DIR__ . '/../../database/migrations/' => database_path('migrations'),
        ], 'doctypes-all');
    }
}