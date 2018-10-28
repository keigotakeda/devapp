<?php

// 新規登録

require_once(__DIR__ . '/../config/config.php');

// ログインしているページはこの設定
$app = new MyApp\Controller\Register_premium();
$app->run();

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
      <?= h($app->me()->username) . " さん ("  . h($app->me()->email) .")" ; ?>
    </p>

    <h1>プレミアム登録</h1>
    <!-- プレミアム処理 -->
    <form action="register_premium.php" method="post" id="change_pw">
      <!-- メール hidden -->
      <input type="hidden" name="email" value="<?= h($app->me()->email); ?>">
      <!-- 現在のパスワード -->
      <p>
        <input type="password" name="password_current" placeholder="current passwd">
      </p>
      <p class="err"><?= h($app->getErrors('change_pw')); ?></p>
      <!-- トークン -->
      <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">

      <!-- プレミアム -->
      <input type="hidden" name="register" value="1">

      <!-- プレミアムになる -->
      <span class="col-md-12 margin"><button class="btn btn-default">プレミアム</button></span>
      <!-- <div class="btn" onclick="click_to_change_password();">プレミアム</div> -->
    </form>


    <h1>プレミアム解除</h1>
    <form action="register_premium.php" method="post" id="change_pw">
      <!-- メール hidden -->
      <input type="hidden" name="email" value="<?= h($app->me()->email); ?>">
      <!-- 現在のパスワード -->
      <p>
        <input type="password" name="password_current" placeholder="current passwd">
      </p>
      <p class="err"><?= h($app->getErrors('change_pw')); ?></p>

      <!-- トークン -->
      <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">

      <!-- 無料ユーザ -->
      <input type="hidden" name="register" value="0">

      <!-- 無料ユーザになる -->
      <!-- <div class="btn" onclick="click_to_change_password();">一般ユーザ</div> -->
      <span class="col-md-12 margin"><button class="btn btn-default">一般ユーザー</button></span>
    </form>

    <!-- キャンセル to index.php-->
    <p class="fs12"><a href="index.php">Cancel</a></p>

  </div>
  <script src="js/functions.js"></script>
</body>
</html>
