@extends('layouts.userLayouts')

@section('subContent')
<div class="container">
  <div class="justify-content-center">
    <div class="row">
      <div class="col border text-center get-calendar" id="previous" data-get-year="{{Carbon\Carbon::now()->subMonth()->year}}" data-get-month="{{Carbon\Carbon::now()->subMonth()->month}}">
        <<前月
      </div>
      <div class="col border text-center" id="current" data-get-year="{{Carbon\Carbon::now()->year}}" data-get-month="{{Carbon\Carbon::now()->month}}">
        {{Carbon\Carbon::now()->month}}月
      </div>
      <div class="col border text-center get-calendar" id="next" data-get-year="{{Carbon\Carbon::now()->addMonth()->year}}" data-get-month="{{Carbon\Carbon::now()->addMonth()->month}}">
        翌月>>
      </div>
    </div>
    <div class="row">
      @foreach (['日', '月', '火', '水', '木', '金', '土'] as $dayOfWeek)
      <div class="col border-dark border">{{ $dayOfWeek }}</div>
      @endforeach
    </div>
    <div id="calendar-position">

    </div>
  </div>
</div>
<div id="modal-bg">
  <div id="modal-main"></div>
</div>
<a id="next-url" href="{{action('PostController@getUserCalendar', $thisUser->id)}}"></a>
@endsection

@section('subFoot')
<script type="text/javascript" src="/js/posts/getCalendar.js"></script>
<script type="text/javascript" src="/js/posts/modal.js"></script>
@endsection
