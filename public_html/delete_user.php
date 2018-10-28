<?php

// 新規登録

require_once(__DIR__ . '/../config/config.php');

// // Autoログアウトの実装
$autologout = new MyApp\Controller\Auto_logout();
$autologout->run();

// ログインしているページはこの設定
$app = new MyApp\Controller\Delete_user();
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
      <?= "こんにちは " . h($app->me()->username) . " さん ("  . h($app->me()->email) .")" ; ?>
    </p>

    <h1>退会処理（この操作は元に戻せません）</h1>
    <form action="delete_user.php" method="post" id="delete_user">

      <!-- メール hidden -->
      <input type="hidden" name="email" value="<?= h($app->me()->email); ?>">

      <!-- 現在のパスワード -->
      <p>
        <input type="password" name="password_current" placeholder="current passwd">
      </p>
      <p class="err"><?= h($app->getErrors('delete_user')); ?></p>

      <!-- トークン -->
      <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">

      <!-- パスワード変更送信 -->
      <!-- <div class="btn" onclick="document.getElementById('delete_user').submit();">退会する</div> -->
      <div class="btn" onClick="delete_confirm();">退会する</div>


      <!-- キャンセル to index.php-->
      <p class="fs12"><a href="index.php">Cancel</a></p>


    </form>
  </div>
  <script>
  function delete_confirm() {
    res = confirm("本当に削除しますか？");
    if (res === true) {
      return document.getElementById('delete_user').submit();
    }
  }
  </script>
</body>
</html>
