<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meshilog extends Model
{
    protected $fillable = ['title', 'body', 'img_path'];

    public function user() {
      return $this->belongsTo('App\User');
    }

    public function likeUsers() {
      return $this->belongsToMany('App\User', 'likes', 'meshilog_id', 'user_id');
    }
}
