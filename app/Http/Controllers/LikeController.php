<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\User;
use App\Meshilog;
use App\Like;

class LikeController extends Controller
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
   * いいね追加　（Ajax専用）
   *
   * @return 成功もしくはエラー
   */
  public function likeAdd(Request $request)
  {

    $like = new Like();
    $like->user_id = Auth::user()->id;
    $like->meshilog_id = $request->meshilogId;

    $meshilog = Meshilog::find($request->meshilogId);
    $meshilog->like_sum++;

    DB::transaction(function() use($like, $meshilog) {
      $like->save();
      $meshilog->save();
      // return true;
    });
    $data = array();
    $data['likeSum'] = $meshilog->like_sum;
    $data['likeDo'] = 'いいねを取り消す';
    return response()->json($data);
  }

  /**
   * いいね取り消し　（Ajax専用）
   *
   * @return 成功もしくはエラー
   */
  public function likeTake(Request $request)
  {
    $meshilog = Meshilog::find($request->meshilogId);
    $meshilog->like_sum--;

    DB::transaction(function() use($meshilog, $request) {
      Like::where('user_id', Auth::user()->id)
        ->where('meshilog_id', $request->meshilogId)
        ->delete();
      $meshilog->save();
      return true;
    });
    $data = array();
    $data['likeSum'] = $meshilog->like_sum;
    $data['likeDo'] = 'いいね';
    return response()->json($data);
  }
}
