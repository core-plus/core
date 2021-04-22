<?php

namespace Core\FileStorage\Validators\Rulers;

use Core\AppInterface;
use Illuminate\Contracts\Validation\Factory as ValidationFactoryContract;

class ValidatorRulesRegister
{
    /**
     * The app.
     *
     * @var AppInterface
     */
    protected $app;
    /**
     * The app validator.
     *
     * @var ValidationFactoryContract
     */
    protected $validator;
    /**
     * The rulers.
     *
     * @var array
     */
    protected $rules
        = [
            'file_storage' => FileStorageRuler::class,
        ];

    /**
     * Create the validator rules register instance.
     *
     * @param  AppInterface  $app
     * @param  ValidationFactoryContract  $validator
     */
    public function __construct(
        AppInterface $app,
        ValidationFactoryContract $validator
    ) {
        $this->app = $app;
        $this->validator = $validator;
    }

    /**
     * The reguster.
     *
     * @return void
     */
    public function register()
    : void
    {
        $app = $this->app;
        foreach ($this->rules as $ruleName => $rulerClassname) {
            $this->validator->extend($ruleName,
                function (...$params) use ($app, $rulerClassname): bool {
                    return $app->make($rulerClassname)->handle($params);
                });
        }
    }
}
