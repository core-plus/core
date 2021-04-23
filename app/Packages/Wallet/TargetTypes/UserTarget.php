<?php

namespace Core\Packages\Wallet\TargetTypes;

use DB;
use Core\Packages\Wallet\Order;
use Core\Packages\Wallet\Wallet;
use Core\Models\WalletOrder as WalletOrderModel;

class UserTarget extends Target
{
    const ORDER_TITLE = '转账';
    protected $ownerWallet;     // \Core\Packages\Wallet\Wallet
    protected $targetWallet;    // \Core\Packages\Wallet\Wallet
    protected $targetUserOrder; // Core\Packages\Wallet\Order

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
        $this->createTargetUserOrder();

        $transaction = function () {
            $this->order->saveStateSuccess();
            $this->targetUserOrder->saveStateSuccess();
            $this->transfer($this->order, $this->ownerWallet);
            $this->transfer($this->targetUserOrder, $this->targetWallet);

            return true;
        };
        $transaction->bindTo($this);

        if (($result = DB::transaction($transaction)) === true) {
            $this->sendNotification();
        }

        return $result;
    }

    /**
     * Send notification.
     *
     * @return void
     * @author GEO <dev@kaifa.me>
     */
    protected function sendNotification()
    {
        // todo.
    }

    /**
     * Init owner and target user wallet.
     *
     * @return void
     * @author GEO <dev@kaifa.me>
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
     * @author GEO <dev@kaifa.me>
     */
    protected function createTargetUserOrder()
    {
        $order = new WalletOrderModel();
        $order->owner_id = $this->targetWallet->getWalletModel()->owner_id;
        $order->target_type = Order::TARGET_TYPE_USER;
        $order->target_id = $this->ownerWallet->getWalletModel()->owner_id;
        $order->title = static::ORDER_TITLE;
        $order->type = $this->getTargetUserOrderType();
        $order->amount = $this->order->getOrderModel()->amount;
        $order->state = Order::STATE_WAIT;

        $this->targetUserOrder = new Order($order);
    }

    /**
     * Get target user order type.
     *
     * @return int
     * @author GEO <dev@kaifa.me>
     */
    protected function getTargetUserOrderType(): int
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
     * @author GEO <dev@kaifa.me>
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
}
