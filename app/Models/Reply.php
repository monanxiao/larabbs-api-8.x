<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reply extends Model
{
    use HasFactory;

    protected $fillable = ['topic_id', 'user_id', 'content'];

    // 一条回复属于一个作者
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    // 一条回复属于一个话题
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
