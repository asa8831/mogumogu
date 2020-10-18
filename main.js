

$(function () {

  // バリデーション
  const NO_ENTERED = '入力必須事項です';
  const ERR_PHONE = '電話番号の形式で入力してください';
  const MAX_LENGTH = '入力最大数を超えています';
  const MIN_LENGTH = '6文字以上で入力をしてください';
  const ERR_EMAIL = 'emailの形式で入力をしてください';
  const ERR_HAN = '半角英数字で入力してください';
  const ERR_REPASS = '再入力はパスワードと同じものを入力してください';

  const min = 6;
  const max = 255;
  const maxtextarea = 500;


  // 新規/ログインバリデーション
  // メール
  $('#js-input-email').blur(function () {

    if ($(this).val().length == 0) {
      $('#js-err-email').text(NO_ENTERED);

    } else if (!$(this).val().match(/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/)) {
      $('#js-err-email').text(ERR_EMAIL);

    } else {
      $('#js-err-email').text('');
    }
  });

  // パスワード
  $('#js-input-pass').blur(function () {

    if ($(this).val().length == 0) {
      $('#js-err-pass').text(NO_ENTERED);

    } else if ($(this).val().length > max) {
      $('#js-err-pass').text(MAX_LENGTH);

    } else if ($(this).val().length < min) {
      $('#js-err-pass').text(MIN_LENGTH);

    } else if (!$(this).val().match(/^[a-zA-Z0-9]+$/)) {
      $('#js-err-pass').text(ERR_HAN);

    } else {
      $('#js-err-pass').text('');

    }

  });

  // パスワード再入力
  $('#js-input-repass').blur(function () {

    if ($(this).val().length == 0) {
      $('#js-err-repass').text(NO_ENTERED);

    } else {
      $('#js-err-repass').text('');
    }
  });


  // info.phpバリデーション
  $('#js-input-user-name').blur(function () {
    if ($(this).val().length > max) {
      $('#js-err-user-name').text(MAX_LENGTH);
    } else {
      $('#js-err-user-name').text('');
    }

  });

  $('#js-input-phone').blur(function () {

    if (!$(this).val().match(/0\d{1,4}\d{1,4}\d{4}/)) {
      $('#js-err-phone').text(ERR_PHONE);

    } else if ($(this).val().length > max) {
      $('#js-err-phone').text(MAX_LENGTH);

    } else {
      $('#js-err-phone').text('');

    }

  });


  $('#js-input-address').blur(function () {
    if ($(this).val().length > max) {
      $('#js-err-address').text(MAX_LENGTH);
    }
  });

  $('#js-input-b-hours').blur(function () {
    if ($(this).val().length > max) {
      $('#js-err-b-hours').text(MAX_LENGTH);
    }
  });

  $('#js-input-holiday').blur(function () {
    if ($(this).val().length > max) {
      $('#js-err-holiday').text(MAX_LENGTH);
    }
  });

  $('#js-input-total-seats').blur(function () {
    if ($(this).val().length > max) {
      $('#js-err-total-seats').text(MAX_LENGTH);
    }
  });

  // pos.phpバリデーション
  $('#js-input-title').blur(function () {

    if ($(this).val().length == 0) {
      $('#js-err-title').text(NO_ENTERED);

    } else if ($(this).val().length > max) {
      $('#js-err-title').text(MAX_LENGTH);

    } else {
      $('#js-err-title').text('');
    }

  });

  $('#js-textarea').blur(function () {

    if ($(this).val().length == 0) {
      $('#js-err-textarea').text(NO_ENTERED);

    } else if ($(this).val().length > maxtextarea) {
      $('#js-err-textarea').text(MAX_LENGTH);

    } else {
      $('#js-err-textarea').text('');
    }

  });

  $('#js-input-display-limit').blur(function () {

    if ($(this).val().length == 0) {
      $('#js-err-display-limit').text(NO_ENTERED);

    } else if ($(this).val().length > max) {
      $('#js-err-display-limit').text(MAX_LENGTH);

    } else {
      $('#js-err-display-limit').text('');
    }

  });


  // ライブプレビュー
  $('.js-pic-file').on('change', function (e) {

    const file = this.files[0];
    fileReader = new FileReader();
    $img = $(this).siblings('.js-prev-img');

    fileReader.onload = function (event) {

      $img.attr('src', event.target.result).show();
    };

    fileReader.readAsDataURL(file);
  });
});

// 文字カウンター
window.addEventListener('DOMContentLoaded', function () {
  const textArea = document.getElementById('js-textarea');

  textArea.addEventListener('keyup', function () {
    const max = 500;
    const count = this.value.length;
    const counterArea = document.getElementById('js-textarea-counter');
    counterArea.innerText = max - count;

  });
});




