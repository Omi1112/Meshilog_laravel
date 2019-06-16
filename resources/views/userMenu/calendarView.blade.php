@extends('layouts.userLayouts')

@section('subContent')
<div class="container">
  <div class="justify-content-center">
    <div class="row">
      <div class="col">
        {{$currentMonth}}月
      </div>
    </div>
    <div class="row">
      @foreach (['日', '月', '火', '水', '木', '金', '土'] as $dayOfWeek)
      <div class="col border-dark border">{{ $dayOfWeek }}</div>
      @endforeach
    </div>
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
        <a class="modal-open" data-date="{{$date->day->format('Y-m-d')}}" data-user-id="{{$thisUser->id}}"></a>
      </div>

    @if ($date->day->dayOfWeek == 6)
    </div>
    @endif
    @endforeach
  </div>
</div>
<div id="modal-bg">
  <div id="modal-main"></div>
</div>
@endsection

@section('subFoot')
<script type="text/javascript" src="/js/posts/modal.js"></script>
@endsection
