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

  </div>
@endforeach
