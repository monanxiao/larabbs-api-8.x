<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Handlers\ImageUploadHandler;

class UsersController extends Controller
{
    // 过滤用户的操作
    public function __construct()
    {
        $this->middleware('auth', ['edit', 'update']); // 登陆后才可以操作编辑和更新
    }

    // 用户个人资料页
    public function show(User $user)
    {
        // 隐性绑定路由绑定 用户 $user
        return view('users.show', compact('user'));
    }

    // 资料编辑页
    public function edit(User $user)
    {
        // 授权验证
        $this->authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    // 接收资料更新数据 UserRequest 表单请求验证
    public function update(UserRequest $request, ImageUploadHandler $uploader, User $user)
    {
        // 授权验证
        $this->authorize('update', $user);

        // 获取表单数据
        $data = $request->all();

        // 验证图片是否存在
        if ($request->avatar) {
            // 调用图片上传方法
            $result = $uploader->save($request->avatar, 'avatars', $user->id, 416);
            // 验证是否回传参数
            if ($result) {
                $data['avatar'] = $result['path']; // 赋值文件路径
            }
        }

        $user->update($data);// 执行更新资料

        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
    }
}
