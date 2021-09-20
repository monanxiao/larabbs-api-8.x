<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ChangeLocale
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
        // 获取请求头 请求的语言格式
        $language = $request->header('accept-language');

        if ($language) {
            \App::setLocale($language);// 设置返回语言
        }

        return $next($request);
    }
}
