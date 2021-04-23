<?php

namespace Core\Packages\Wallet;

use JsonSerializable;
use Core\Models\User as UserModel;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Core\Models\NewWallet as WalletModel;

class Wallet implements Arrayable, Jsonable, JsonSerializable
{
    /**
     * The Wallet user.
     *
     * @var \Core\Models\User
     */
    protected $user;

    /**
     * The user wallet.
     *
     * @var \Core\Models\NewWallet
     */
    protected $wallet;

    /**
     * Create wallet.
     *
     * @param int|\Core\Models\User $user
     * @author GEO <dev@kaifa.me>
     */
    public function __construct($user = null)
    {
        if ($user) {
            $this->setUser($user);
        }
    }

    /**
     * Set user.
     *
     * @param int|\Core\Models\User $user
     * @author GEO <dev@kaifa.me>
     */
    public function setUser($user)
    {
        $this->user = $this->resolveUser($user);

        return $this;
    }

    /**
     * Get the user wallet model.
     *
     * @return \Core\Models\NewWallet
     * @author GEO <dev@kaifa.me>
     */
    public function getWalletModel(): WalletModel
    {
        if (! $this->wallet instanceof WalletModel) {
            throw new \Exception('没有设置钱包用户');
        }

        return $this->wallet;
    }

    /**
     * Increment the user wallet balance.
     *
     * @param int $amount
     * @author GEO <dev@kaifa.me>
     */
    public function increment(int $amount)
    {
        $wallet = $this->getWalletModel();
        $wallet->balance += $amount;
        $wallet->total_income += $amount;
        $wallet->save();
        $this->wallet = $wallet;

        return $this;
    }

    /**
     * Decrement the user wallet balance.
     *
     * @param int $amount
     * @author GEO <dev@kaifa.me>
     */
    public function decrement(int $amount)
    {
        $wallet = $this->getWalletModel();
        $wallet->balance -= $amount;
        $wallet->total_expenses += $amount;
        $wallet->save();
        $this->wallet = $wallet;

        return $this;
    }

    /**
     * The user wallet balance enough amount.
     *
     * @param int $amount
     * @return bool
     * @author GEO <dev@kaifa.me>
     */
    public function enough(int $amount): bool
    {
        $balance = $this->getWalletModel()->balance;

        return $balance >= $amount;
    }

    /**
     * Resolve user.
     *
     * @param int|\Core\Models\User $user
     * @return \Core\Models\User
     * @throws \Exception
     * @author GEO <dev@kaifa.me>
     */
    protected function resolveUser($user): UserModel
    {
        if (is_numeric($user)) {
            return $this->resolveWallet(
                $this->userFindOrFail((int) $user)
            );
        } elseif ($user instanceof UserModel) {
            return $this->resolveWallet($user);
        }

        throw new \Exception('传递的不是一个用户');
    }

    /**
     * Resolve the user wallet.
     *
     * @param \Core\Models\User $user
     * @return \Core\Models\User
     * @author GEO <dev@kaifa.me>
     */
    protected function resolveWallet(UserModel $user): UserModel
    {
        $this->wallet = $this->walletFind($user->id);

        if (! $this->wallet) {
            $this->wallet = new WalletModel();
            $this->wallet->owner_id = $user->id;
            $this->wallet->balance = 0;
            $this->wallet->total_income = 0;
            $this->wallet->total_expenses = 0;
        }

        return $user;
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     * @author GEO <dev@kaifa.me>
     */
    public function toArray()
    {
        return $this->getWalletModel()->toArray();
    }

    /**
     * Convert the model instance to JSON.
     *
     * @param int $options
     * @return string
     * @throws \RuntimeException
     * @author GEO <dev@kaifa.me>
     */
    public function toJson($options = 0)
    {
        $json = json_encode($this->jsonSerialize(), $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \RuntimeException('Error encoding the class ['.static::class.'] to JSON: '.json_last_error_msg());
        }

        return $json;
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     * @author GEO <dev@kaifa.me>
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Convert the model to its string representation.
     *
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Find user or fail.
     *
     * @param int $user
     * @return \Core\Models\User
     * @author GEO <dev@kaifa.me>
     */
    protected function userFindOrFail(int $user): UserModel
    {
        return UserModel::findOrFail($user);
    }

    /**
     * Find wallet.
     *
     * @param int $user
     * @return Core\Models\NewWallet
     * @author GEO <dev@kaifa.me>
     */
    protected function walletFind(int $user)
    {
        return WalletModel::find($user);
    }
}
