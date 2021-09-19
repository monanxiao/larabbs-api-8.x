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
}
