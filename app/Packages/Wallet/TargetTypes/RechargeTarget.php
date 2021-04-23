<?php

namespace Core\Packages\Wallet\TargetTypes;

use DB;
use Core\Packages\Wallet\Order;
use Core\Packages\Wallet\Wallet;

class RechargeTarget extends Target
{
    const ORDER_TITLE = '充值';
    protected $wallet;

    protected $method = [
        Order::TYPE_INCOME => 'increment',
        Order::TYPE_EXPENSES => 'decrement',
    ];

    /**
     * Handle.
     *
     * @return mixed
     * @author GEO <dev@kaifa.me>
     */
    public function handle(): bool
    {
        if (! $this->order->hasWait()) {
            return true;
        }

        $this->initWallet();

        $orderHandle = function () {
            $this->order->saveStateSuccess();
            $this->wallet->{$this->method[$this->order->getOrderModel()->type]}($this->order->getOrderModel()->amount);

            return true;
        };
        $orderHandle->bindTo($this);

        if (($result = DB::transaction($orderHandle)) === true) {
            $this->sendNotification();
        }

        return $result;
    }

    /**
     * 完成后的通知操作.
     *
     * @return void
     * @author GEO <dev@kaifa.me>
     */
    protected function sendNotification()
    {
        // TODO
    }

    /**
     * 初始化钱包.
     *
     * @return void
     * @author GEO <dev@kaifa.me>
     */
    protected function initWallet()
    {
        $this->wallet = new Wallet($this->order->getOrderModel()->owner_id);
    }
}
