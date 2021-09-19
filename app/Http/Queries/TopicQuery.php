<?php

namespace App\Http\Queries;

use App\Models\Topic;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class TopicQuery extends QueryBuilder
{
    public function __construct()
    {
        parent::__construct(Topic::query());

        $this->allowedIncludes('user', 'category') // 预加载的方法 传入某个，返回某个数据，默认不返回
            ->allowedFilters([// 传入被搜索的数据
                'title',
                AllowedFilter::exact('category_id'), // 分类ID
                AllowedFilter::scope('withOrder')->default('recentReplied'), // 排序规则
            ]);
    }
}
