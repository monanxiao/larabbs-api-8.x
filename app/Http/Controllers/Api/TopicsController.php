<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Topic;
use App\Http\Queries\TopicQuery;
use App\Http\Resources\TopicResource;
use App\Http\Requests\Api\TopicRequest;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Models\User;

class TopicsController extends Controller
{

    // 话题列表
    public function index(Request $request, Topic $topic, TopicQuery $query)
    {

        $topics = $query->paginate();

        return TopicResource::collection($topics);

    }

    // 某个用户发布的话题
    public function userIndex(Request $request, User $user, TopicQuery $query)
    {
        $topics = $query->where('user_id', $user->id)->paginate();

        return TopicResource::collection($topics);
    }

    // 话题发布
    public function store(TopicRequest $request, Topic $topic)
    {

        // return $this->errorResponse(403, '您还没有通过认证', 1003);// 自定义错误代码

        // 字段匹配
        $topic->fill($request->all());
        $topic->user_id = $request->user()->id;// 发布用户ID
        $topic->save();// 数据入库

        // 返回当前实例资源
        return new TopicResource($topic);

    }

    // 话题修改(更新、编辑)
    public function update(TopicRequest $request, Topic $topic)
    {

        $this->authorize('update', $topic);// 验证是否有权限修改 策略

        $topic->update($request->all());// 修改数据

        return new TopicResource($topic);// 返回资源
    }

    // 话题删除
    public function destroy(Topic $topic)
    {
        // 验证是否有权限删除
        $this->authorize('destroy', $topic);
        $topic->delete();// 删除成功

        return response(null, 204);
    }

    // 话题详情
    // public function show(Topic $topic)
    public function show($topicId, TopicQuery $query)// 不适用模型绑定
    {

        $topic = $query->findOrFail($topicId);

        return new TopicResource($topic);
    }
}
