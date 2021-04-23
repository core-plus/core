<?php

namespace Core\Packages\Wallet;

use Illuminate\Support\Manager;
use Core\Packages\Wallet\TargetTypes\Target;

class TargetTypeManager extends Manager
{
    protected $order;

    /**
     * Set the manager order.
     *
     * @param \Core\Packages\Wallet\Order $order
     * @author GEO <dev@kaifa.me>
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the order target type driver.
     *
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    public function getDefaultDriver()
    {
        return $this->order->getOrderModel()->target_type;
    }

    /**
     * Create user target type driver.
     *
     * @return \Core\Packages\TargetTypes\Target
     * @author GEO <dev@kaifa.me>
     */
    protected function createUserDriver(): Target
    {
        $driver = $this->app->make(TargetTypes\UserTarget::class);
        $driver->setOrder($this->order);

        return $driver;
    }

    /**
     * Create widthdraw target type driver.
     *
     * @return \Core\Packages\TargetTypes\Target
     * @author GEO <dev@kaifa.me>
     */
    protected function createWidthdrawDriver(): Target
    {
        $driver = $this->app->make(TargetTypes\WidthdrawTarget::class);
        $driver->setOrder($this->order);

        return $driver;
    }

    /**
     * Create Rew target type driver.
     *
     * @return \Core\Packages\TargetTypes\Target
     * @author hh <915664508@qq.com>
     */
    protected function createRewardDriver(): Target
    {
        $driver = $this->app->make(TargetTypes\RewardTarget::class);
        $driver->setOrder($this->order);

        return $driver;
    }

    /**
     * Create Charge target type driver.
     *
     * @return \Core\Packages\TargetTypes\Target
     * @author GEO <dev@kaifa.me>
     */
    protected function createRechargePingPPDriver(): Target
    {
        $driver = $this->app->make(TargetTypes\RechargeTarget::class);
        $driver->setOrder($this->order);

        return $driver;
    }

    /**
     * Create Transform target type driver.
     *
     * @return \Core\Packages\TargetTypes\Target
     * @author GEO <dev@kaifa.me>
     */
    protected function createTransformDriver(): Target
    {
        $driver = $this->app->make(TargetTypes\TransformCurrencyTarget::class);
        $driver->setOrder($this->order);

        return $driver;
    }
}
