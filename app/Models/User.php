<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
      use Traits\ActiveUserHelper;
     use HasRoles;
    // use  MustVerifyEmailTrait;
     use Traits\LastActivedAtHelper;
     use Notifiable {
         notify as protected laravelNotify;
     }

     protected $fillable = [
        'name', 'email', 'password','introduction','avatar','phone','weixin_openid', 'weixin_unionid'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

     public function notify($instance)
    {
        // 如果要通知的人是当前用户，就不必通知了！
        if ($this->id == Auth::id()) {
            return;
        }

        // 只有数据库类型通知才需提醒，直接发送 Email 或者其他的都 Pass
        if (method_exists($instance, 'toDatabase')) {
            $this->increment('notification_count');
        }

        $this->laravelNotify($instance);
    }



    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

     public function isAuthorOf($model)
    {
        return $this->id == $model->user_id;
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }

    public function setPasswordAttribute($value)
    {
        if(strlen($value)!=60){
            $value=bcrypt($value);
        }

        $this->attributes['password']=$value;

    }

    public function setAvatarAttribute($path)
    {
        if(!starts_with($path,'http'))
        {
            $path = config('app.url') . "/uploads/images/avatars/$path";

        }
        $this->attributes['avatar']= $path;
    }

     // Rest omitted for brevity

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }


}
