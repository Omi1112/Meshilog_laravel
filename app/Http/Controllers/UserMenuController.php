<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

use App\User;
use App\Meshilog;

use App\Http\Requests\MeshilogRequest;

define("NAVTHIS", "user-nav-item-this");

class DayAndPost
{
  public $day;
  public $meshilog;
}

class UserMenuController extends Controller
{
  public $navThisArray = array(
    'posts'=>'',
    'calendar'=>'',
    'follows'=>'',
    'followers'=>'',
    'likes'=>'',
  );
  public const NAVTHIS = 'user-nav-item-this';

  public function __construct()
  {
    $this->middleware('auth');
  }

  public function postsView($userId){
    $this->navThisArray['posts'] = NAVTHIS;
    $thisUser = User::find($userId);

    $meshilogs = DB::table('meshilogs')
      ->leftJoin('likes', function($join){
        $join->on('meshilogs.id', '=', 'likes.meshilog_id')
          ->where('likes.user_id', '=', Auth::user()->id);
      })
      ->where('meshilogs.user_id', $userId)
      ->get();

    return view('userMenu.postsView')
      ->with('meshilogs',$meshilogs)
      ->with('navThisArray',$this->navThisArray)
      ->with('thisUser',$thisUser);
  }

  public function dayPosts(Request $request){
    $meshilogs = Meshilog::where('user_id', $request->userId)
      ->where('meal_date', $request->date)->get();

    return view('posts.ajax.dayPosts')
      ->with('meshilogs', $meshilogs);
  }

  public function calendarView($userId){
    $this->navThisArray['calendar'] = NAVTHIS;
    $thisUser = User::find($userId);

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

    return view('userMenu.calendarView')
          ->with('dates',$dates)
          ->with('currentMonth', $month)
          ->with('navThisArray',$this->navThisArray)
          ->with('thisUser',$thisUser);
  }

  public function followsView($userId){
    $this->navThisArray['follows'] = NAVTHIS;
    $thisUser = User::find($userId);

    $users = DB::table('users')
      ->select('users.id', 'users.name', 'users.img_path', 'userFollow.follow_id as follow_id')
      ->leftJoin('follows as userFollow', function($join){
        $join->on('users.id', '=', 'userFollow.follow_id')
          ->where('userFollow.user_id', '=', Auth::user()->id);
      })
      ->join('follows as followList', function($join) use($userId){
        $join->on('users.id', '=', 'followList.follow_id')
          ->where('followList.user_id', '=', $userId);
      })
      ->get();

    return view('userMenu.followsView')
      ->with('users',$users)
      ->with('navThisArray',$this->navThisArray)
      ->with('thisUser',$thisUser);
  }

  public function followersView($userId){
    $this->navThisArray['followers'] = NAVTHIS;
    $thisUser = User::find($userId);

    $users = DB::table('users')
    ->select('users.id', 'users.name', 'users.img_path', 'userFollow.follow_id as follow_id')
      ->leftJoin('follows as userFollow', function($join){
        $join->on('users.id', '=', 'userFollow.follow_id')
          ->where('userFollow.user_id', '=', Auth::user()->id);
      })
      ->join('follows as followerList', function($join) use($userId){
        $join->on('users.id', '=', 'followerList.user_id')
          ->where('followerList.follow_id', '=', $userId);
      })
      ->get();

    return view('userMenu.followersView')
      ->with('users',$users)
      ->with('navThisArray',$this->navThisArray)
      ->with('thisUser',$thisUser);
  }

  public function likesView($userId){
    $this->navThisArray['likes'] = NAVTHIS;
    $thisUser = User::find($userId);

    $meshilogs = DB::table('meshilogs')
    ->select('meshilogs.id', 'meshilogs.title', 'meshilogs.body', 'meshilogs.img_path', 'meshilogs.like_sum', 'myLikes.meshilog_id')
      ->leftJoin('likes as myLikes', function($join){
        $join->on('meshilogs.id', '=', 'myLikes.meshilog_id')
          ->where('myLikes.user_id', '=', Auth::user()->id);
      })
      ->join('likes', function($join) use($userId){
        $join->on('meshilogs.id', '=', 'likes.meshilog_id')
          ->where('likes.user_id', '=', $userId);
      })
      ->where('meshilogs.user_id', $userId)
      ->get();

    return view('userMenu.postsView')
      ->with('meshilogs',$meshilogs)
      ->with('navThisArray',$this->navThisArray)
      ->with('thisUser',$thisUser);
  }

}
