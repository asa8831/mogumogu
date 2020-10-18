<?php
require('function.php');
debug('ログアウト処理中');

$_SESSION = array();

if(isset($_COOKIE[session_name()])){
  setcookie(session_name(),'',time()-42000,'/');
}

session_destroy();
header('Location:index.php');
exit;

?>