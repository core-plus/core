<?php

namespace Core\Packages\Wallet\Types;

use Core\Packages\Wallet\Order;
use Core\Models\User as UserModel;
use Core\Models\WalletOrder as WalletOrderModel;
use Core\Packages\Wallet\TargetTypes\UserTarget;

class UserType extends Type
{
    /**
     * User to user transfer.
     *
     * @param int|\Core\Models\User $owner
     * @param int|\Core\Models\User $target
     * @param int $amount
     * @return bool
     * @author GEO <dev@kaifa.me>
     */
    public function transfer($owner, $target, int $amount): bool
    {
        $owner = $this->resolveGetUserId($owner);
        $target = $this->resolveGetUserId($target);
        $order = $this->createOrder($owner, $target, $amount);

        return $order->autoComplete();
    }

    /**
     * Resolve get user id.
     *
     * @param int|\Core\Models\User $user
     * @return int
     * @author GEO <dev@kaifa.me>
     */
    protected function resolveGetUserId($user): int
    {
        if ($user instanceof UserModel) {
            return $user->id;
        }

        return (int) $user;
    }

    /**
     * Create a order.
     *
     * @param int $owner
     * @param int $target
     * @param int $amount
     * @return \Core\Packages\Wallet\Order
     * @author GEO <dev@kaifa.me>
     */
    public function createOrder(int $owner, int $target, int $amount): Order
    {
        return new Order($this->createOrderModel($owner, $target, $amount));
    }

    /**
     * Create order model.
     *
     * @param int $owner
     * @param int $target
     * @param int $amount
     * @return \Ziyi\Plus\Models\WalletOrder
     * @author GEO <dev@kaifa.me>
     */
    public function createOrderModel(int $owner, int $target, int $amount): WalletOrderModel
    {
        $order = new WalletOrderModel();
        $order->owner_id = $owner;
        $order->target_type = Order::TARGET_TYPE_USER;
        $order->target_id = $target;
        $order->title = UserTarget::ORDER_TITLE;
        $order->type = Order::TYPE_EXPENSES;
        $order->amount = $amount;
        $order->state = Order::STATE_WAIT;

        return $order;
    }
}
