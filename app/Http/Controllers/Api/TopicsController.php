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

}
