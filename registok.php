<?php

require('function.php');
debug('登録完了画面');
require('loginauth.php');

?>

<?php
$siteTitle = '登録完了';
require('head.php'); ?>


<body>

  <?php require('header.php'); ?>

  <section class="choise-wrap">

    <form>

      <h1 class='title'>登録が完了しました。</h1>
      <p class='sentence'>3秒後に自動で登録選択画面へ戻ります。</p>
      
    </form>
    

  </section>

 <script>
   setTimeout(function(){
     window.location.href ='choice.php';
   },5*1000);

 </script>

  <?php
  require('footer.php');
  ?>