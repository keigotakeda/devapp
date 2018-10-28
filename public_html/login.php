<?php

// ログイン

require_once(__DIR__ . '/../config/config.php');

$app = new MyApp\Controller\Login();

$app->run();

//echo "login screen";
//exit;

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Log In</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <div id="container">
    <h1 class="text_center">ユーザログイン</h1>

    <form action="" method="post" id="login">

      <!-- ログイン メール -->
      <p>
        <input type="text" name="email" placeholder="email" value="<?= isset($app->getValues()->email) ? h($app->getValues()->email) : ''; ?>">
      </p>

      <!-- ログイン パスワード -->
      <!-- enter_to_submit()でボタンを押下せず送信可能 -->
      <p>
        <input type="password" name="password" placeholder="password" onkeydown="enter_to_login();">
      </p>
      <p class="err"><?= h($app->getErrors('login')); ?></p>

      <!-- 送信 -->
      <div class="btn" onclick="click_to_login()">Login</div>
      <!-- <p class="fs12"><a href="/signup.php">Sign Up</a></p> -->

      <!-- サインアップ -->
      <p class="fs12"><a href="signup.php">新規登録</a></p>
      <!-- 管理者ログインへ -->
      <p class="fs12"><a href="reminder_input.php">パスワードを忘れた</a></p>
      <!-- 管理者ログインへ -->
      <p class="fs12"><a href="admin_login.php">管理者ログイン</a></p>

      <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
    </form>
  </div>
  <script src="js/functions.js"></script>
</body>
</html>
