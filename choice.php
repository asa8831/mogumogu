<?php
require('function.php');
debug('登録選択画面');
require('loginauth.php');
?>
<?php 
$siteTitle = '登録選択';
require('head.php'); ?>

<body>

  <?php require('header.php'); ?>

  <section class="choice-wrap">
    
      <h1 class='title'>登録選択</h1>

      <div class="btn-choice-wrap">
        <input class="btn-choice" type="button" onclick="location.href='info.php'" value="店舗基本情報">
      
        <input class="btn-choice" type="button" onclick="location.href='pos.php'" value="記事投稿/修正">
      </div>

  </section>

<?php require('footer.php'); ?>
