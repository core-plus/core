<?php

namespace Core\FileStorage\Http;

use Illuminate\Contracts\Routing\Registrar as RegistrarContract;

class MakeRoutes
{
    /**
     * The router instance.
     * @var \Illuminate\Contracts\Routing\Registrar
     */
    protected $router;

    /**
     * Create the maker instance.
     * @param \Illuminate\Contracts\Routing\Registrar $router
     */
    public function __construct(RegistrarContract $router)
    {
        $this->router = $router;
    }

    /**
     * The routes resister.
     * @return void
     */
    public function register(): void
    {
        $this->registerLocalFilesystemRoutes();
        $this->registerChannelCallbackRoutes();
        $this->registerCreateTaskRoutes();
    }

    /**
     * Register local filesystem routes.
     * @return void
     */
    protected function registerLocalFilesystemRoutes(): void
    {
        $this->router->group(['prefix' => 'storage'], function (RegistrarContract $router) {
            $router
                ->get('{channel}:{path}', Controllers\Local::class.'@get')
                ->name('storage:get');
            $router
                ->put('{channel}:{path}', Controllers\Local::class.'@put')
                ->name('storage:local-put');
        });
    }

    /**
     * Register channel callback routes.
     * @return void
     */
    protected function registerChannelCallbackRoutes(): void
    {
        $this->router->group(['prefix' => 'api/v2'], function (RegistrarContract $router) {
            $router
                ->post('storage/{channel}:{path}', Controllers\Callback::class)
                ->name('storage:callback');
        });
    }

    /**
     * Register create a upload task routes.
     * @return void
     */
    protected function registerCreateTaskRoutes(): void
    {
        $this->router->group(['prefix' => 'api/v2'], function (RegistrarContract $router) {
            $router
                ->post('storage', Controllers\CreateTask::class)
                ->name('storage:create-task');
        });
    }
}
