<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Auth;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use MustVerifyEmailTrait;
    use Traits\LastActivedAtHelper;

    // use Notifiable;
    use Notifiable{
        notify as protected laravelNotify;
    }

    public function notify($instance)
    {
        //只有資料庫類型通知才需提醒，直接發送Email或其他的都Pass
        if(method_exists($instance, 'toDatabase')){
            $this->increment('notification_count');

            //如果要通知的人是當前用戶，就不必通知了
            if($this->id == Auth::id()){
                return;
            }
        }

        $this->laravelNotify($instance);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'introduction', 'avatar',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

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
}
