<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

use App\User;
use App\Meshilog;

use App\Http\Requests\MeshilogRequest;

/**
 *userCalendarViewへの受け渡しクラス
 */
class DayAndPost
{
  public $day;
  public $post;
}


class PostController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 投稿画面表示
     *
     * @return 投稿画面
     */
    public function create()
    {
      return view('posts.create');
    }

    /**
     * 飯ログ格納
     *
     * @return 投稿一覧へリダイレクト
     */
    public function store(MeshilogRequest $request)
    {
      // 画像ファイルの有無判定
      $path = NULL;
      if (!(is_null($request->file('img')))) {
        $path = $request->file('img')->store('public/meshiimg');
        $path = str_replace('public/meshiimg/', '', $path);
      }

      \Debugbar::info('$path='.$path);   // ロギング

      // ログインユーザ取得
      $user = Auth::user();

      // Postデータ取得
      $meshilog = new Meshilog();
      $meshilog->title = $request->title;
      $meshilog->body = $request->body;
      $meshilog->img_path = $path;
      $meshilog->meal_timing = $request->meal_timing;
      $meshilog->meal_date = $request->meal_date;

      \Debugbar::info('$request='.$request);      // ロギング
      \Debugbar::info('$user='.$user);            // ロギング
      \Debugbar::info('$meshilog='.$meshilog);    // ロギング


      // DBへデータ格納
      $user->meshilogs()->save($meshilog);

      return redirect()->action('HomeController@index');
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
        ->paginate(9);
      $data['nextPageUrl'] = $meshilogs->nextPageUrl();
      $data['cardData'] = view('posts.ajax.postsView')->with('meshilogs', $meshilogs)->render();

      return response()->json($data);
    }

    public function getUserPosts($userId){
      $meshilogs = DB::table('meshilogs')
        ->select('meshilogs.id','meshilogs.user_id', 'meshilogs.title', 'meshilogs.body', 'meshilogs.img_path', 'meshilogs.like_sum', 'users.name as user_name', 'users.img_path as user_img_path', 'likes.meshilog_id')
        ->leftJoin('likes', function($join){
          $join->on('meshilogs.id', '=', 'likes.meshilog_id')
            ->where('likes.user_id', '=', Auth::user()->id);
        })
        ->join('users', 'meshilogs.user_id', '=', 'users.id')
        ->where('meshilogs.user_id', $userId)
        ->paginate(9);

      $data['nextPageUrl'] = $meshilogs->nextPageUrl();
      $data['cardData'] = view('posts.ajax.postsView')->with('meshilogs', $meshilogs)->render();

      return response()->json($data);
    }

    public function getUserLikes($userId){
      $meshilogs = DB::table('meshilogs')
      ->select('meshilogs.id','meshilogs.user_id', 'meshilogs.title', 'meshilogs.body', 'meshilogs.img_path', 'meshilogs.like_sum', 'users.name as user_name', 'users.img_path as user_img_path', 'myLikes.meshilog_id')
        ->leftJoin('likes as myLikes', function($join){
          $join->on('meshilogs.id', '=', 'myLikes.meshilog_id')
            ->where('myLikes.user_id', '=', Auth::user()->id);
        })
        ->join('users', 'meshilogs.user_id', '=', 'users.id')
        ->join('likes', function($join) use($userId){
          $join->on('meshilogs.id', '=', 'likes.meshilog_id')
            ->where('likes.user_id', '=', $userId);
        })
        ->paginate(9);

      $data['nextPageUrl'] = $meshilogs->nextPageUrl();
      $data['cardData'] = view('posts.ajax.postsView')->with('meshilogs', $meshilogs)->render();

      return response()->json($data);
    }


    public function dayPosts(Request $request){
      $meshilogs = Meshilog::where('user_id', $request->userId)
        ->where('meal_date', $request->date)->get();

      return view('posts.ajax.dayPosts')
        ->with('meshilogs', $meshilogs);
    }

}
