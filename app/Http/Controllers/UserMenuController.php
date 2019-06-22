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
    $this->setUserData($userId);
    $this->navThisArray['calendar'] = NAVTHIS;

    return $this->returnCreate('userMenu.calendarView');
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
