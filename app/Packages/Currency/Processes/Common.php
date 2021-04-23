<?php

namespace Core\Packages\Currency\Processes;

use Core\Packages\Currency\Order;
use Core\Packages\Currency\Process;
use Core\Models\CurrencyOrder as CurrencyOrderModel;

class Common extends Process
{
    /**
     * 创建默认积分流水订单.
     *
     * @param  int  $owner_id
     * @param  int  $amount
     * @param  int  $type
     * @param  string  $title
     * @param  string  $body
     *
     * @return CurrencyOrderModel
     * @throws \Exception
     * @author GEO <dev@kaifa.me>
     */
    public function createOrder(
        int $owner_id,
        $amount,
        int $type,
        string $title,
        string $body
    )
    : CurrencyOrderModel {
        $user = $this->checkUser($owner_id);

        $order = new CurrencyOrderModel();
        $order->owner_id = $user->id;
        $order->title = $title;
        $order->body = $body;
        $order->type = $type;
        $order->currency = $this->currency_type->get('id');
        $order->target_type = Order::TARGET_TYPE_COMMON;
        $order->target_id = 0;
        $order->amount = intval($amount);

        return $order;
    }
}
