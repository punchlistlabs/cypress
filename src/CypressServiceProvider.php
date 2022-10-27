<?php

namespace Laracasts\Cypress;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CypressServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (! app()->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__ . '/../config/cypress.php', 'cypress');
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $excludedEnvironments = config('cypress.exclude');

        if (is_string($excludedEnvironments)) {
            $excludedEnvironments = explode(',', $excludedEnvironments);
        }
        $excludedEnvironments[] = 'production';
        $excludedEnvironments = array_unique($excludedEnvironments);

        if ($this->app->environment($excludedEnvironments)) {
            return;
        }

        $this->addRoutes();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/routes/cypress.php' => base_path('routes/cypress.php'),
            ]);

            $this->commands([
                CypressBoilerplateCommand::class,
            ]);
        }
    }

    /**
     * Add the Cypress routes.
     *
     * @return void
     */
    protected function addRoutes()
    {
        Route::namespace('')
            ->middleware('web')
            ->group(__DIR__.'/routes/cypress.php');
    }
}
