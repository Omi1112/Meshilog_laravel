<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\User;
use App\Meshilog;

class UserController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }
  /**
   * ユーザプロフィール表示
   *
   * @return ユーザプロフィールページ
   */
  public function profileView(){
    return view('users.profileView');
  }

  /**
   *ユーザのプロフィールアップデート
   *
   * @return プロフィールコントローラへリダイレクト
   */
    public function profileUpdate(Request $request){
      // 画像ファイルの有無判定
      $path = Auth::user()->img_path;
      if (!(is_null($request->file('img')))) {
        $path = $request->file('img')->store('public/profileimg');
        $path = str_replace('public/profileimg/', '', $path);
      }

      // Postデータ取得
      Auth::user()->name = $request->name;
      Auth::user()->img_path = $path;

      // DBへデータ格納
      Auth::user()->save();

      return redirect()->action('UserController@profileView');
  }

  /**
   * ユーザ一覧表示
   *
   * @return ユーザ一覧表示画面
   */
  public function usersView()
  {
    $users = DB::table('users')
      ->leftJoin('follows', function($join){
        $join->on('users.id', '=', 'follows.follow_id')
          ->where('follows.user_id', '=', Auth::user()->id);
      })
      ->get();

    return view('users.usersView')->with('users',$users);
  }
  /*
  /**
   * フォロー一覧表示
   *
   * @return フォロー一覧表示画面
   */
  public function followsView($userId)
  {
    // フォロー一覧
    $users = DB::table('users')
      ->select('users.id', 'user.name', 'userFollow.follow_id as followFlg')
      ->leftJoin('follows as userFollow', function($join){
        $join->on('users.id', '=', 'userFollow.follow_id')
          ->where('userFollow.user_id', '=', Auth::user()->id);
      })
      ->join('follows as followList', function($join) use($userId){
        $join->on('users.id', '=', 'followList.follow_id')
          ->where('followList.user_id', '=', $userId);
      })
      ->get();

    return view('users.followsView')->with('users',$users);
  }
  /**
   * ユーザ一覧表示
   *
   * @return ユーザ一覧表示画面
   */
  public function followersView($userId)
  {
    // フォロワー一覧
    $users = DB::table('users')
      ->select('users.id', 'user.name', 'userFollow.follow_id as followFlg')
      ->leftJoin('follows as userFollow', function($join){
        $join->on('users.id', '=', 'userFollow.follow_id')
          ->where('userFollow.user_id', '=', Auth::user()->id);
      })
      ->join('follows as followerList', function($join) use($userId){
        $join->on('users.id', '=', 'followerList.user_id')
          ->where('followerList.follow_id', '=', $userId);
      })
      ->get();

    return view('users.followersView')->with('users',$users);
  }
}
