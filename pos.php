<?php

require('function.php');
debug('投稿/修正画面');
sessionNow();


// エリアのセレクトボックス表示用
$getCategory = getCategory();


// ---------------------
// 画面表示＆画面保持
// ---------------------

// DBから投稿記事データをもってくる
function getPostData($users_id)
{
  debug('getPostData関数引数 $users_id:' . print_r($users_id, true));

  try {
    $dbh = dbConnect();
    $sql = 'SELECT pic1, pic2, pic3, title, post_at, area, display_limit, create_at, update_at FROM post_at WHERE users_id = :users_id';
    $data = array(':users_id' => $users_id);
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      return $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
      return false;
    }
  } catch (Exception $e) {
    error_log($e->getMessage());
  }
}

// DBからとってきた投稿のデータ
$dbData = getPostData($_SESSION['user_id']);

// $dbDataに中身が入っていれば以前にも投稿したことがある、入っていなければ初投稿
if (empty($dbData)) {
  debug('投稿が未登録です');
  $edit = 0;
} else {
  debug('投稿があります');
  $edit = true;
}


// ---------
// 投稿処理
// ---------

// 画像アップロード
function uploadImg($file, $key){
  debug('画像アップロード処理開始');
  debug('FILE情報：' . print_r($file, true));

  // ファイルの有無をチェック
  if (isset($file['error']) && is_int($file['error'])) {
    debug('③写真投稿があるかチェック' . print_r($file, true));

    try {
      switch ($file['error']) {
          // バリデーション
        case UPLOAD_ERR_OK:
          debug('写真のバリデーションOK');
          break;
        case UPLOAD_ERR_NO_FILE:
          throw new RuntimeException('ファイルが選択されていません');
        case UPLOAD_ERR_INI_SIZE:
          throw new RuntimeException('画像を5MB以下にしてください');
        case UPLOAD_ERR_FORM_SIZE:
          throw new RuntimeException('画像を5MB以下にしてください');
        default:
          throw new RuntimeException('アップロードエラー');

      }

      // ファイルの形式をチェック
      $type = @exif_imagetype($file['tmp_name']);
      if (!in_array($type, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF], true)) {
        throw new RuntimeException('画像形式が未対応です');
      }

      // 一時ファイルを正式な保存場所へ
      $path = 'img/' . sha1_file($file['tmp_name']) . image_type_to_extension($type);
      if (!move_uploaded_file($file['tmp_name'], $path)) {
        throw new RuntimeException('ファイル保存時にエラーが発生しました');
      }

      // パーミッション変更
      chmod($path, 0644);
      debug('④パーミッション変更後　$pass' . print_r($path, true));
      return $path;

    } catch (RuntimeException $e) {
      debug($e->getMessage());
      global $err_msg;
      $err_msg[$key] = $e->getMessage();
      debug('$err_msgの$key'.print_r($key,true));
    }
  }
}


// 投稿処理
if (!empty($_POST)) {
  debug('投稿画面でPOSTがありました');
  debug('POST情報：' . print_r($_POST, true));
  debug('FILE情報：' . print_r($_FILES, true));


  $title = $_POST['title'];
  $post_at = $_POST['post_at'];
  $area = $_POST['area'];
  $display_limit = $_POST['display_limit'];

  // 写真1
  if (!empty($_FILES['pic1']['name'])) {
    $pic1 = uploadImg($_FILES['pic1'], 'pic1');
    
  } else if (empty($_FILES['pic1']['name']) && !empty($dbData['pic1'])) {
    $pic1 = $dbData['pic1'];
    
  } else {
    $pic1 = '';
   
  }

  // 写真2
  if (!empty($_FILES['pic2']['name'])) {
    $pic2 = uploadImg($_FILES['pic2'], 'pic2');
   
  } elseif (empty($_FILES['pic2']['name']) && !empty($dbData['pic2'])) {
    $pic2 = $dbData['pic2'];
    
  } else {
    $pic2 = '';
  }

  // 写真3
  if (!empty($_FILES['pic3']['name'])) {
    $pic3 = uploadImg($_FILES['pic3'], 'pic3');
    
  } else if (empty($_FILES['pic3']['name']) && !empty($dbData['pic3'])) {
    $pic3 = $dbData['pic3'];
    
  } else {
    $pic3 = '';
  }



  // バリデーション
  // データDBに登録されていない場合は普通にバリデーション
  if (empty($dbData)) {

    noEntered($title, 'title');
    validMax($title, 'title');
    noEntered($post_at, 'post_at');
    validMax($post_at, 'post_at', $max = 500);
    noEntered($area, 'area');
    noEntered($display_limit, 'display_limit');
  } else {

    // データがある場合は、データと登録が異なる場合のみバリデーション
    if ($dbData['title'] !== $_POST['title']) {
      noEntered($title, 'title');
      validMax($title, 'title');
    }

    if ($dbData['post_at'] !== $_POST['post_at']) {
      noEntered($post_at, 'post_at');
      validMax($post_at, 'post_at', $max = 500);
    }

    if ($dbData['area'] !== $_POST['area']) {
      noEntered($area, 'area');
    }

    if ($dbData['display_limit'] !== $_POST['display_limit']) {
      noEntered($display_limit, 'display_limit');
    }
  }


  if (empty($err_msg)) {
    debug('バリデーションOK');


    try {
      $dbh = dbConnect();

      if ($edit) {
        debug('データ更新');

        $sql = 'UPDATE post_at SET pic1 = :pic1, pic2 = :pic2,  pic3 = :pic3, title = :title, post_at =:post_at, area = :area, display_limit = :display_limit WHERE users_id = :users_id';

        $data = array(':pic1' => $pic1, ':pic2' => $pic2, ':pic3' => $pic3, ':title' => $title, ':post_at' => $post_at, ':area' => $area, ':display_limit' => $display_limit, 'users_id' => $_SESSION['user_id']);
        debug('$dataの中の$pic1' . print_r($pic1, true));
        debug('$dataの中の$pic2' . print_r($pic2, true));
        debug('$dataの中の$pic3' . print_r($pic3, true));
        debug('$dataの中の$title' . print_r($title, true));
      } else {
        debug('データ新規作成');

        $sql = 'INSERT INTO post_at (pic1, pic2,  pic3, users_id, title, post_at, area, display_limit, create_at) VALUES (:pic1, :pic2, :pic3, :users_id, :title, :post_at, :area, :display_limit, :create_at)';

        $data = array(':pic1' => $pic1, ':pic2' => $pic2, ':pic3' => $pic3, ':users_id' => $_SESSION['user_id'], ':title' => $title, ':post_at' => $post_at, ':area' => $area, ':display_limit' => $display_limit, ':create_at' => date('Y-m-d H:i:s'));
      }

      $stmt = queryPost($dbh, $sql, $data);

      if ($stmt) {

        debug('投稿処理完了$stmt' . print_r($stmt, true));
        header('Location:registok.php');
      }
    } catch (Exception $e) {
      error_log($e->getMessage());
      $err_msg['common'] = ERR_COMMON;
    }
  }
}


?>

<?php
$siteTitle = '投稿画面';
require('head.php'); ?>

<body>

  <?php require('header.php'); ?>

  <section>

    <form action="" class="form-pos" method="post" enctype="multipart/form-data">

      <h1 class='title'>記事投稿</h1>

      <div class="err-msg"><?php if (!empty($err_msg['common'])) echo $err_msg['common']; ?></div>

      <!-- 写真部分 -->
      <span class='color-change-black'>※5MB以下の画像のみアップロード可能です。</span>
      <div class='pic-container'>

        <div class='pic-wrap'>
          <div class="err-msg pos-pic-err-msg">
            <?php if (!empty($err_msg['pic1'])) echo $err_msg['pic1'] ?>
          </div>
          <label class='pic-display'>
            <input type="hidden" name="MAX_FILE_SIZE" value="5242880">
            <input type="file" name="pic1" class='pic-file js-pic-file'>
            画像1(クリックして追加)
            <img src="<?php echo keepData('pic1'); ?>" class="prev-img js-prev-img" style='<?php if (empty(keepData('pic1'))) echo "display:none;" ?>'>
          </label>
          
        </div>

        <div class='pic-wrap'>
          <div class="err-msg pos-pic-err-msg">
            <?php if (!empty($err_msg['pic2'])) echo $err_msg['pic2'] ?>
          </div>
          <label class='pic-display'>
            <input type="hidden" name="MAX_FILE_SIZE" value="5242880">
            <input type="file" name="pic2" class='pic-file js-pic-file'>
            画像2(クリックして追加)
            <img src="<?php echo keepData('pic2'); ?>" class="prev-img js-prev-img" style='<?php if (empty(keepData('pic2'))) echo "display:none;" ?>'>
          </label>
        </div>

        <div class='pic-wrap'>
          <div class="err-msg pos-pic-err-msg">
            <?php if (!empty($err_msg['pic3'])) echo $err_msg['pic3'] ?>
          </div>
          <label class='pic-display'>
            <input type="hidden" name="MAX_FILE_SIZE" value="5242880">
            <input type="file" name="pic3" class='pic-file js-pic-file'>
            画像3(クリックして追加)
            <img src="<?php echo keepData('pic3'); ?>" class="prev-img js-prev-img" style='<?php if (empty(keepData('pic3'))) echo "display:none;" ?>'>
          </label>

        </div>

      </div>

      <!-- 記事タイトル -->
      <div class="err-msg" id="js-err-title">
        <?php if (!empty($err_msg['title'])) echo $err_msg['title']; ?>
      </div>
      <label class='pos-title color-change-black'>記事タイトル
        <input type="text" name="title" id='js-input-title' class='pos-input' value='<?php echo keepData('title'); ?>'>
      </label>

      <!-- 記事 -->
      <div class="err-msg" id="js-err-textarea">
        <?php if (!empty($err_msg['post_at'])) echo $err_msg['post_at']; ?>
      </div>
      <div>
        <textarea name="post_at" id="js-textarea" cols="30" rows="10" class='pos-textarea'><?php echo keepData('post_at'); ?></textarea>
      </div>
      <div class='pos-counter color-change-black'>
        <span id="js-textarea-counter">500</span>/500
      </div>

      <!-- エリア選択 -->
      <div class="err-msg">
        <?php if (!empty($err_msg['area'])) echo $err_msg['area']; ?>
      </div>
      <label class='pos-area'><span class='color-change-black'>エリア選択</span>

        <select name="area" class="pos-select-box">
          <option value="0" <?php if (keepData('area') == 0) {
                              echo 'selected';
                            } ?>>選択してください</option>

          <?php
          foreach ($getCategory as $key => $val) :
          ?>

            <option value="<?php echo $val['area_id']; ?>" <?php if (keepData('area') == $val['area_id']) {
                                                            echo 'selected';
                                                          } ?>>
              <?php echo $val['area_name']; ?>
            </option>

          <?php
          endforeach;
          ?>


        </select>

      </label>

      <!-- 表示終了 -->
      <div class="err-msg" id="js-err-display-limit">
        <?php if (!empty($err_msg['display_limit'])) echo $err_msg['display_limit']; ?>
      </div>
      <label><span class='color-change-black'>表示終了日</span>
        <input type="date" name="display_limit" id='js-input-display-limit' class="pos-select-box" value='<?php echo keepData('display_limit'); ?>'>
      </label>
      <span class='color-change-black'>※表示を終了する日付を入力してください（例: 2020/01/01）<br>表示終了日を過ぎた記事は自動で表示されなくなります。</span>


      <div>
        
        <input class='btn' type="submit" name="submit" onclick="return confirm('投稿してよろしいですか？')" value="投稿" style='margin-right:20px;'>
      </div>


    </form>

  </section>

  <?php
  require('footer.php');
  ?>