<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        // 三个判断：
        // 1. 如果用户已经登录
        // 2. 并且还未认证
        // 3. 并且访问的不是 email 验证相关 URL 或者退出的 URL。
        if ($request->user() && // 客户登录
            ! $request->user()->hasVerifiedEmail() && // 未验证邮箱
            ! $request->is('email/*', 'logout')) { // 访问的不是 email 相关 URL

                // 根据客户端返回对应的内容
                return $request->expectsJson()
                            ? abort(403, 'Your email address is not verified')
                            : redirect()->route('verification.notice');
        }


        return $next($request);
    }
}
