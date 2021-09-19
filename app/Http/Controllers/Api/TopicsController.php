<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Topic;
use App\Http\Resources\TopicResource;
use App\Http\Requests\Api\TopicRequest;

class TopicsController extends Controller
{
    // 话题发布
    public function store(TopicRequest $request, Topic $topic)
    {
        // 字段匹配
        $topic->fill($request->all());
        $topic->user_id = $request->user()->id;// 发布用户ID
        $topic->save();// 数据入库

        // 返回当前实例资源
        return new TopicResource($topic);

    }

}
