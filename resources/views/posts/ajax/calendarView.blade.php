@foreach ($dates as $date)
@if ($date->day->dayOfWeek == 0)
<div class="row">
@endif
  <?php
  $bgSecondary = '';
  if($date->day->month != $currentMonth){
    $bgSecondary = 'bg-secondary';
  }
  ?>
  <div class="col {{$bgSecondary}} border-dark border calendar-cell">

    {{-- Day(日付)の表示--}}
    <p class="text-right mb-0">{{ $date->day->day }}日</p>

    {{--Post(データ）の表示--}}
    @isset($date->post)
      @isset($date->post->img_path)
        <img width="100%" src="/storage/meshiimg/{{$date->post->img_path}}">
      @endisset
      @empty($date->post->img_path)
        {{$date->post->title}}
      @endempty
    @endisset
    <a class="modal-open" data-date="{{$date->day->format('Y-m-d')}}" data-user-id="{{$userId}}"></a>
  </div>

@if ($date->day->dayOfWeek == 6)
</div>
@endif
@endforeach
