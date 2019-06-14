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
     * 投稿一覧表示 カレンダーバージョン
     *
     * @return ユーザ表示画面
     */
    public function userCalendarView($userId){
      // 日付(今月を取得)
      $year = Carbon::today()->year;
      $month = Carbon::today()->month;
      $dateStr = sprintf('%04d-%02d-01', $year, $month);
      $date = new Carbon($dateStr);

      // カレンダーを四角形にするため、前月となる左上の隙間用のデータを入れるためずらす
      $date->subDay($date->dayOfWeek);
      $count = 42;

      $dates = [];
      $startDay = $date->copy();
      $endDay = $date->copy()->addDay($count);

      // 日付ごとの最新更新データを１件づつ取得する。
      $posts = DB::select('
        select m1.id,  m1.title , m1.body, m1.meal_date, m1.img_path
        from meshilogs as m1
        where
         m1.user_id = ? and
         m1.meal_date between ? and ? and
         m1.id =(
           select m2.id
           From meshilogs as m2
           Where m1.meal_date = m2.meal_date
           order by updated_at desc
           limit 1
         )
        order by meal_date asc',
        [$userId, $startDay->format('Y-m-d'), $endDay->format('Y-m-d')]
      );

      // 投稿と日付をマージ
      $postCnt = 0;
      for ($i = 0; $i < $count; $i++, $date->addDay()) {
        $dateObj = new DayAndPost();
        $dateObj->day = $date->copy();

        // マージ判定
        if($postCnt < count($posts)){
          if($dateObj->day->format('Y-m-d') == $posts[$postCnt]->meal_date){
            $dateObj->post = $posts[$postCnt];
            \Debugbar::info('$dateObj='.$dateObj->post->title);   // ロギング
            $postCnt++;
          }
        }
        $dates[] = $dateObj;
      }

      \Debugbar::info('dateEnd='.$dates[count($dates)-1]->day->day);   // ロギング
      return view('posts.userCalendarView')
            ->with('dates',$dates)
            ->with('currentMonth', $month)
            ->with('userId', $userId);

    }

    /**
     * 投稿一覧表示
     *
     * @return ユーザ表示画面
     */
    public function userView($userId)
    {
      // $meshilogs = Meshilog::where('user_id',$userId)->get();
      $meshilogs = DB::table('meshilogs')
        ->leftJoin('likes', function($join){
          $join->on('meshilogs.id', '=', 'likes.meshilog_id')
            ->where('likes.user_id', '=', Auth::user()->id);
        })
        ->where('meshilogs.user_id', $userId)
        ->get();
      return view('posts.userView')->with('meshilogs',$meshilogs);
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

      foreach ($tests as $key => $value) {
        \Debugbar::info('$test->day='.$value->day);   // ロギング
        \Debugbar::info('$test->post='.$value->post);   // ロギング
      }
      return view('posts.create');
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
