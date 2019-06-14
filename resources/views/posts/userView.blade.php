@extends('layouts.app')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12">
      <div class="card-columns">
      @foreach($meshilogs as $meshilog)
        <div class="card">
          <div class="card-header">
            {{$meshilog->title}}
          </div>

          <div class="card-body">
            {{$meshilog->body}}
          </div>

          @isset($meshilog->img_path)
            <img class="card-img-top" src="/storage/meshiimg/{{$meshilog->img_path}}" alt="Card image cap">
          @endisset

          <?php
          $likeClass = 'fas';
          $likeDo ='いいねを取り消す';
          if(is_null($meshilog->meshilog_id)){
            $likeClass = 'far';
            $likeDo ='いいね';
          }
          ?>
          <div class="card-body">
            <span class="like" data-meshilog-id="{{$meshilog->id}}">
              <i class="text-danger {{$likeClass}} fa-heart"><small>{{$meshilog->like_sum}}</small></i>
              <p class="arrow_box">{{$likeDo}}</p>
            </span>
          </div>

        </div>
      @endforeach
      </div>
    </div>
  </div>
</div>
@endsection

@section('foot')
<script type="text/javascript" src="/js/likes/likeAddTake.js"></script>
@endsection('foot')
