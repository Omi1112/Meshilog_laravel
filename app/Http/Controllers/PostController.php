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
      $test = new DayAndPost();
      $test->day = 10;
      $test->post = 'test';
      // $tests[];
      for($i=1; $i <= 10; $i++) {
        $test = new DayAndPost();
        $test->day = $i;
        $test->post = 'test' . $i;
        $tests[] = $test;
      }
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


    /**
     * １日の投稿データを取得する（Ajax専用）
     *
     * @return １日の投稿データ
     */
    public function dayPosts(Request $request){
      $meshilogs = Meshilog::where('user_id', $request->userId)
        ->where('meal_date', $request->date)->get();

      return view('posts.ajax.dayPosts')
        ->with('meshilogs', $meshilogs);
    }

}
