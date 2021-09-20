<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Link;
use App\Http\Resources\LinkResource;

class LinksController extends Controller
{
    // 推荐资源
    public function index(Link $link)
    {
        $links = $link->getAllCached();

        // 返回资源包含 data 里面
        LinkResource::wrap('data');
        return LinkResource::collection($links);
    }
}
