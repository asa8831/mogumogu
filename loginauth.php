<?php
if (!empty($_SESSION['login_date'])) {

  if ($_SESSION['login_date'] + $_SESSION['login_limit'] < time()) {
    debug('期間外です、ログインページに遷移します');
    session_destroy();
    header('Location:login.php');
    exit;
    
  } else {
    debug('期間内です');
    $_SESSION['login_date'] = time();

    // 現在実行中のファイル名を取り出して、それがlogin.phpだった場合のみchoice.phpに遷移
    // すでにchoice.phpにいればchoice.phpには遷移しない
    if (basename($_SERVER['PHP_SELF']) === 'login.php') {
      debug('ログイン済みのため、会員画面に遷移します');
      header('Location:choice.php');
      exit;
    }
  }

} else {
  // login.php内でループを回避するための処理
  if (basename($_SERVER['PHP_SELF']) !== 'login.php') {
    debug('$_SERVER'.print_r($_SERVER,true));
    debug('未ログインです');
    header('Location:login.php');
    exit;
  }

}