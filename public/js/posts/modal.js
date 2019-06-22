//modal
$(function() {
    //カレンダーをクリックしたら
    $("body").on("click", ".modal-open", function() {
        //モーダルウィンドウを表示
        $("#modal-bg,#modal-main").fadeIn("first");

        //モーダルウィンドウの中身取得
        var modalOpen = $(this);
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:"POST",
          url:"/posts/dayPosts",
          data:{
            'userId': modalOpen.data('userId'),
            'date': modalOpen.data('date')
          }
        })
        .done(function(data) {
          $("#modal-main").html(data);
        })
        .fail(function(data) {
            alert(data.responseJSON);
        });

        //画面のどこかをクリックしたらモーダルを閉じる
        $("#modal-bg,#modal-main").click(function() {
            $("#modal-main,#modal-bg").fadeOut("slow", function() {
                //挿入した<div id="modal-bg"></div>を削除
                // $('#modal-bg').remove();
            });
        });

        //画面の左上からmodal-mainの横幅・高さを引き、その値を2で割ると画面中央の位置が計算できます
        // $(window).resize(modalResize);

        function modalResize() {

            var w = $(window).width();
            var h = $(window).height();

            var cw = $("#modal-main").outerWidth();
            var ch = $("#modal-main").outerHeight();

            //取得した値をcssに追加する
            $("#modal-main").css({
                "left": ((w - cw) / 2) + "px",
                "top": ((h - ch) / 2) + "px"
            });
        }
    });
});
