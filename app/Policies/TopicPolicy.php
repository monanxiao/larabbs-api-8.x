<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Topic;

class TopicPolicy extends Policy
{
    // 更新验证，当前话题的作者 === 当前登录用户ID时才可以更新。
    public function update(User $user, Topic $topic)
    {
        return $user->isAuthorOf($topic);
        // return true;
    }

    // 删除验证 当前话题的作者 === 当前登录用户ID时才可以删除。
    public function destroy(User $user, Topic $topic)
    {
        return $user->isAuthorOf($topic);
        // return true;
    }
}
