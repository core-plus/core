<?php

namespace Core\Packages\Wallet;

use RuntimeException;
use Illuminate\Support\Manager;
use Core\Packages\Wallet\Types\Type;

class TypeManager extends Manager
{
    /**
     * Get default type driver.
     *
     * @return string User type
     * @author GEO <dev@kaifa.me>
     */
    public function getDefaultDriver()
    {
        throw new RuntimeException('The manager not support default driver.');
    }

    /**
     * Create user driver.
     *
     * @return \Core\Packages\Wallet\Types\Type
     * @author GEO <dev@kaifa.me>
     */
    protected function createUserDriver(): Type
    {
        return $this->app->make(Types\UserType::class);
    }

    /**
     * Create widthdraw driver.
     *
     * @return \Core\Packages\Wallet\Types\Type
     * @author GEO <dev@kaifa.me>
     */
    protected function createWidthdrawDriver(): Type
    {
        return $this->app->make(Types\WidthdrawType::class);
    }

    /**
     * Create reward driver.
     *
     * @return \Core\Packages\Wallet\Types\Type
     * @author hh <915664508@qq.com>
     */
    protected function createRewardDriver(): Type
    {
        return $this->app->make(Types\RewardType::class);
    }

    /**
     * Create recharge driver.
     *
     * @return \Core\Packages\Wallet\Types\Type
     * @author GEO <dev@kaifa.me>
     */
    protected function createRechargePingPPDriver(): Type
    {
        return $this->app->make(Types\RechargeType::class);
    }

    /**
     * Create transform driver.
     *
     * @return \Core\Packages\Wallet\Types\Type
     * @author GEO <dev@kaifa.me>
     */
    protected function createTransformDriver(): Type
    {
        return $this->app->make(Types\TransformCurrencyType::class);
    }
}
