<?php

require('function.php');
debug('投稿記事詳細');
sessionNow();


// ---------------------
// 画面表示＆画面保持
// ---------------------

// どのデータが選択されたのか判別
$post_id = (!empty($_GET['post_id'])) ? $_GET['post_id'] : '';

// DBからとってきた投稿のデータ
$dbUserPostData = getUserPostData($post_id);
debug('getPostData $dbUserPostData' . print_r($dbUserPostData, true));

?>

<?php
$siteTitle = '店舗詳細';
require('head.php'); ?>

<body>

  <?php require('header.php'); ?>

  <section>

    <form action="" class="form-detail">


      <div class="err-msg"><?php if (!empty($err_msg['common'])) echo $err_msg['common']; ?></div>

      <!-- 写真部分 -->

      <div class='pic-container'>

        <div class='pic-wrap'>
          <div class="err-msg">
            <?php if (!empty($err_msg['pic1'])) echo $err_msg ?>
          </div>
          <label class='pic-display'>
            <img src="<?php echo showImg(Sanitize($dbUserPostData['pic1'])); ?>" class="detail-pic">
          </label>
        </div>

        <div class='pic-wrap'>
          <div class="err-msg">
            <?php if (!empty($err_msg['pic2'])) echo $err_msg ?>
          </div>
          <label class='detail-pic'>
            <img class="detail-pic" src="<?php echo showImg(Sanitize($dbUserPostData['pic2'])); ?>">
          </label>
        </div>
        <style>

        </style>

        <div class='pic-wrap'>
          <div class="err-msg">
            <?php if (!empty($err_msg['pic3'])) echo $err_msg ?>
          </div>
          <label class='pic-display'>
            <img src="<?php echo showImg(Sanitize($dbUserPostData['pic3'])); ?>" class="detail-pic">
          </label>
        </div>

      </div>

      <!-- 記事タイトル -->
      <div class='pos-title color-change-black' style='text-align: center;'>
        <b>＊＊<?php echo Sanitize($dbUserPostData['title']); ?>＊＊</b>
      </div>

      <!-- 記事 -->
      <div class='color-change-black'>
        <p><?php echo nl2br(Sanitize($dbUserPostData['post_at'])); ?></p>
      </div>

      <!-- 店舗基本情報 -->
      <table class="color-change-black">
        <tr>
          <th colspan="2">店舗基本情報</th>
        </tr>
        <tr>
          <th>店名</th>
          <td><?php echo Sanitize($dbUserPostData['users_name']); ?></td>
        </tr>
        <tr>
          <th>電話</th>
          <td><?php echo Sanitize($dbUserPostData['phone']); ?></td>
        </tr>
        <tr>
          <th>住所</th>
          <td><?php echo Sanitize($dbUserPostData['address']); ?></td>
        </tr>
        <tr>
          <th>営業時間</th>
          <td><?php echo Sanitize($dbUserPostData['b_hours']); ?></td>
        </tr>
        <tr>
          <th>定休日</th>
          <td><?php echo Sanitize($dbUserPostData['holiday']); ?></td>
        </tr>
        <tr>
          <th>総席数</th>
          <td><?php echo Sanitize($dbUserPostData['total_seats']); ?></td>
        </tr>

      </table>
    </form>

  </section>

  <?php
  require('footer.php');
  ?>