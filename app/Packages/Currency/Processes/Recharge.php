<?php

namespace Core\Packages\Currency\Processes;

use DB;
use Illuminate\Http\Request;
use function Core\setting;
use Pingpp\Charge as PingppCharge;
use Core\Packages\Currency\Order;
use Core\Packages\Currency\Process;
use Core\Models\CurrencyOrder as CurrencyOrderModel;
use Core\Services\Wallet\Charge as WalletChargeService;

class Recharge extends Process
{
    // ping++订单前缀标识
    protected $PingppPrefix = 'C';

    public function createOrder(int $owner_id, int $amount)
    : CurrencyOrderModel
    {
        $user = $this->checkUser($owner_id);
        $ratio = setting('currency', 'settings')['recharge-ratio'] ?? 1;

        $title = '积分充值';
        $body = sprintf('充值积分：%s%s%s', $amount, $this->currency_type['unit'], $this->currency_type['name']);

        $order = new CurrencyOrderModel();
        $order->owner_id = $user->id;
        $order->title = $title;
        $order->body = $body;
        $order->type = 1;
        $order->currency = $this->currency_type['id'];
        $order->target_type = Order::TARGET_TYPE_RECHARGE;
        $order->amount = $amount * $ratio;

        return $order;
    }

    /**
     * 创建充值订单.
     *
     * @param  int  $owner_id
     * @param  int  $amount
     * @param  string  $type
     * @param  array  $extra
     *
     * @return mixed
     * @author GEO <dev@kaifa.me>
     */
    public function createPingPPOrder(int $owner_id, int $amount, string $type, array $extra = [])
    {
        if (app(WalletChargeService::class)->checkRechargeArgs($type, $extra)) {
            $transaction = function () use ($owner_id, $amount, $extra, $type) {
                $order = $this->createOrder($owner_id, $amount);
                $order->save();
                $service = app(WalletChargeService::class)->setPrefix($this->PingppPrefix);
                $pingppCharge = $service->createWithoutModel($order->id, $type, $order->amount, $order->title,
                    $order->body, $extra);
                $order->target_id = $pingppCharge->id;
                $order->save();

                return [
                    'pingpp_order' => $pingppCharge,
                    'order'        => $order,
                ];
            };

            return DB::transaction($transaction);
        }

        return false;
    }

    /**
     * 主动取回凭据.
     *
     * @return boolen
     * @author GEO <dev@kaifa.me>
     */
    public function retrieve(CurrencyOrderModel $currencyOrderModel)
    : bool
    {
        $pingppCharge = app(WalletChargeService::class)->query($currencyOrderModel->target_id);

        if ($pingppCharge['paid'] === true) {
            return $this->complete($currencyOrderModel);
        }

        return false;
    }

    /**
     * 异步回调通知.
     *
     * @param  Request  $request
     *
     * @return boolen
     * @author GEO <dev@kaifa.me>
     */
    public function webhook(Request $request)
    : bool
    {
        if ($this->verifyWebHook($request)) {
            $pingppCharge = $request->json('data.object');
            $service = app(WalletChargeService::class)->setPrefix($this->PingppPrefix);
            $currencyOrderModel = CurrencyOrderModel::find($service->unformatChargeId($pingppCharge['order_no']));

            if ($currencyOrderModel || $currencyOrderModel->state === 1) {
                return true;
            }

            return $this->complete($currencyOrderModel);
        }

        return false;
    }

    /**
     * 完成充值操作.
     *
     * @param  PingppCharge  $pingppCharge
     * @param  CurrencyOrderModel  $currencyOrderModel
     *
     * @return boolen
     * @author GEO <dev@kaifa.me>
     */
    private function complete(CurrencyOrderModel $currencyOrderModel)
    : bool
    {
        $currencyOrderModel->state = 1;
        $user = $this->checkUser($currencyOrderModel->user);

        return DB::transaction(function () use ($user, $currencyOrderModel) {
            $currencyOrderModel->save();
            $user->currency->increment('sum', $currencyOrderModel->amount);

            return true;
        });
    }

    /**
     * 验证回调信息.
     *
     * @param  Request  $request
     *
     * @return boolen
     * @author GEO <dev@kaifa.me>
     */
    protected function verifyWebHook(Request $request)
    : bool
    {
        if ($request->json('type') !== 'charge.succeeded') {
            return false;
        }

        $settings = setting('wallet', 'ping++', []);
        $signature = $request->headers->get('x-pingplusplus-signature');
        $pingPlusPlusPublicCertificate = $settings['public_key'] ?? null;
        $signed = openssl_verify($request->getContent(), base64_decode($signature), $pingPlusPlusPublicCertificate,
            OPENSSL_ALGO_SHA256);

        if (! $signed) {
            return false;
        }

        return true;
    }
}
