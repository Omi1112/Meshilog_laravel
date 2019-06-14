@foreach($meshilogs as $meshilog)
  <div class="mb-5">
    <div class="card-header border-top border-bottom">
      <h2>{{$meshilog->title}}</h2>
    </div>

    <div class="card-body">
      {{$meshilog->body}}
    </div>

    @isset($meshilog->img_path)
      <img class="card-img-top" src="/storage/meshiimg/{{$meshilog->img_path}}" alt="Card image cap">
    @endisset

  </div>
@endforeach
