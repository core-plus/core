<?php

namespace Core\Packages\Wallet\TargetTypes;

use Core\Packages\Wallet\Order;

abstract class Target
{
    /**
     * The order service.
     *
     * @var \Core\Packages\Wallet\Order
     */
    protected $order;

    /**
     * Set the order service.
     *
     * @param \Core\Packages\Wallet\Order $order
     * @author GEO <dev@kaifa.me>
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }
}
