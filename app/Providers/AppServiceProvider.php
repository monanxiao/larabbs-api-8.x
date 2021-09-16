<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // 用户切换扩展包注册 只有本地开发环境中使用
        if (app()->isLocal()) {
            $this->app->register(\VIACreative\SudoSu\ServiceProvider::class);
        }

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
	{
		\App\Models\User::observe(\App\Observers\UserObserver::class);
		\App\Models\Reply::observe(\App\Observers\ReplyObserver::class);// 用户监听器
		\App\Models\Topic::observe(\App\Observers\TopicObserver::class);// 话题监听器
        \App\Models\Link::observe(\App\Observers\LinkObserver::class);// 推荐资源更新监听

        // 使用 boostatrap 样式
        \Illuminate\Pagination\Paginator::useBootstrap();
    }
}
