@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <form class="" action="{{action('UserController@profileUpdate')}}" method="post" enctype="multipart/form-data">
        @csrf
          <div class="card-header">
            名前
            <input type="text" name="name" placeholder="enter name" value="{{old('name', Auth::user()->name)}}">
            @if ($errors->has('name'))
              <span class="badge badge-danger">{{ $errors->first('name') }}</span>
            @endif
          </div>

          <div class="card-body">
            アイコン<br>
            <img width="100%" src="/storage/profileimg/{{Auth::user()->img_path}}">

            <div class="form-image_url">
                <input type="file" name="img">
            </div>

            <input type="submit" value="更新" class="float-right mb-2 mt-2">
          </div>
        </form>

      </div>
    </div>
  </div>
</div>
@endsection
