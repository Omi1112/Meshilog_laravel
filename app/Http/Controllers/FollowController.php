<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\User;
use App\Follow;


class FollowController extends Controller
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
   * フォローする　（Ajax専用）
   *
   * @return 成功もしくはエラー
   */
  public function followAdd(Request $request)
  {
    $follow = new Follow();
    $follow->user_id = Auth::user()->id;
    $follow->follow_id = $request->followId;

    $followUser = User::find($request->followId);
    $followUser->follower_sum++;

    $user = Auth::user();
    $user->follow_sum++;

    DB::transaction(function() use($follow, $followUser, $user) {
      $follow->save();
      $followUser->save();
      $user->save();
    });
    $data = array();
    $data['followDo'] = 'フォロー中';
    return response()->json($data);
  }

  /**
   * フォロー取り消し　（Ajax専用）
   *
   * @return 成功もしくはエラー
   */
  public function followTake(Request $request)
  {
    $followUser = User::find($request->followId);
    $followUser->follower_sum--;

    $user = Auth::user();
    $user->follow_sum--;

    DB::transaction(function() use($followUser, $user, $request) {
      Follow::where('user_id', $user->id)
        ->where('follow_id', $request->followId)
        ->delete();
      $followUser->save();
      $user->save();
      return true;
    });
    $data = array();
    $data['followDo'] = 'フォローする';
    return response()->json($data);
  }
}
