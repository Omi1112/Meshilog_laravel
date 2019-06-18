@foreach($meshilogs as $meshilog)
<div class="col-md-4 mb-2">

  <div class="card">
    <div class="card-header bg-primary user-header">
      <img width="32px" height="32px" src="/storage/profileimg/{{$meshilog->user_img_path}}">
      {{$meshilog->user_name}}
      <a href="{{action('UserMenuController@postsView', $meshilog->user_id)}}"></a>
    </div>
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
</div>

@endforeach
