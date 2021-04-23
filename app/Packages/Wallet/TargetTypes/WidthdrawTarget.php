<?php

namespace Core\Packages\Wallet\TargetTypes;

use DB;
use Core\Packages\Wallet\Order;
use Core\Packages\Wallet\Wallet;
use Core\Models\WalletCash as WalletCashModel;

class WidthdrawTarget extends Target
{
    const ORDER_TITLE = '提现';
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
    public function handle($type, $account): bool
    {
        if (! $this->order->hasWait()) {
            return true;
        }

        $this->initWallet();

        $orderHandle = function () use ($type, $account) {
            $this->order->saveStateSuccess();
            $this->wallet->{$this->method[$this->order->getOrderModel()->type]}($this->order->getOrderModel()->amount);

            $this->createCash($type, $account);

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

    /**
     * 创建提现申请.
     *
     * @param $type
     * @param $account
     * @return void
     * @author GEO <dev@kaifa.me>
     */
    protected function createCash($type, $account)
    {
        $cashModel = new WalletCashModel();
        $cashModel->user_id = $this->order->getOrderModel()->owner_id;
        $cashModel->value = $this->order->getOrderModel()->amount;
        $cashModel->type = $type;
        $cashModel->account = $account;
        $cashModel->status = 0;

        $cashModel->save();
    }
}
