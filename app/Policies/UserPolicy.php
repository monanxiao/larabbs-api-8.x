<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    // 用户更新授权策略
    public function update(User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;// 当登录ID全等于操作ID时，才可更新
    }

}
