<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Auth;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements MustVerifyEmailContract,JWTSubject
{
    // use HasApiTokens, HasFactory, Notifiable, MustVerifyEmailTrait;
    use Traits\LastActivedAtHelper;
    use Traits\ActiveUserHelper;
    use HasRoles;
    use HasApiTokens, HasFactory, MustVerifyEmailTrait;

    // 通知引入修改
    use Notifiable {
        notify as protected laravelNotify;
    }

    // 修改通知方法
    public function notify($instance)
    {
        // 如果要通知的人是当前用户，就不必通知了！
        if ($this->id == Auth::id()) {
            return;
        }

        // 只有数据库类型通知才需提醒，直接发送 Email 或者其他的都 Pass
        if (method_exists($instance, 'toDatabase')) {
            // 未读 + 1
            $this->increment('notification_count');
        }

        $this->laravelNotify($instance);

    }


    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'introduction',
        'avatar',
        'weixin_openid',
        'weixin_unionid'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // 用户拥有的话题 一对多
    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    // 验证
    public function isAuthorOf($model)
    {
        // 当前登录用户ID == 当前数据所有者ID 验证是否匹配
        return $this->id == $model->user_id;
    }

    // 一条个用户有多个评论，一对多
    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    // 当用户读了通知消息，清空未读
    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }

    // 密码修改器
    public function setPasswordAttribute($value)
    {
        // 如果值的长度等于 60，即认为是已经做过加密的情况
        if (strlen($value) != 60) {

            // 不等于 60，做密码加密处理
            $value = bcrypt($value);
        }

        $this->attributes['password'] = $value;
    }

    // 头像修改器
    public function setAvatarAttribute($path)
    {
        // 如果不是 `http` 子串开头，那就是从后台上传的，需要补全 URL
        if ( ! \Str::startsWith($path, 'http')) {

            // 拼接完整的 URL
            $path = config('app.url') . "/uploads/images/avatars/$path";
        }

        $this->attributes['avatar'] = $path;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

}
