<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Auth\AuthenticationException;
use App\Http\Requests\Api\SocialAuthorizationRequest;

class AuthorizationsController extends Controller
{
    // 第三方登录
    public function socialStore($type, SocialAuthorizationRequest $request)
    {
        // 实例化登录类型 wechat 微信
        $driver = \Socialite::create($type);

        // 执行
        try {

            // 判断 code 存在的时候
            if ($code = $request->code) {

                // 获取微信用户数据
                $oauthUser = $driver->userFromCode($code);

            } else {

                // 获取 acctoken 再次获取用户数据

                // 微信需要增加 openid
                if ($type == 'wechat') {

                    $driver->withOpenid($request->openid);
                }
                // 获取 acctoken
                $oauthUser = $driver->userFromToken($request->access_token);
            }
        } catch (\Exception $e) {
            // 授权失败返回
           throw new AuthenticationException('参数错误，未获取用户信息');
        }

        // 获取微信 openId 是否成功
        if (!$oauthUser->getId()) {
           throw new AuthenticationException('参数错误，未获取用户信息');
        }

        // 判断第三方登录类型
        switch ($type) {
            case 'wechat': // 微信登录
                // 检测是否有 unionid
                $unionid = $oauthUser->getRaw()['unionid'] ?? null;

                // 假如存在  unionid 则查找 unionid 用户是否存在，否则使用 openid 查找
                if ($unionid) {
                    // 通过 unionid 查询用户是否存在
                    $user = User::where('weixin_unionid', $unionid)->first();
                } else {
                    // 通过 openid 查询用户是否存在
                    $user = User::where('weixin_openid', $oauthUser->getId())->first();
                }

                // 没有用户，默认创建一个用户
                if (!$user) {
                    $user = User::create([
                        'name' => $oauthUser->getNickname(),// 微信昵称
                        'avatar' => $oauthUser->getAvatar(),// 微信头像
                        'weixin_openid' => $oauthUser->getId(),// 微信 openId
                        'weixin_unionid' => $unionid, // unionid
                    ]);
                }

                break;
        }

        return response()->json(['token' => $user->id]);
    }
}
