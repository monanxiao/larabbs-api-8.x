<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Topic;
use App\Models\Reply;
use App\Http\Resources\ReplyResource;
use App\Http\Requests\Api\ReplyRequest;

class RepliesController extends Controller
{
    // 发布回复
    public function store(ReplyRequest $request, Topic $topic, Reply $reply)
    {
        $reply->content = $request->content;// 回复内容
        $reply->topic()->associate($topic);// 增加外键
        $reply->user()->associate($request->user());// 增加外键
        $reply->save();// 保存内容

        return new ReplyResource($reply);
    }

    // 删除回复
    public function destroy(Topic $topic, Reply $reply)
    {
        // 判断话题id是否和回复话题ID一致
        if ($reply->topic_id != $topic->id) {
            abort(404);
        }
        // 检测权限
        $this->authorize('destroy', $reply);
        $reply->delete();// 执行删除

        return response(null, 204);
    }
}
