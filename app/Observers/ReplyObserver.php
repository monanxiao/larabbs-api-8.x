<?php

namespace App\Observers;

use App\Models\Reply;
use App\Notifications\TopicReplied;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ReplyObserver
{

    // 用户回复内容监听 处理 XSS 攻击
    public function creating(Reply $reply)
    {
        // 过滤用户的内容
        $reply->content = clean($reply->content, 'user_topic_body');

    }

    // 用户回复话题后，回复数+1
    public function created(Reply $reply)
    {
        // 简单用法
        // $reply->topic->increment('reply_count', 1);

        // 计算当前的回复总数，严谨用法
        $reply->topic->updateReplyCount();

        // 命令行运行迁移时不做这些操作！
        if ( ! app()->runningInConsole()) {

            $reply->topic->updateReplyCount();
           // 通知话题作者有新的评论
            $reply->topic->user->notify(new TopicReplied($reply));
        }

    }

    // 删除回复后，回复数 -1
    public function deleted(Reply $reply)
    {
        // 计算当前的回复总数，严谨用法
        $reply->topic->updateReplyCount();

    }
}
