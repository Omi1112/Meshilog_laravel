@extends('layouts.app')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection


@section('content')
<div class="container">
  <div class="row justify-content-center">
    <table class="table table-bordered">
      <thead>
        <tr>
          {{$currentMonth}}月
        </tr>
      </thead>
      <thead>
        <tr>
          @foreach (['日', '月', '火', '水', '木', '金', '土'] as $dayOfWeek)
          <th class="calTh">{{ $dayOfWeek }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @foreach ($dates as $date)
        @if ($date->day->dayOfWeek == 0)
        <tr>
        @endif

          <td class="calTh
            @if ($date->day->month != $currentMonth)
            bg-secondary
            @endif
          ">
          <a style="display:block;" class="modal-open" data-date="{{$date->day->format('Y-m-d')}}" data-user-id="{{$userId}}">

            {{-- Day(日付)の表示--}}
            {{ $date->day->day }}

            {{--Post(データ）の表示--}}
            @isset($date->post)
              @isset($date->post->img_path)
                <br><img width="100%" src="/storage/meshiimg/{{$date->post->img_path}}">
              @endisset
              @empty($date->post->img_path)
                <br>{{$date->post->title}}
              @endempty
            @endisset
          </a>
          </td>

        @if ($date->day->dayOfWeek == 6)
        </tr>
        @endif
        @endforeach
      </tbody>
    </table>
  </div>
</div>
<div id="modal-bg">
  <div id="modal-main"></div>
</div>
@endsection

@section('foot')
<script type="text/javascript" src="/js/posts/modal.js"></script>
@endsection
