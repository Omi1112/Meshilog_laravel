$(function(){
  more();
  var hi= $(document).height();
  console.log(hi);
  $(window).scroll(function() {
    var hi= $(document).height();
    console.log(hi);
    if ($(window).scrollTop() + $(window).height() == $(document).height()) {
          more();
    }
  });
  function more() {
    $('#loading').show();

    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type:"GET",
      url:$('#next-url').attr('href')
    })
    .done(function(data) {
      $(data.cardData).appendTo('#cards').hide().fadeIn(1000);
      $('#next-url').attr('href', data.nextPageUrl);
      console.log(data.nextPageUrl);
    })
    .fail(function(data) {
        alert(data.responseJSON);
    });
  }
});
