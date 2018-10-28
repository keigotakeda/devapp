<?php

// 新規登録

require_once(__DIR__ . '/../config/config.php');

// ログインしているページはこの設定
$app = new MyApp\Controller\Reminder_password_input();
$app->run();


// トークンを取り出す
// $token = (string)@$_GET['t'];
// 直接formに書いたので削除

// リマインダーで、パスワード変更が成功したか？で、Viewの表示切り替え
$is_reminder_pw_success = false;
if (h($app->getValues()->is_reminder_pw_success == true)) {
  $is_reminder_pw_success = true;
}

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
    <?php if($is_reminder_pw_success == true) :?>
      <h1>パスワードが変更されました</h1>
      <!-- ログインへ-->
      <p class="fs12"><a href="login.php">戻る</a></p>
    <?php else: ?>

    <!-- メイン -->
    <h1>パスワードの再設定</h1>
    <form action="reminder_password_input.php" method="post" id="change_pw">

      <!-- 新規パスワード -->
      <p>
        <input type="password" name="password_new" placeholder="new password">
      </p>
      <p class="err"><?= h($app->getErrors('change_pw')); ?></p>

      <!-- 確認パスワード -->
      <p>
        <input type="password" name="password_new_check" placeholder="check password" onkeydown="enter_to_change_password();">
      </p>
      <p class="err"><?= h($app->getErrors('change_pw')); ?></p>

      <!-- リマインダーで作ったGETトークン -->
      <input type="hidden" name="token" value="<?= h($_GET['t']); ?>">

      <!-- パスワード変更送信 -->
      <div class="btn" onclick="click_to_change_password();">Change Passwd</div>

    </form>
    <!-- キャンセル to index.php-->
    <p class="fs12"><a href="index.php">戻る</a></p>

    <?php endif; ?>



  </div>
  <script src="js/functions.js"></script>
</body>
</html>
