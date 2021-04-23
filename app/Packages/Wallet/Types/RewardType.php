<?php

namespace Core\Packages\Wallet\Types;

use Core\Models\User;
use Core\Packages\Wallet\Order;
use Core\Models\WalletOrder as WalletOrderModel;
use Core\Packages\Wallet\TargetTypes\RewardTarget;

class RewardType extends Type
{
    /**
     * @param $owner
     * @param $target
     * @param int $amount
     * @param $extra
     * @return bool
     */
    public function reward($data, $type = 'auto')
    {
        $rewardOrder = $data['order'];

        $owner = $this->resolveGetUserId($rewardOrder['user']);
        $target = $this->resolveGetUserId($rewardOrder['target']);

        $order = $this->createOrder($owner, $target, $rewardOrder['amount'], $rewardOrder['user_order_body']);

        if ($type == 'manual') {
            $target = new RewardTarget();
            $target->setOrder($order);
            $target->handle($data);

            return $target->getTargetOrder();
        } else {
            return $order->autoComplete($data);
        }
    }

    /**
     * Get user id.
     *
     * @param $user
     * @return int
     * @author hh <915664508@qq.com>
     */
    public function resolveGetUserId($user): int
    {
        if ($user instanceof User) {
            $user = $user->id;
        }

        return $user;
    }

    /**
     * Create a order.
     *
     * @param int $owner
     * @param int $target
     * @param int $amount
     * @return \Core\Packages\Wallet\Order
     * @author hh <915664508@qq.com>
     */
    public function createOrder(int $owner, int $target, int $amount, string $body): Order
    {
        return new Order($this->createOrderModel($owner, $target, $amount, $body));
    }

    /**
     * Create order model.
     *
     * @param int $owner
     * @param int $target
     * @param int $amount
     * @return \Ziyi\Plus\Models\WalletOrder
     * @author hh <915664508@qq.com>
     */
    public function createOrderModel(int $owner, int $target, int $amount, string $body): WalletOrderModel
    {
        $order = new WalletOrderModel();
        $order->owner_id = $owner;
        $order->target_type = Order::TARGET_TYPE_REWARD;
        $order->target_id = $target;
        $order->title = RewardTarget::ORDER_TITLE;
        $order->type = Order::TYPE_EXPENSES;
        $order->amount = $amount;
        $order->body = $body;
        $order->state = Order::STATE_WAIT;

        return $order;
    }
}
