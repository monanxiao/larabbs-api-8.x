<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Requests\Api\UserRequest;
use Illuminate\Auth\AuthenticationException;

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

        return new UserResource($user);
    }
}
