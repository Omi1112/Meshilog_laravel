@extends('layouts.app')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="container">
  <div class="row">
    <div class="col-2">
      <img width="100%" src="/storage/profileimg/{{$thisUser->img_path}}">
      <h4>{{$thisUser->name}}</h4>
    </div>
    <div class="col-10">
      <div class="row">
        <div class="col-2 user-nav-item {{$navThisArray['posts']}}">
          投稿一覧
          <a href="{{action('UserMenuController@postsView', $thisUser->id)}}"></a>
        </div>
        <div class="col-2 user-nav-item {{$navThisArray['calendar']}}">
          カレンダー
          <a href="{{action('UserMenuController@calendarView', $thisUser->id)}}"></a>
        </div>
        <div class="col-2 user-nav-item {{$navThisArray['follows']}}">
          フォロー
          <a href="{{action('UserMenuController@followsView', $thisUser->id)}}"></a>
        </div>
        <div class="col-2 user-nav-item {{$navThisArray['followers']}}">
          フォロワー
          <a href="{{action('UserMenuController@followersView', $thisUser->id)}}"></a>
        </div>
        <div class="col-2 user-nav-item {{$navThisArray['likes']}}">
          いいね
          <a href="{{action('UserMenuController@likesView', $thisUser->id)}}"></a>
        </div>
      </div>
      <div class="row mt-2">
        <div class="col-6">
          <button type="button" class="btn follow {{$followClass}}" data-follow-id="{{$thisUser->id}}">{{$followDo}}</button>
        </div>
      </div>

    </div>
  </div>
</div>

@yield('subContent')
@endsection

@section('foot')
<script type="text/javascript" src="/js/follows/followAddTake.js"></script>
@yield('subFoot')
@endsection
