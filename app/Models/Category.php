<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // 不使用 laravel 时间维护
    public $timestamps = false;

    // 字段白名单  可批量赋值属性
    protected $fillable = [
        'name', 'description'
    ];
}
