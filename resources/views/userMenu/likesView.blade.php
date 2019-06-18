@extends('layouts.userLayouts')

@section('subContent')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12">
      <div id="cards" class="row">

      </div>
    </div>
  </div>
  <div id="loading">
    Now Loading...
    <a id="next-url" href="{{action('PostController@getUserLikes', $thisUser->id)}}"></a>
  </div>
</div>
@endsection

@section('subFoot')
<script type="text/javascript" src="/js/infinityScroll.js"></script>
<script type="text/javascript" src="/js/likes/likeAddTake.js"></script>
@endsection
