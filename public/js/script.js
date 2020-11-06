$(function () {
  $('#birth_date, #output_date, #donation_date').datetimepicker({
    format: 'YYYY/MM/DD',
    locale: 'ja'
  });

  // 生年月日に初期値をセット
  if ($('#birth_date').val() == '') {
    $('#birth_date').val(moment('1989-01-01').format("YYYY/MM/DD"));
  }
  if ($('#birth_date_sp').val() == '') {
    $('#birth_date_sp').val(moment('1989-01-01').format("YYYY-MM-DD"));
  }
  // 出力年月日、寄附年月日に現在日をセット
  let pc_current_date = moment().format("YYYY/MM/DD");
  if ($('#output_date').val() == '') {
    $('#output_date').val(pc_current_date);
  }
  if ($('#donation_date').val() == '') {
    $('#donation_date').val(pc_current_date);
  }
  let sp_current_date = moment().format("YYYY-MM-DD");
  if ($('#output_date_sp').val() == '') {
    $('#output_date_sp').val(sp_current_date);
  }
  if ($('#donation_date_sp').val() == '') {
    $('#donation_date_sp').val(sp_current_date);
  }

  // テストデータで埋めるボタンの処理
  // SP版の日付項目はセットする年月日がハイフンつなぎの必要がある模様
  $("#test").on("click", function(){
    $("form").find("input[type='text'], input[type='date'], textarea").each(function(input_index, input_item){
      let $input = $(input_item);
      $input.val($input.attr('placeholder'));
    });
    $("#radio_1").trigger('click');
    $("#check_one_0, #check_two_0").prop('checked', true);
  });

  // 非表示中のinput要素の値をpostしないようにする
  $("form").on("submit", function(){
    $('form input').prop("disabled", false);
    $('form input:hidden').prop("disabled", true);
  });

  // windowサイズ変更時に非表示中のinput要素のdisabled属性をいったん取る
  var timer = false;
  $(window).resize(function() {
      if (timer !== false) {
        clearTimeout(timer);
      }
      timer = setTimeout(function() {
        $('form input').prop("disabled", false);
      }, 200);
  });

  // エンターキーのsubmit無効化
  $("input").keydown(function(e) {
    if ((e.which && e.which === 13) || (e.keyCode && e.keyCode === 13)) {
        return false;
    } else {
        return true;
    }
  });
});