<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Topic;
use App\Http\Resources\TopicResource;
use App\Http\Requests\Api\TopicRequest;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Models\User;

class TopicsController extends Controller
{

    // 话题列表
    public function index(Request $request, Topic $topic)
    {

        $topics = QueryBuilder::for(Topic::class) // 模型类
                        ->allowedIncludes('user', 'category') // 预加载的方法 传入某个，返回某个数据，默认不返回
                        ->allowedFilters([ // 传入被搜索的数据
                            'title', // 标题
                            AllowedFilter::exact('category_id'), // 分类ID
                            AllowedFilter::scope('withOrder')->default('recentReplied'),// 排序规则
                        ])
                        ->paginate();

        // $query = $topic->query();// 执行语句
        // // 判断查询类型
        // if ($categoryId = $request->category_id) {
        //     // id查询
        //     $query->where('category_id', $categoryId);
        // }

        // $topics = $query->with('user', 'category') // 预加载关联方法
        //                 ->withOrder($request->order) // 排序规则
        //                 ->paginate();// 分页
        // 返回资源集合
        return TopicResource::collection($topics);
    }

    // 某个用户发布的话题
    public function userIndex(Request $request, User $user)
    {
        // 获取用户发布的话题
        $query = $user->topics()->getQuery();

        $topics = QueryBuilder::for($query)
            ->allowedIncludes('user', 'category')// 预加载的方法 传入某个，返回某个数据，默认不返回
            ->allowedFilters([// 传入被搜索的数据
                'title',
                AllowedFilter::exact('category_id'),
                AllowedFilter::scope('withOrder')->default('recentReplied'),
            ])
            ->paginate();

        return TopicResource::collection($topics);
    }

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
