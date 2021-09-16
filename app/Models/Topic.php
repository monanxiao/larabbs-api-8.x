<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'body', 'category_id', 'excerpt', 'slug'
    ];

    // 一个话题属于一个作者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 一个话题属于一个分类
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // 话题排序
    public function scopeWithOrder($query, $order)
    {
        // 不同的排序，使用不同的数据读取逻辑
        switch ($order) {
            case 'recent':
                $query->recent();
                break;

            default:
                $query->recentReplied();
                break;
        }
    }

    // 排序规则 最新发布 创建时间排序
    public function scopeRecent($query)
    {
        // 按照创建时间排序
        return $query->orderBy('created_at', 'desc');
    }

    // 排序规则 最后回复 最新更新时间排序
    public function scopeRecentReplied($query)
    {
        // 当话题有新回复时，我们将编写逻辑来更新话题模型的 reply_count 属性，
        // 此时会自动触发框架对数据模型 updated_at 时间戳的更新
        return $query->orderBy('updated_at', 'desc');
    }

    // links 方法
    public function link($params = [])
    {
        return route('topics.show', array_merge([$this->id, $this->slug], $params));
    }

    // 一条话题有多个回复，一对多
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    // 统计当前回复数量
    public function updateReplyCount()
    {
        $this->reply_count = $this->replies->count(); // 统计当前回复数量
        $this->save(); // 保存更新数据
    }

}
