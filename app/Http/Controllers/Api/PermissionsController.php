<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Resources\PermissionResource;

class PermissionsController extends Controller
{
    //
    public function index(Request $request)
    {
        // 获取当前用户拥有的权限
        $permissions = $request->user()->getAllPermissions();

        // 包含在 data 数据里
        PermissionResource::wrap('data');
        // 返回集合
        return PermissionResource::collection($permissions);

    }
}
