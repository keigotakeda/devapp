<?php

// *** 管理者ページ ***

// データベースなので変更なし
require_once(__DIR__ . '/../config/config.php');

//
$app = new MyApp\Controller\Admin_login();

$app->run();

//echo "login screen";
//exit;

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Admin_login</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <div id="container">
    <h1 class="text_center">管理者ログイン</h1>
    <form action="" method="post" id="login">

      <!-- 管理者ネーム -->
      <p>
        <input type="text" name="admin" placeholder="admin_name" value="<?= isset($app->getValues()->admin) ? h($app->getValues()->admin) : ''; ?>">
      </p>

      <!-- ログイン パスワード -->
      <!-- enter_to_submit()でボタンを押下せず送信可能 -->
      <p>
        <input type="password" name="password" placeholder="password" onkeydown="enter_to_login();">
      </p>
      <p class="err"><?= h($app->getErrors('empty_post')); ?></p>
      <p class="err"><?= h($app->getErrors('unmatch')); ?></p>

      <!-- 送信 -->
      <div class="btn" onclick="click_to_login();">Login</div>

      <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">

      <p class="fs12"><a href="login.php">ユーザログイン</a></p>
    </form>
      
  </div>
  <script src="js/functions.js"></script>
</body>
</html>
