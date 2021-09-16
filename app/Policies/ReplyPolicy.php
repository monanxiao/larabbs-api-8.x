<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Reply;

class ReplyPolicy extends Policy
{
    public function update(User $user, Reply $reply)
    {
        // return $reply->user_id == $user->id;
        return true;
    }

    // 删除验证
    public function destroy(User $user, Reply $reply)
    {
        // 当前回复的用户ID == 当前用户 或 当前话题发布者，拥有删除回复权限
        return $user->isAuthorOf($reply) || $user->isAuthorOf($reply->topic);
    }
}
