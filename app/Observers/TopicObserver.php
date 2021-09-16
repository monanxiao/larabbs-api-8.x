<?php

namespace App\Observers;

use App\Models\Topic;
use App\Handlers\SlugTranslateHandler;
use App\Jobs\TranslateSlug;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{
    public function creating(Topic $topic)
    {
        //
    }

    public function updating(Topic $topic)
    {
        //
    }

    // saving 保存前监听
    public function saving(Topic $topic)
    {

        // 入库前，对输入内容进行过滤
        $topic->body = clean($topic->body, 'user_topic_body');

        // 赋值 excerpt 调用辅助方法 make_excerpt
        $topic->excerpt = make_excerpt($topic->body);

    }

    // saved 数据保存后监听
    public function saved(Topic $topic)
    {
        // 如 slug 字段无内容，即使用翻译器对 title 进行翻译
        if ( ! $topic->slug)
        {
            // 调用自动翻译方法
            // $topic->slug = app(SlugTranslateHandler::class)->translate($topic->title);
            // 推送任务到队列
            dispatch(new TranslateSlug($topic));

        }
    }

    // 监听话题删除
    public function deleted(Topic $topic)
    {
        // 当话题删除时，连带删除所有回复
        \DB::table('replies')->where('topic_id', $topic->id)->delete();
    }
}
