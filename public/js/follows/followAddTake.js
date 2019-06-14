$(function() {
    //アイコンをクリックしたら
    $(".follow").click(function() {

        //いいねを実行
        var followThis = $(this);

        var flagAddTake ="add";
        if (followThis.hasClass("btn-primary")) {
          flagAddTake = "take"
        }
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:"POST",
          url:"/home/follow/" + flagAddTake,
          data:{
            'followId': followThis.data('followId')
          }
        })
        .done(function(data) {
          followThis.toggleClass("btn-outline-primary");
          followThis.toggleClass("btn-primary");
          followThis.text(data.followDo);
        })
        .fail(function(data) {
            alert(data.responseJSON);
        });

    });
});
