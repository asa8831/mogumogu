<?php

require('function.php');
debug('店舗基本情報ページ');
sessionNow();

// 画面表示
$dbData = getUserData($_SESSION['user_id']);


// 登録
if (!empty($_POST)) {

  $users_name = $_POST['users_name'];
  $phone = $_POST['phone'];
  $address = $_POST['address'];
  $b_hours = $_POST['b_hours'];
  $holiday = $_POST['holiday'];
  $total_seats = $_POST['total_seats'];

  if ($dbData['users_name'] !== $_POST['users_name']) {
    validMax($users_name, 'users_name');
  }
  if ($dbData['phone'] !== $_POST['phone']) {
    validMax($phone, 'phone');
    validPhone($phone, 'phone');
  }
  if ($dbData['address'] !== $_POST['address']) {
    validMax($address, 'address');
  }
  if ($dbData['b_hours'] !== $_POST['b_hours']) {
    validMax($b_hours, 'b_hours');
  }
  if ($dbData['holiday'] !== $_POST['holiday']) {
    validMax($holiday, 'holiday');
  }
  if ($dbData['total_seats'] !== $_POST['total_seats']) {
    validMax($total_seats, 'total_seats');
  }

  if (empty($err_msg)) {


    try {

      $dbh = dbConnect();
      $sql = 'UPDATE users SET users_name = :users_name, phone = :phone, address = :address, b_hours = :b_hours, holiday = :holiday, total_seats = :total_seats WHERE id = :id';
      $data = array(':users_name' => $users_name, ':phone' => $phone, ':address' => $address, ':b_hours' => $b_hours, ':holiday' => $holiday, ':total_seats' => $total_seats, ':id' => $_SESSION['user_id']);

      $stmt = queryPost($dbh, $sql, $data);
      if ($stmt) {
        header('Location:registok.php');
        exit;
      } else {
        debug('エラーが発生しました');
        $err_msg['common'] = ERR_COMMON;
      }
    } catch (Exception $e) {
      error_log('エラー発生' . $e->getMessage());
    }
  }
}

?>

<?php
$siteTitle = '店舗基本情報';
require('head.php'); ?>

<body>

  <?php require('header.php'); ?>

  <section>

    <form class="form form-info" action="" method="post">
      <h1 class='title'>店舗基本情報</h1>
      <p>記事を投稿した際に店舗基本情報として表示されます。</p>

      <div class="err-msg"><?php if (!empty($err_msg['common'])) echo $err_msg['common']; ?></div>

      <!-- 店名 -->
      <div class="err-msg" id="js-err-user-name"><?php if (!empty($err_msg['users_name'])) echo $err_msg['users_name']; ?></div>

      <label><span class='color-change-black'>店舗名</span>
        <input class="inputs" id="js-input-user-name" type="text" name="users_name" value="<?php echo keepData('users_name'); ?>">
      </label>

      <!-- 電話 -->
      <div class="err-msg" id="js-err-phone" style='height:20px;'><?php if (!empty($err_msg['phone'])) echo $err_msg['phone']; ?></div>
      <label><span class='color-change-black'>電話番号 ※半角英数字ハイフンなしで入力</span>
        <input class="inputs" id="js-input-phone" type="text" name="phone" value="<?php echo keepData('phone'); ?>">
      </label>

      <!-- 住所 -->
      <div class="err-msg" id="js-err-address"><?php if (!empty($err_msg['address'])) echo $err_msg['address']; ?></div>
      <label><span class='color-change-black'>住所</span>
        <input class="inputs" id="js-input-address" type="text" name="address" value="<?php echo keepData('address'); ?>">
      </label>

      <!-- 営業時間 -->
      <div class="err-msg" id="js-err-b-hours"><?php if (!empty($err_msg['b_hours'])) echo $err_msg['b_hours']; ?></div>
      <label><span class='color-change-black'>営業時間</span>
        <input class="inputs" id="js-input-b-hours" type="text" name="b_hours" value="<?php echo keepData('b_hours'); ?>">
      </label>

      <!-- 定休日 -->
      <div class="err-msg" id="js-err-holiday"><?php if (!empty($err_msg['holiday'])) echo $err_msg['holiday']; ?></div>
      <label><span class="color-change-black">定休日</span>
        <input class="inputs" id="js-input-holiday" type="text" name="holiday" value="<?php echo keepData('holiday'); ?>">
      </label>

      <!-- 総席数 -->
      <div class="err-msg" id="js-err-total-seats"><?php if (!empty($err_msg['total_seats'])) echo $err_msg['total_seats']; ?></div>
      <label><span class='input-title'>総席数</span>
        <input class="inputs" id="js-input-total-seats" type="text" name="total_seats" value="<?php echo keepData('total_seats'); ?>">
      </label>


      <input class="btn" type="submit" name="submit" onclick="return confirm('登録してよろしいですか？')" value="登録">

    </form>

  </section>

  <?php
  require('footer.php');
  ?>