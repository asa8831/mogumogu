<?php
// ログを表示
// error_reporting(E_ALL);
// ini_set('log_errors', 'On');
ini_set('error_log', 'php.log');

// デバッグ
$debug_flg = true;
function debug($str)
{
  global $debug_flg;
  if (!empty($debug_flg)) {
    error_log('デバッグ:' . $str);
  }
}

// 現在セッションに何が入っているか
function sessionNow(){
  debug('$_SESSIONの中身:'.print_r($_SESSION,true));
}

// セッション関係
session_save_path('/var/tmp');
ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30);
ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30);
session_start();
session_regenerate_id();

// エラーメッセージ
define('NO_ENTERED', '入力必須事項です');
define('ERR_PHONE', '電話番号の形式で入力してください');
define('MAX_LENGTH', '入力最大数を超えています');
define('MIN_LENGTH', '6文字以上で入力をしてください');
define('ERR_EMAIL', 'emailの形式で入力をしてください');
define('ERR_HAN', '半角英数字で入力してください');
define('DOUBLE_EMAIL', 'そのemailはすでに登録されています');
define('ERR_REPASS','パスワードと再入力を一致させてください');
define('ERR_COMMON', 'エラーが発生しました');
define('ERR_PASS_EMAIL','パスワードかemailが誤っています');

// メッセージ格納用配列
$err_msg = array();

// サニタイズ
function Sanitize($str){
  return htmlspecialchars($str, ENT_QUOTES);
}

// バリデーション関数
function noEntered($str, $key){
  if ($str === '') {
    global $err_msg;
    debug('バリデーションエラー　noEntered');
    $err_msg[$key] = NO_ENTERED;
  }
}

function validPhone($str, $key){
  if (!preg_match('/0\d{1,4}\d{1,4}\d{4}/', $str)) {
    global $err_msg;
    debug('バリデーションエラー　validPhone');
    $err_msg[$key] = ERR_PHONE;
  }
}

function validMax($str, $key, $max = 255){
  if (mb_strlen($str) > $max) {
    global $err_msg;
    debug('バリデーションエラー　validMax');
    $err_msg[$key] = MAX_LENGTH;
  }
}

function validMin($str, $key, $min = 6){
  if (mb_strlen($str) < $min) {
    global $err_msg;
    debug('バリデーションエラー　validMin');
    $err_msg[$key] = MIN_LENGTH;
  }
}

function validEmail($str, $key){
  if (!preg_match("/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/", $str)) {
    global $err_msg;
    debug('バリデーションエラー　validEmail');
    $err_msg[$key] = ERR_EMAIL;
  }
}

function validHankaku($str, $key){
  if (!preg_match("/^[a-zA-Z0-9]+$/", $str)) {
    global $err_msg;
    debug('バリデーションエラー　validHankaku');
    $err_msg[$key] = ERR_HAN;
  }
}

function validRepass($pass,$repass){
  if($pass !== $repass){
    global $err_msg;
    debug('バリデーションエラー　validRepass');
    $err_msg['pass'] = ERR_REPASS;
  }
}


function doubleEmail($email, $key){
  debug('doubleEmail 重複バリデーション開始');
  
  if (!empty($_POST['email'])) {
    
    global $err_msg;

    try {
      $dbh = dbConnect();
      $sql = 'SELECT count(*) FROM users WHERE email = :email';
      $data  = array(':email' => $email);

      $stmt = queryPost($dbh, $sql, $data);

      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      debug('$result doubleEmail'.print_r($result,true));

      if (!empty(array_shift($result))) {
        debug('doubleEmail メールアドレスが重複');
        $err_msg[$key] = DOUBLE_EMAIL;
      }

    } catch (Exception $e) {
    
      error_log('doubleEmail エラーが発生しました' . $e->getMessage());
      $err_log['common'] = ERR_COMMON;
    }
  }
}


// DB接続
function dbConnect(){
  debug('dbConnect開始');

  try {
    $dsn = 'mysql:dbname=mogumogu;host=localhost;charset=utf8';
    $username = 'root';
    $pass = 'root';
    $option = array(
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );
    // debug('dbConnectの中の$dsn'.print_r($dsn,true));
    // debug('dbConnectの中の$username'.print_r($username,true));
    // debug('dbConnectの中の$pass'.print_r($pass,true));
    // debug('dbConnectの中の$option'.print_r($option,true));

    $dbh = new PDO($dsn, $username, $pass, $option);
    // debug('dbConnectの中の$dbh:'.print_r($dbh,true));

    return $dbh;
  } catch (Exception $e) {
    
    error_log('dbConnectでエラーが発生しました' . $e->getMessage());
    $err_log['common'] = ERR_COMMON;
  }
}

// DB接続後クエリ生成
function queryPost($dbh, $sql, $data){
  debug('queryPost開始');

  $stmt = $dbh->prepare($sql);
 
  if (!$stmt->execute($data)) {
    debug('queryPost クエリの生成に失敗しました');
    $err_msg['common'] = ERR_COMMON;
    return false;
  }

  debug('queryPost引数$stmt クエリの生成に成功'.print_r($stmt,true));
  return $stmt;
}

// セッションのユーザーIDと合致するユーザーデータ取得
function getUserData($user_id){

  try{

    $dbh = dbConnect();
    $sql = 'SELECT users_name,phone,address, b_hours,holiday,total_seats FROM users WHERE id=:id AND delete_flg = 0';
    $data = array(':id' => $user_id);
    $stmt = queryPost($dbh,$sql,$data);
    
    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }

  }catch(Exception $e){
    error_log($e->getMessage());
    global $err_msg;
    $err_msg['common'] = ERR_COMMON;
  }
}

// post_atテーブルのユーザーIDと合致するユーザーデータをusersテーブルから取得
function getUserPostData($post_id){

  try{
    $dbh = dbConnect();
    $sql = 'SELECT p.pic1, p.pic2, p.pic3, p.title, p.post_at, p.area, u.users_name, u.phone, u.address, u.b_hours, u.holiday, u.total_seats FROM post_at AS p LEFT JOIN users AS u ON p.users_id = u.id WHERE post_id = :post_id';
    $data = array(':post_id' => $post_id);

    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      return $stmt -> fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }

  }catch(Exception $e){
    error_log($e->getMessage());
    global $err_msg;
    $err_msg['common'] = ERR_COMMON;
  }

}

// 入力保持
function keepData($str,$get_flg = false){
  debug('入力保持関数');
  debug('keepData 引数$str:'.print_r($str,true));


  if($get_flg){
    $method = $_GET;
  }else{
    $method = $_POST;
  }

  global $dbData;

  // パターンに合わせて入力保持するための場合分け
  if(!empty($dbData)){

    if(!empty($err_msg[$str])){

      if(isset($method[$str])){
        return Sanitize($method[$str]);

      }else{
        return Sanitize($dbData[$str]);
      }

    }else{

      if(isset($method[$str]) && $method[$str] !== $dbData[$str]){
        return Sanitize($method[$str]);

      }else{
        return Sanitize($dbData[$str]);
      }
    }

  }else{
    if(isset($method[$str])){
      return Sanitize($method[$str]);
    }
  }
}

// エリアデータを取得
function getCategory(){

  try {
    $dbh = dbConnect();
    $sql = 'SELECT area_id, area_name FROM category';
    $data = array();
    $stmt = queryPost($dbh, $sql, $data);

    if ($stmt) {
      return $stmt->fetchAll();
    } else {
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生' . $e->getMessage());
  }
}

// 写真が登録されなかった場合に「no-image」と書かれた画像を表示
function showImg($path){
  debug('showImg関数開始' . print_r($path, true));
  if (empty($path)) {
    return 'pic/no-image.png';
  } else {
    return $path;
  }
}
