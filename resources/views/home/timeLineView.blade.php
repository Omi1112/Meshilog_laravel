@extends('layouts.app')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="container">
  <div class="row justify-content-center">
    タイムライン
  </div>
  <div class="row justify-content-center">
    <div class="col-md-12">
      <div id="cards" class="row">
      </div>
    </div>
  </div>
  <div id="loading">
    Now Loading...
    <a id="next-url" href="{{action('HomeController@getTimeLine')}}"></a>
  </div>
</div>

@endsection

@section('foot')
<script type="text/javascript" src="/js/likes/likeAddTake.js"></script>
<script type="text/javascript" src="/js/home/timeLineLoad.js"></script>

@endsection
