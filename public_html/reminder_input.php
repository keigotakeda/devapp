<?php

// 新規登録

// tokenチェック
// メールのvalidate
// sqlメールアドレス取得


require_once(__DIR__ . '/../config/config.php');

$app = new MyApp\Controller\Reminder_input();

$app->run();

// リマインダーが無事送信されたかどうかで、Viewの表示切り替え
$is_reminder_mail_submit = false;
if (h($app->getValues()->is_reminder_mail_submit === true)) {
  $is_reminder_mail_submit = true;
}


?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Sign Up</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <div id="container" class="text_center">
    <h3>パスワードを忘れた</h3>
    <hr>

    <!-- もし無事に送信されたら、メールに再設定用のリンクが送信されました。 -->
    <?php if($is_reminder_mail_submit == true) :?>
      <p class="color_red">入力されたemailに、メールを送信しました。 そちらから再設定してください。</p>
    <?php endif; ?>

    <p>入力されたemailに、パスワード再設定のURLを送ります。</p>
    <form action="./reminder_input.php" method="post" id="signup">
      <!-- サインイン メール -->
      <p>
        <input type="text" name="email" placeholder="email" value="">
      </p>
      <p class="err"><?= h($app->getErrors('email')); ?></p>

      <!-- 新規ユーザ登録 -->
      <div class="btn" onclick="click_to_signup();">再設定</div>

      <!-- トークン -->
      <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
    </form>

    <!-- ログイン画面へ -->
    <p class="fs12"><a href="login.php">Log In</a></p>

  </div>
  <!-- Javascript -->
  <script src="js/functions.js"></script>
</body>
</html>
