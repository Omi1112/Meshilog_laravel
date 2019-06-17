$(function() {
    //アイコンをクリックしたら
    $(".like").click(function() {
        //いいねを実行
        var likeThis = $(this);
        var likeHeart = likeThis.find('.fa-heart')

        var flagAddTake ="add";
        if (likeHeart.hasClass("fas")) {
          flagAddTake = "take"
        }
        console.log(likeThis.data('meshilogId'));
        console.log(likeThis.hasClass("far"));
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:"POST",
          url:"/like/" + flagAddTake,
          data:{
            'meshilogId': likeThis.data('meshilogId')
          }
        })
        .done(function(data) {
          likeHeart.toggleClass("fas");
          likeHeart.toggleClass("far");
          likeHeart.find('small').text(data.likeSum);
          likeThis.find('.arrow_box').text(data.likeDo);
          console.log(data.likeDo);
        })
        .fail(function(data) {
            alert(data.responseJSON);
        });

    });
});
