<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Image;
use Illuminate\Support\Str;
use App\Handlers\ImageUploadHandler;
use App\Http\Resources\ImageResource;
use App\Http\Requests\Api\ImageRequest;

class ImagesController extends Controller
{
    // 上传图片
    public function store(ImageRequest $request, ImageUploadHandler $uploader, Image $image)
    {
        // 当前用户
        $user = $request->user();
        // 判断类型
        $size = $request->type == 'avatar' ? 416 : 1024;
        // 上传图片
        $result = $uploader->save($request->image, Str::plural($request->type), $user->id, $size);

        $image->path = $result['path'];// 上传路径
        $image->type = $request->type;// 上传类型
        $image->user_id = $user->id;// 上传用户id
        $image->save(); // 保存

        return new ImageResource($image);// 返回图片资源
    }
}
