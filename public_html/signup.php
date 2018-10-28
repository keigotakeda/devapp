<?php

// 新規登録

require_once(__DIR__ . '/../config/config.php');

$app = new MyApp\Controller\Signup();

$app->run();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Sign Up</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <div id="container">
    <form action="" method="post" id="signup">

      <!-- サインイン メール -->
      <p>
        <input type="text" name="email" placeholder="email" value="<?= isset($app->getValues()->email) ? h($app->getValues()->email) : ''; ?>">
      </p>
      <p class="err"><?= h($app->getErrors('email')); ?></p>

      <!-- サインイン ユーザーネーム -->
      <p>
        <input type="text" name="username" placeholder="username">
      </p>
      <p class="err"><?= h($app->getErrors('username')); ?></p>

      <!-- サインイン パスワード -->
      <p>
        <input type="password" name="password" placeholder="password" onkeydown="enter_to_signup();">
      </p>
      <p class="err"><?= h($app->getErrors('password')); ?></p>

      <!-- 新規ユーザ登録 -->
      <div class="btn" onclick="click_to_signup();">Sign Up</div>

      <!-- ログイン画面へ -->
      <p class="fs12"><a href="login.php">Log In</a></p>

      <!-- トークン -->
      <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
    </form>
  </div>
  <!-- Javascript -->
  <script src="js/functions.js"></script>
</body>
</html>
