<?php

require('function.php');
debug('新規登録ページ');
sessionNow();


if (!empty($_POST)) {
  debug('新規登録でpostがありました');

  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $repass = $_POST['repass'];


  // バリデーション
  noEntered($email, 'email');
  noEntered($pass, 'pass');
  noEntered($repass, 'repass');

  if (empty($err_msg)) {

    validEmail($email, 'email');
    validHankaku($pass, 'pass');


    if (empty($err_msg)) {
      doubleEmail($email, 'email');

      validMax($pass, 'pass');
      validMin($pass, 'pass');
      validRepass($pass, $repass);

      if (empty($err_msg)) {
        debug('バリデーションok');

        try {

          $dbh = dbConnect();

          $sql = 'INSERT INTO users(email, pass, create_at, login_date) VALUES(:email, :pass, :create_at, :login_date)';

          $data = array(':email' => $email, ':pass' => password_hash($pass, PASSWORD_DEFAULT), ':create_at' => date('Y-m-d H:i:s'), ':login_date' => date('Y-m-d H:i:s'));

          $stmt = queryPost($dbh, $sql, $data);
          debug('try catchの中の$stmt' . print_r($stmt, true));

          // 新規登録の内容をセッションに入れサーバーに渡す
          $session_limit = 60 * 60;
          $_SESSION['login_limit'] = $session_limit;
          $_SESSION['login_date'] = time();
          $_SESSION['user_id'] = $dbh->lastInsertId();

          header('Location:choice.php');
          exit;
          
        } catch (Exception $e) {
          error_log('新規登録でエラー発生' . $e->getMessage());
          $error_log['common'] = ERR_COMMON;
        }
      }
    }
  }
}

?>

<?php
$siteTitle = '新規登録';
require('head.php'); ?>

<body>

  <?php require('header.php'); ?>

  <section>

    <form class="form form-new" action="" method="post">
      <h1 class='title'>新規登録</h1>

      <div class="err-msg"><?php if (!empty($err_msg['common'])) echo $err_msg['common']; ?></div>

      <div class="err-msg" id ="js-err-email"><?php if (!empty($err_msg['email'])) echo $err_msg['email']; ?></div>

      <label><span class='color-change-black'>メールアドレス</span>
        <input class="inputs" id="js-input-email" type="email" name="email" placeholder="test@test.com" value="<?php if (!empty($_POST['email'])) echo $_POST['email']; ?>">
      </label>


      <div class="err-msg" id="js-err-pass"><?php if (!empty($err_msg['pass'])) echo $err_msg['pass']; ?></div>

      <label class="label"><span class='color-change-black'>パスワード</span>
        <input class="inputs" id="js-input-pass" type="text" name="pass">
      </label>


      <div class="err-msg" id="js-err-repass"><?php if (!empty($err_msg['repass'])) echo $err_msg['repass']; ?></div>

      <label class="label"><span class='color-change-black'>パスワード再入力</span>
        <input class="inputs" id="js-input-repass" type="text" name="repass" placeholder="再度パスワードを入力してください">
      </label>

      <input class="btn" type="submit" name="submit" value="登録">

    </form>

  </section>

  <?php
  require('footer.php');
  ?>