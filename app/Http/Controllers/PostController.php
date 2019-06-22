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

      $user = Auth::user();

      // Postデータ取得
      $meshilog = new Meshilog();
      $meshilog->title = $request->title;
      $meshilog->body = $request->body;
      $meshilog->img_path = $path;
      $meshilog->meal_timing = $request->meal_timing;
      $meshilog->meal_date = $request->meal_date;

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

    public function getUserCalendar(Request $request, $userId){

      // 日付(今月を取得)
      $year = $request->year;
      \Debugbar::info($year);   // ロギング
      $month = $request->month;
      $dateStr = sprintf('%04d-%02d-01', $year, $month);
      $date = new Carbon($dateStr);

      $data['previousYear'] = $date->copy()->subMonth()->year;
      $data['previousMonth'] = $date->copy()->subMonth()->month;
      $data['currentYear'] = $date->copy()->year;
      $data['currentMonth'] = $date->copy()->month;
      $data['nextYear'] = $date->copy()->addMonth()->year;
      $data['nextMonth'] = $date->copy()->addMonth()->month;

      // カレンダーを四角形にするため、前月となる左上の隙間用のデータを入れるためずらす
      $date->subDay($date->dayOfWeek);
      $count = 42;
      $dates = [];
      $startDay = $date->copy();
      $endDay = $date->copy()->addDay($count);

      // 日付ごとの最新更新データを１件づつ取得する。
      $meshilogs = DB::select('
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
      $meshilogCnt = 0;
      for ($i = 0; $i < $count; $i++, $date->addDay()) {
        $dateObj = new DayAndPost();
        $dateObj->day = $date->copy();

        // マージ判定
        if($meshilogCnt < count($meshilogs)){
          if($dateObj->day->format('Y-m-d') == $meshilogs[$meshilogCnt]->meal_date){
            $dateObj->post = $meshilogs[$meshilogCnt];
            $meshilogCnt++;
          }
        }
        $dates[] = $dateObj;
      }

      $data['calendarData'] = view('posts.ajax.calendarView')
        ->with('dates', $dates)
        ->with('userId', $userId)
        ->with('currentMonth', $request->month)
        ->render();

      return response()->json($data);
    }


    public function dayPosts(Request $request){
      $meshilogs = Meshilog::where('user_id', $request->userId)
        ->where('meal_date', $request->date)->get();

      return view('posts.ajax.dayPosts')
        ->with('meshilogs', $meshilogs);
    }

}
