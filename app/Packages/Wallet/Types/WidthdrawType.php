<?php

namespace Core\Packages\Wallet\Types;

use Core\Packages\Wallet\Order;
use Core\Models\User as UserModel;
use Core\Models\WalletOrder as WalletOrderModel;
use Core\Packages\Wallet\TargetTypes\WidthdrawTarget;

class WidthdrawType extends Type
{
    /**
     * 提现.
     *
     * @param int|UserModel $owner
     * @param int $amount
     * @param string $type
     * @param string $account
     * @return boolen
     * @author GEO <dev@kaifa.me>
     */
    public function widthdraw($owner, $amount, $type, $account): bool
    {
        $owner = $this->checkUserId($owner);
        $order = $this->createOrder($owner, $amount);

        return $order->autoComplete($type, $account);
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
        $order->target_type = Order::TARGET_TYPE_WITHDRAW;
        $order->target_id = 0;
        $order->title = WidthdrawTarget::ORDER_TITLE;
        $order->type = Order::TYPE_EXPENSES;
        $order->amount = $amount;
        $order->state = Order::STATE_WAIT;

        return new Order($order);
    }
}
