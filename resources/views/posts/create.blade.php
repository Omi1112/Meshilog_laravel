@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <form class="" action="{{action('PostController@store')}}" method="post" enctype="multipart/form-data">
        @csrf
          <div class="card-header">
            タイトル
            <input type="text" name="title" placeholder="enter title" value="{{old('title')}}">
            @if ($errors->has('title'))
              <span class="badge badge-danger">{{ $errors->first('title') }}</span>
            @endif

          </div>

          <div class="card-body">
            本文<br>
            <textarea name="body" placeholder="enter body">{{old('body')}}</textarea>

            @if ($errors->has('body'))
              <span class="badge badge-danger">{{ $errors->first('body') }}</span>
            @endif

            <hr>

            画像を投稿<br>
            <div class="form-image_url">
                <input type="file" name="img">
            </div>

            <hr>

            日付 <input type="date" name="meal_date" id="today"><br>
            タイミング
            @foreach(config('const.MEAL_TIMING') as $key => $value)
              <input type="radio" name="meal_timing" value="{{$key}}">{{$value}}
            @endforeach
            <input type="submit" value="送信" class="float-right mb-2 mt-2">
          </div>
        </form>

      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="/js/posts/create.js"></script>
@endsection
