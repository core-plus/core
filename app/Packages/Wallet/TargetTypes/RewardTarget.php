<?php

namespace Core\Packages\Wallet\TargetTypes;

use DB;
use Core\Packages\Wallet\Order;
use Core\Packages\Wallet\Wallet;
use Core\Models\WalletOrder as WalletOrderModel;

class RewardTarget extends Target
{
    const ORDER_TITLE = '打赏';
    protected $ownerWallet;     // \Core\Packages\Wallet\Wallet
    protected $targetWallet;    // \Core\Packages\Wallet\Wallet
    protected $targetRewardOrder; // Core\Packages\Wallet\Order

    /**
     * Handle.
     *
     * @return mixed
     * @author hh <915664508@qq.com>
     */
    public function handle($extra): bool
    {
        if (! $this->order->hasWait()) {
            return true;
        }

        $this->initWallet();

        $this->createTargetRewordOrder($extra['order']['target_order_body']);

        $transaction = function () use ($extra) {
            $this->order->saveStateSuccess();
            $this->targetRewardOrder->saveStateSuccess();
            $this->transfer($this->order, $this->ownerWallet);
            $this->transfer($this->targetRewardOrder, $this->targetWallet);

            // 记录打赏记录
            $this->createRewardRecord($extra['reward_resource'], $this->order);

            return true;
        };

        $transaction->bindTo($this);

        if (($result = DB::transaction($transaction)) === true) {
            // 发送消息通知
            $this->sendNotification($extra);
        }

        return $result;
    }

    /**
     * return target Order.
     *
     * @return mixed
     */
    public function getTargetOrder()
    {
        return $this->targetRewardOrder->getOrderModel();
    }

    /**
     * Send notification.
     *
     * @return void
     * @author hh <915664508@qq.com>
     */
    protected function sendNotification($extra)
    {
        //
    }

    /**
     * Init owner and target user wallet.
     *
     * @return void
     * @author hh <915664508@qq.com>
     */
    protected function initWallet()
    {
        // Target user wallet.
        $this->targetWallet = new Wallet(
            $this->order->getOrderModel()->target_id
        );

        // owner wallet.
        $this->ownerWallet = new Wallet(
            $this->order->getOrderModel()->owner_id
        );
    }

    /**
     * Create target user order.
     *
     * @return void
     * @author hh <915664508@qq.com>
     */
    protected function createTargetRewordOrder(string $body)
    {
        $order = new WalletOrderModel();
        $order->owner_id = $this->targetWallet->getWalletModel()->owner_id;
        $order->target_type = Order::TARGET_TYPE_REWARD;
        $order->target_id = $this->ownerWallet->getWalletModel()->owner_id;
        $order->title = static::ORDER_TITLE;
        $order->type = $this->getTargetRewordOrderType();
        $order->amount = $this->order->getOrderModel()->amount;
        $order->state = Order::STATE_WAIT;
        $order->body = $body;

        $this->targetRewardOrder = new Order($order);
    }

    /**
     * Get target user order type.
     *
     * @return int
     * @author hh <915664508@qq.com>
     */
    protected function getTargetRewordOrderType(): int
    {
        if ($this->order->getOrderModel()->type === Order::TYPE_INCOME) {
            return Order::TYPE_EXPENSES;
        }

        return Order::TYPE_INCOME;
    }

    /**
     * Transfer.
     *
     * @param \Core\Packages\Wallet\Order $order
     * @param \Core\Packages\Wallet\Wallet $wallet
     * @return void
     * @author hh <915664508@qq.com>
     */
    protected function transfer(Order $order, Wallet $wallet)
    {
        $methods = [
            Order::TYPE_INCOME => 'increment',
            Order::TYPE_EXPENSES => 'decrement',
        ];
        $method = $methods[$order->getOrderModel()->type];
        $wallet->$method($order->getOrderModel()->amount);
    }

    /**
     * 记录打赏.
     *
     * @param $resource
     * @param $order
     */
    protected function createRewardRecord($resource, $order)
    {
        $orderModel = $order->getOrderModel();

        $resource->reward($orderModel->owner_id, $orderModel->amount);
    }
}
