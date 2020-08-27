<?php

require('function.php');
debug('ログインページ');

require('loginauth.php');


// ログイン処理
if (!empty($_POST)) {
  debug('ログインでpostがありました');

  // POSTの内容
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  
  if(!empty($_POST['keep-login'])){
    // ログイン保持
    $keep_login = true;

  }else{
    $keep_login = false;
  }


  // バリデーション
  noEntered($email, 'email');
  noEntered($pass, 'pass');

  if (empty($err_msg)) {

    validEmail($email, 'email');
    validHankaku($pass, 'pass');


    if (empty($err_msg)) {
      
      validMax($pass, 'pass');
      validMin($pass, 'pass');

      if (empty($err_msg)) {
        debug('バリデーションok');

        try {
          $dbh = dbConnect();
          $sql = 'SELECT pass,id FROM users WHERE email = :email AND delete_flg = 0';
          $data = array(':email' => $email);

          $stmt = queryPost($dbh, $sql, $data);
          debug('try catchの中の$stmt' . print_r($stmt, true));
          
          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          debug('try catchの中の$result'.print_r($result,true));

          // 入力されたパスワードとDBから出したパスワードが合っていればログイン
          if(!empty($result) && password_verify($pass,array_shift($result))){

            $_SESSION['login_date'] = time();

            $session_limit = 60 * 60;
            
            // ログイン保持にチェックあるかないか
            if($keep_login){
              debug('ログイン保持あり');
              $_SESSION['login_limit'] = $session_limit*24*30;

            }else{
              debug('ログイン保持なし');
              $_SESSION['login_limit'] = $session_limit;
            }

           //  DBからとってきたデータでidが中に残っているので取り出してセッションへ
            $_SESSION['user_id'] = array_shift($result);
            
            header('Location:choice.php');

          }else{
            debug('パスワード誤り');
            $err_msg['common'] = ERR_PASS_EMAIL;

          }


        } catch (Exception $e) {
          error_log('ログインでエラー発生'.$e->getMessage());
          $error_log['common'] = ERR_COMMON;
        }
      }
    }
  }
}

?>

<?php 
$siteTitle = 'ログイン';
require('head.php'); ?>

<body>

  <?php require('header.php'); ?>

  <section>

    <form class="form form-login" action="" method="post">
      <h1 class='title'>ログイン</h1>

      <div class="err-msg"><?php if (!empty($err_msg['common'])) echo $err_msg['common']; ?></div>

      <div class="err-msg js-err-email"><?php if (!empty($err_msg['email'])) echo $err_msg['email']; ?></div>

      <label><span class='color-change-black'>メールアドレス</span>
        <input class="inputs js-input-email" type="email" name="email" placeholder="test@test.com" value="<?php if (!empty($_POST['email'])) echo $_POST['email']; ?>">
      </label>



      <div class="err-msg js-err-pass"><?php if (!empty($err_msg['pass'])) echo $err_msg['pass']; ?></div>

      <label class="label"><span class='color-change-black'>パスワード</span>
        <input class="inputs js-input-pass" type="password" name="pass">
      </label>

      <label>
        <div class="err-msg js-err-repass"><?php if (!empty($err_msg['repass'])) echo $err_msg['repass']; ?></div>
      </label>

      <label>
        <input type="checkbox" name="keep-login">
        <span class='color-change-black'>次回からログインを省略</span>
      </label>
    
        <input class="btn" type="submit" name="submit" value="ログイン">
  
    </form>

  </section>

  <?php
  require('footer.php');
  ?>