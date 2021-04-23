<?php

namespace Core\Packages\Wallet\TargetTypes;

use DB;
use function Core\setting;
use Core\Packages\Wallet\Wallet;
use Core\Packages\Currency\Processes\Recharge;

class TransformCurrencyTarget extends Target
{
    const ORDER_TITLE = '积分转换';

    public function handle(): bool
    {
        if (! $this->order->hasWait()) {
            return true;
        }

        $this->initWallet();

        $ratio = setting('currency', 'settings')['recharge-ratio'] ?? 1;
        $order = $this->order->getOrderModel();
        $currency_amount = $order->amount * $ratio;

        $orderHandle = function () use ($order, $currency_amount) {
            // 钱包订单部分
            $body = sprintf('充值%s积分', $currency_amount);
            $order->body = $body;
            $order->state = 1;

            $order->save();
            $this->wallet->decrement($order->amount);

            // 积分订单部分
            $currency_order = app(Recharge::class)->createOrder($order->owner_id, $currency_amount);
            $currency_order->state = 1;
            $currency_order->save();
            $currency_order->user->currency->increment('sum', $currency_amount);

            return true;
        };

        return DB::transaction($orderHandle);
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
