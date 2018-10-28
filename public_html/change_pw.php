<?php

// 新規登録

require_once(__DIR__ . '/../config/config.php');

// // Autoログアウトの実装
$autologout = new MyApp\Controller\Auto_logout();
$autologout->run();

// ログインしているページはこの設定
$app = new MyApp\Controller\Change_pw();
$app->run();

// // Autoログアウトの実装 ここではなし
// $autologout = new MyApp\Controller\Auto_logout();
// $autologout->run();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Change Password</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <div id="container">
    <!-- メイン -->
    <p class="userinfo">
      <?= "PWの変更ですね " . h($app->me()->username) . " さん ("  . h($app->me()->email) .")" ; ?>
    </p>

    <h1>パスワード変更</h1>
    <form action="change_pw.php" method="post" id="change_pw">

      <!-- メール hidden -->
      <input type="hidden" name="email" value="<?= h($app->me()->email); ?>">

      <!-- 現在のパスワード -->
      <p>
        <input type="password" name="password_current" placeholder="current passwd">
      </p>
      <!-- <p class="err"><?= h($app->getErrors('change_pw')); ?></p> -->

      <!-- 新規パスワード -->
      <p>
        <input type="password" name="password_new" placeholder="new password">
      </p>
      <!-- <p class="err"><?= h($app->getErrors('change_pw')); ?></p> -->

      <!-- 確認パスワード -->
      <p>
        <input type="password" name="password_new_check" placeholder="check password" onkeydown="enter_to_change_password();">
      </p>
      <p class="err"><?= h($app->getErrors('change_pw')); ?></p>

      <!-- トークン -->
      <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">

      <!-- パスワード変更送信 -->
      <div class="btn" onclick="click_to_change_password();">Change Passwd</div>

      <!-- キャンセル to index.php-->
      <p class="fs12"><a href="index.php">Cancel</a></p>

    </form>
  </div>
  <script src="js/functions.js"></script>
</body>
</html>
