//modal
$(function() {
  getCalendar($('#current').data('getYear'), $('#current').data('getMonth'));

  $(".get-calendar").click(function() {
    getCalendar($(this).data('getYear'), $(this).data('getMonth'));
  });

  function getCalendar(year, month) {
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type:"POST",
      url:$('#next-url').attr('href'),
      data:{
        'year': year,
        'month': month
      }
    })
    .done(function(data) {
      $('#calendar-position').html(data.calendarData);
      $('#previous').data('getYear',data.previousYear);
      $('#previous').data('getMonth',data.previousMonth);
      $('#current').data('getYear',data.currentYear);
      $('#current').data('getMonth',data.currentMonth);
      $('#next').data('getYear',data.nextYear);
      $('#next').data('getMonth',data.nextMonth) ;
      $('#current').text(data.currentMonth + "月");
    })
    .fail(function(data) {
        alert(data.responseJSON);
    });
  }
});
