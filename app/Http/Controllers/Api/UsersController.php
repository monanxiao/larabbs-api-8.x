<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Requests\Api\UserRequest;
use Illuminate\Auth\AuthenticationException;
use App\Models\Image;

class UsersController extends Controller
{
    // 接收用户注册数据
    public function store(UserRequest $request)
    {
        // 获取缓存验证码
        $verifyData = \Cache::get($request->verification_key);

        // 检测验证码是否失效
        if (!$verifyData) {
           abort(403, '验证码已失效');
        }

        // 匹配验证
        if (!hash_equals($verifyData['code'], $request->verification_code)) {
            // 返回401
            throw new AuthenticationException('验证码错误');
        }

        // 创建用户
        $user = User::create([
            'name' => $request->name,
            'phone' => $verifyData['phone'],
            'password' => $request->password,
        ]);

        // 清除验证码缓存
        \Cache::forget($request->verification_key);

        return (new UserResource($user))->showSensitiveFields();
    }

    // 某个用户详情
    public function show(User $user, Request $request)
    {
        return new UserResource($user);
    }

    // 个人详情
    public function me(Request $request)
    {
        return (new UserResource($request->user()))->showSensitiveFields();
    }

    // 修改用户资料
    public function update(UserRequest $request)
    {
        // 当前登录用户
        $user = $request->user();
        // 字段白名单
        $attributes = $request->only(['name', 'email', 'introduction', 'registration_id']);
        // 检测是否存在 头像id
        if ($request->avatar_image_id) {
            // 获取图片ID实例
            $image = Image::find($request->avatar_image_id);
            // 赋值头像路径
            $attributes['avatar'] = $image->path;
        }

        // 更新用户资源
        $user->update($attributes);

        // 返回当前用户资源，并显示全部字段
        return (new UserResource($user))->showSensitiveFields();
    }

    // 活跃用户
    public function activedIndex(User $user)
    {
        UserResource::wrap('data');// 数据包含
        return UserResource::collection($user->getActiveUsers());
    }
}
