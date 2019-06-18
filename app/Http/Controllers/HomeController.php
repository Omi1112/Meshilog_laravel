<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\User;
use App\Meshilog;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
      return view('home.timeLineView');
    }

    public function getTimeLine()
    {
      $meshilogs = DB::table('meshilogs')
        ->select('meshilogs.id','meshilogs.user_id', 'meshilogs.title', 'meshilogs.body', 'meshilogs.img_path', 'meshilogs.like_sum', 'users.name as user_name', 'users.img_path as user_img_path', 'likes.meshilog_id')
        ->leftJoin('likes', function($join){
          $join->on('meshilogs.id', '=', 'likes.meshilog_id')
            ->where('likes.user_id', '=', Auth::user()->id);
        })
        ->join('users', 'meshilogs.user_id', '=', 'users.id')
        ->join('follows', function($join){
          $join->on('meshilogs.user_id', '=', 'follows.follow_id')
            ->where('follows.user_id', '=', Auth::user()->id);
        })
        ->paginate(10);


      \Debugbar::info($meshilogs->nextPageUrl());   // ロギング
      $data['nextPageUrl'] = $meshilogs->nextPageUrl();
      $data['cardData'] = view('home.ajax.timeLineItemsView')->with('meshilogs', $meshilogs)->render();

      \Debugbar::info($data['cardData']);   // ロギング

      return response()->json($data);
    }
}
