<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Router;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

use App\User;
use App\Meshilog;
use App\Follow;


use App\Http\Requests\MeshilogRequest;

define("NAVTHIS", "user-nav-item-this");

class UserMenuController extends Controller
{
  public $navThisArray = array(
    'posts'=>'',
    'calendar'=>'',
    'follows'=>'',
    'followers'=>'',
    'likes'=>'',
  );
  public $user;
  public $followClass;
  public $followDo;

  public const NAVTHIS = 'user-nav-item-this';

  public function __construct(Router $router)
  {
    $this->middleware('auth');
  }

  public function setUserData($userId)
  {
    $this->user =  User::find($userId);

    $follow = Follow::where('user_id', Auth::User()->id)
      ->where('follow_id', $userId)
      ->get();
    $this->followClass = 'btn-primary';
    $this->followDo ='フォロー中';
    if(is_null($follow)){
      $this->followClass = 'btn-outline-primary';
      $this->followDo ='フォローする';
    }
  }

  public function returnCreate($viewPath)
  {
    return view($viewPath)
      ->with('navThisArray',$this->navThisArray)
      ->with('thisUser',$this->user)
      ->with('followDo',$this->followDo)
      ->with('followClass',$this->followClass);
  }

  public function postsView($userId){
    $this->setUserData($userId);
    $this->navThisArray['posts'] = NAVTHIS;

    return $this->returnCreate('userMenu.postsView');
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
    $this->setUserData($userId);
    $this->navThisArray['follows'] = NAVTHIS;

    return $this->returnCreate('userMenu.followsView');
  }

  public function followersView($userId){
    $this->setUserData($userId);
    $this->navThisArray['followers'] = NAVTHIS;

    return $this->returnCreate('userMenu.followersView');
  }

  public function likesView($userId){
    $this->setUserData($userId);
    $this->navThisArray['likes'] = NAVTHIS;

    return $this->returnCreate('userMenu.likesView');
  }

}
