<?php

namespace Core\Packages\Wallet\Types;

use Core\Packages\Wallet\Order;
use Core\Models\User as UserModel;
use Core\Models\WalletOrder as WalletOrderModel;
use Core\Packages\Wallet\TargetTypes\TransformCurrencyTarget;

class TransformCurrencyType extends Type
{
    /**
     * 钱包兑换积分.
     *
     * @param $owner
     * @param int $amount
     * @return bool
     * @author GEO <dev@kaifa.me>
     */
    public function transform($owner, int $amount): bool
    {
        $owner = $this->checkUserId($owner);
        $order = $this->createOrder($owner, $amount);

        return $order->autoComplete();
    }

    /**
     * Create Order.
     *
     * @param int $owner
     * @param int $amount
     * @return Core\Models\WalletOrderModel
     * @author GEO <dev@kaifa.me>
     */
    protected function createOrder(int $owner, int $amount): Order
    {
        $order = new WalletOrderModel();
        $order->owner_id = $owner;
        $order->target_type = Order::TARGET_TYPE_TRANSFORM;
        $order->target_id = 0;
        $order->title = TransformCurrencyTarget::ORDER_TITLE;
        $order->body = '兑换积分';
        $order->type = Order::TYPE_EXPENSES;
        $order->amount = $amount;
        $order->state = Order::STATE_WAIT;

        return new Order($order);
    }

    /**
     * Check user.
     *
     * @param int|UserModel $user
     * @return int
     * @author GEO <dev@kaifa.me>
     */
    protected function checkUserId($user): int
    {
        if ($user instanceof UserModel) {
            $user = $user->id;
        }

        return (int) $user;
    }
}
