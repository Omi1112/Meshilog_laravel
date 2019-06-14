<?php

namespace App;


use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','img_path', 'follow_sum', 'follower_sum',
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

    public function meshilogs() {
      return $this->hasMany('App\Meshilog');
    }
    public function likeMeshilogs() {
      return $this->belongsToMany('App\Meshilog', 'likes', 'user_id', 'meshilog_id');
    }

    public function followUsers() {
      return $this->belongsToMany('App\User', 'follows', 'user_id', 'follow_id');
    }

    public function followerUsers() {
      return $this->belongsToMany('App\User', 'follows', 'follow_id', 'user_id');
    }
}
