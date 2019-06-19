@extends('layouts.userLayouts')

@section('subContent')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12">
      <div class="container">
        <div id="cards" class="row">

        </div>
        <div id="loading">
          Now Loading...
          <a id="next-url" href="{{action('UserController@getUserFollows', $thisUser->id)}}"></a>
        </div>
      </div>
    </div>


  </div>

</div>
</style>
@endsection

@section('subFoot')
<script type="text/javascript" src="/js/infinityScroll.js"></script>
@endsection
