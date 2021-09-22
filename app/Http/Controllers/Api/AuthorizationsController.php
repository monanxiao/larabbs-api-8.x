<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Auth\AuthenticationException;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Http\Requests\Api\AuthorizationRequest;
use Psr\Http\Message\ServerRequestInterface;
use Laravel\Passport\Http\Controllers\AccessTokenController;

class AuthorizationsController extends AccessTokenController
{
    public function store(ServerRequestInterface $request)
    {
        return $this->issueToken($request)->setStatusCode(201);
    }

    public function update(ServerRequestInterface $request)
    {
        return $this->issueToken($request);
    }

     public function destroy()
    {
        if (auth('api')->check()) {
            auth('api')->user()->token()->revoke();
            return response(null, 204);
        } else {
            throw new AuthenticationException('The token is invalid.');
        }
    }

    // // 账号密码登录
    // public function store(AuthorizationRequest $request)
    // {
    //     // 获取用户名
    //     $username = $request->username;

    //     // 检测是否邮箱
    //     filter_var($username, FILTER_VALIDATE_EMAIL) ?
    //         $credentials['email'] = $username :
    //         $credentials['phone'] = $username;

    //     // 获取密码
    //     $credentials['password'] = $request->password;

    //     // 执行登录
    //     if (!$token = \Auth::guard('api')->attempt($credentials)) {
    //         // 登陆失败就报错
    //         // throw new AuthenticationException('用户名或密码错误');
    //         throw new AuthenticationException(trans('auth.failed'));// 本地化接口
    //     }

    //     return $this->respondWithToken($token)->setStatusCode(201);

    // }

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

        // 登录当前用户
        $token = auth('api')->login($user);

        return $this->respondWithToken($token)->setStatusCode(201);

    }

    // 更新用户 token
    // public function update()
    // {
    //     $token = auth('api')->refresh();
    //     return $this->respondWithToken($token);
    // }


    // 删除用户 token 退出登录
    // public function destroy()
    // {
    //     auth('api')->logout();
    //     return response(null, 204);
    // }

    // 统一返回格式
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token, // 返回token
            'token_type' => 'Bearer', // header
            'expires_in' => auth('api')->factory()->getTTL() * 60 // 设置 api token 60 分钟过期时间
        ]);
    }
}
