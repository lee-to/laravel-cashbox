<?php

namespace Leeto\CashBox\Providers;

use Leeto\CashBox\CashBox;
use Illuminate\Support\ServiceProvider;
use Leeto\CashBox\commands\InstallCommand;

/**
 * Class CashBoxServiceProvider
 * @package Leeto\CashBox\Providers
 */
class CashBoxServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $namespace = "cashbox";

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('payment', function ($app) {
            return (new CashBox())->payment();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $path = __DIR__ . "/..";

        /* Config */
        $this->publishes([
            $path . '/config/' . $this->namespace . '.php' => config_path($this->namespace . '.php'),
        ]);

        /* Migrations */
        $this->loadMigrationsFrom($path . '/database/migrations');

        /* Commands */
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }
}
