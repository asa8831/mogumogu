<?php

require('function.php');
debug('index画面です');

// エリアデータ取得
$getCategory = getCategory();


// ------------------以下、検索で絞るためのロジック
// エリアのセレクトボックス
$area_id = (!empty($_GET['area_choice'])) ? $_GET['area_choice'] : '';
// ソートのセレクトボックス
$sort_id = (!empty($_GET['sort_id'])) ? $_GET['sort_id'] : '';

// 投稿データをすべて取得して検索に対応させる関数
function getPostList($area_id, $sort_id){
  debug('getPostList $area_id' . print_r($area_id, true));
  debug('getPostList $sort_id' . print_r($sort_id, true));

  try {
    $dbh = dbConnect();
    $sql = 'SELECT post_id, pic1, pic2, pic3, users_id, title, post_at, area, display_limit, create_at, update_at FROM post_at WHERE display_limit >= CURDATE()';

    //  エリアが指定されて検索された場合
    if (!empty($area_id)) {
      $sql .= ' AND area = ' . $area_id;
      debug('エリア指定　$sql'.print_r($sql,true));
    }

    // ソートが指定されて検索された場合
    if (!empty($sort_id)) {
      debug('ソート指定$sort_id:'.print_r($sort_id,true));
      
      
      if ($sort_id == 1) {
        debug('降順　新しい順');
        $sql .= ' ORDER BY update_at DESC';
        debug('エリア指定　$sql'.print_r($sql,true));

      } else if ($sort_id == 2) {
        debug('昇順　古い順');
        $sql .= ' ORDER BY update_at ASC';
        debug('エリア指定　$sql'.print_r($sql,true));
      }
    }

    $data = array();
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      return $stmt->fetchAll();
    } else {
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生:' . $e->getMessage());
  }
}

// -----------以下、データ表示関係

// 取得した投稿データをforeachで画面表示
$getPostData = getPostList($area_id, $sort_id);
debug('全ての投稿データ$getPostData' . print_r($getPostData, true));

?>

<?php
$siteTitle = 'TOP';
require('head.php'); ?>

<body>
  <?php require('header.php'); ?>
  <!-- トップバナー -->
  <img src="banner/top-banner.png" alt="" style="width: 100%; height: 50%;">

  <!-- サイドバー -->
  <div class="site-width top-wrap">

    <section class="sidebar">
      <form action="" method='get' class='select-form'>

        <h1 class="color-change-black">エリア</h1>
        <div>
          <span></span>
          <select name="area_choice" class="top-select-box">
            <option value="0" <?php if (keepData('area_choice', true) == 0) {
                                echo 'selected';
                              } ?>>選択してください</option>

            <?php
            foreach ($getCategory as $key => $val) :
            ?>

              <option value="<?php echo $val['area_id'] ?>" <?php if (keepData('area_choice', true) == $val['area_id']) {
                                                              echo 'selected';
                                                            } ?>>
                <?php echo $val['area_name']; ?>
              </option>

            <?php
            endforeach;
            ?>

          </select>
        </div>

        <h1>投稿日</h1>
        <div>
          <span></span>
          <select name="sort_id" class="top-select-box">
            <option value="0" <?php if (keepData('sort_id', true) == 0) {
                                echo 'selected';
                              } ?>>選択してください</option>
            <option value="1" <?php if (keepData('sort_id', true) == 1) {
                                echo 'selected';
                              } ?>>新しい順</option>
            <option value="2" <?php if (keepData('sort_id', true) == 2) {
                                echo 'selected';
                              } ?>>古い順</option>
          </select>
        </div>

        <input type="hidden" name="scroll_top" class="st">
        <input type="submit" value="検索" class='btn' style='margin-top: 40px;'>

      </form>
    </section>


    <!-- メイン -->
    <section class='top-main-wrap'>
      <?php
      if (!empty($getPostData)) :
        foreach ($getPostData as $key => $val) :
      ?>
          <a href="detail.php<?php echo '?post_id='.$val['post_id']; ?>" class='get-param-link'>

            <!-- 表示データ -->
            <div class="top-post-wrap">
              <!-- 写真 -->
              <div>
                <img src="<?php echo showImg(Sanitize($val['pic1'])); ?>" alt="" class="post-avatar">
              </div>

              <!-- 記事 -->
              <div>
                <p>投稿日:　<?php echo Sanitize($val['update_at']);?></p>
                <b><?php echo Sanitize($val['title']); ?></b>
                <!-- nl2br→改行してDBから出力 -->
                <p><?php echo nl2br(mb_substr(Sanitize($val['post_at']), 0, 80)); ?>....</p>
              </div>
            </div>
          </a>

      <?php
        endforeach;
      endif;
      ?>

    </section>
  </div>


  <?php
  require('footer.php');
  ?>