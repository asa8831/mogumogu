<?php
require('function.php');
debug('退会画面');
require('loginauth.php');

if(!empty($_POST)){
  debug('退会画面でPOSTがありました');

  if(!empty($_POST)){

  try{
    $dbh = dbConnect();

    $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :users_id';
    $sql2 = 'UPDATE post_at SET delete_flg = 1 WHERE users_id = :users_id';
    
    $data = array(':users_id' => $_SESSION['user_id']);
    
    $resultUser = queryPost($dbh, $sql1,$data);
    $resultPost = queryPost($dbh, $sql2, $data);

    if($resultUser && $resultPost){
      $_SESSION = array();
      if(isset($_COOKIE[session_name()])){
        setcookie(session_name(),'',time()-42000,'/');
      }
      session_destroy();
      header('Location:index.php');
      exit;
    }
    

  }catch(Exception $e){
    error_log($e->getMessage());
    $err_log['common'] = ERR_COMMON;
  }
 }
}
?>


<?php 
$siteTitle = '退会';
require('head.php'); ?>

<body>

  <?php require('header.php'); ?>

  <section class="choice-wrap">

    <form method='post'>

      <h1 class='title'>退会しますか？</h1>
      <div class='err_msg'><?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?></div>
      
      <div class="btn-choice-wrap">
        <input class="btn-choice" type="submit" name="submit" onclick="" value="退会">
      </div>
      <div class="btn-choice-wrap">
        <input class="btn-choice" type="button" onclick="location.href='choice.php'" value="戻る">

      </div>
      
    </form>
    

  </section>

<?php require('footer.php'); ?>
