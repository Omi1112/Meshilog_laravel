@extends('layouts.app')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12">
      <div class="container">
        <div class="row justify-content-between">
        @foreach($users as $user)
          <div class="card col-lg-5 mb-3 container">
            <div class="card-header row justify-content-between">
              <div class="col-2">
                <img width="32px" height="32px" src="/storage/profileimg/{{$user->img_path}}" alt="Card image cap">
              </div>
              <div class="col-6">
                {{$user->name}}
              </div>

              <?php
              $followClass = 'btn-primary';
              $followDo ='フォロー中';
              if(is_null($user->follow_id)){
                $followClass = 'btn-outline-primary';
                $followDo ='フォローする';
              }
              ?>

              <div class="col-4">
                <button type="button" class="btn {{$followClass}} follow" data-follow-id="{{$user->id}}">{{$followDo}}</button>
              </div>
            </div>
            <div class="card-body row">
              {{$user->name}}
            </div>

  {{--
            <div class="card-body">
              <span class="like" data-meshilog-id="{{$meshilog->id}}">
                <i class="text-danger {{$likeClass}} fa-heart"><small>{{$meshilog->like_sum}}</small></i>
                <p class="arrow_box">{{$likeDo}}</p>
              </span>
            </div>
  --}}
          </div>
        @endforeach
        </div>

      </div>
    </div>
  </div>
</div>
</style>
@endsection

@section('foot')
<script type="text/javascript" src="/js/follows/followAddTake.js"></script>
@endsection('foot')
