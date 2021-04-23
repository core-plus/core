<?php

namespace Core\Packages\Currency;

use Core\Models\User as UserModel;
use Core\Models\CurrencyType as CurrencyTypeModel;

class Process
{
    /**
     * 货币类型.
     *
     * @var [CurrencyTypeModel]
     */
    protected $currency_type;

    public function __construct()
    {
        $this->currency_type = CurrencyTypeModel::current();
    }

    /**
     * 检测用户模型.
     *
     * @param $user
     * @param  bool  $throw
     *
     * @return UserModel | bool
     * @throws \Exception
     * @author GEO <dev@kaifa.me>
     */
    public function checkUser($user, $throw = true)
    {
        if (is_numeric($user)) {
            $user = UserModel::find((int) $user);
        }

        if (! $user) {
            if ($throw) {
                throw new \Exception('找不到所属用户', 1);
            }

            return false;
        }

        return $this->checkCurrency($user);
    }

    /**
     * 检测用户货币模型，防止后续操作出现错误.
     *
     * @param  UserModel  $user
     *
     * @return UserModel
     * @author GEO <dev@kaifa.me>
     */
    protected function checkCurrency(UserModel $user)
    : UserModel
    {
        if (! $user->currency) {
            $user->currency = $user->currency()
                ->create(['type' => $this->currency_type->get('id'), 'sum' => 0]);
        }

        return $user;
    }
}
