<?php

namespace Core\Observers;

use Core\Models\User;
use Core\Models\Famous;

class UserObserver
{
    /**
     * Handle the user "created" event.
     *
     * @param  User  $user
     *
     * @return void
     */
    public function created(User $user)
    {
        // 处理默认关注和默认相互关注
        $famous = Famous::query()->with('user')->get()
            ->groupBy('type');
        $famous
            ->map(function ($type, $key) use ($user) {
                $users = $type->filter(function ($famou) {
                    return $famou->user !== null;
                })->pluck('user');
                $user->followings()->attach($users->pluck('id'));
                // 相互关注
                if ($key === 'each') {
                    $users->map(function ($source) use ($user) {
                        $source->followings()->attach($user);
                    });
                }
            });
    }
}
