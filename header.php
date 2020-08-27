<header>
    <div id='header-container' class="site-width">
      <h1 class="header-title"><a href = "index.php"> mogumogu</a></h1>

          <?php if (empty($_SESSION['user_id'])) {
            debug('$_SESSIONの中身'.print_r($_SESSION,true));
          ?>
      <nav class="header-nav header-nav-before-login">
        <ul>
            <li><a href="index.php">TOP</a></li>
            <li><a href="login.php">ログイン</a></li>
            <li><a href="new.php">新規登録</a></li>

          <?php } else { ?>

      <nav class="header-nav header-nav-after-login">
        <ul>
            <li><a href="index.php">TOP</a></li>
            <li><a href="logout.php" onclick="return confirm('ログアウトしてよろしいですか？')">ログアウト</a></li>
            <li><a href="info.php">店舗基本情報</a></li>
            <li><a href="pos.php">記事投稿/修正</a></li>
            <li><a href="withdrawal.php">退会</a></li>
            
          <?php
          } ?>
        </ul>
      </nav>
    </div>
  </header>