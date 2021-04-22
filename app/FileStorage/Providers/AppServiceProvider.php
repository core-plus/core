<?php

namespace Core\FileStorage\Providers;

use Core\AppInterface;
use Core\FileStorage\Storage;
use Illuminate\Support\ServiceProvider;
use Core\FileStorage\ChannelManager;
use Core\FileStorage\Http\MakeRoutes;
use Core\FileStorage\StorageInterface;
use Core\FileStorage\Validators\Rulers\ValidatorRulesRegister;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The app register.
     *
     * @return void
     */
    public function register()
    {
        // Register StorageInterface instance.
        $this->app->singleton(StorageInterface::class,
            function (AppInterface $app) {
                $manager = $this->app->make(ChannelManager::class);

                return new Storage($app, $manager);
            });
    }

    /**
     * The app bootstrap handler.
     *
     * @return void
     */
    public function boot()
    {
        // Register routes.
        $this->app->make(MakeRoutes::class)->register();

        // Register validate rules.
        $this->app->make(ValidatorRulesRegister::class)->register();
    }
}
