<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\CategoryResource;

class CategoriesController extends Controller
{
    // 分类列表
    public function index()
    {
        // 创建数组 把数据集合放在 data 下
        CategoryResource::wrap('data');
        return CategoryResource::collection(Category::all());
    }
}
