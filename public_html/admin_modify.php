<?php

// 新規登録

require_once(__DIR__ . '/../config/config.php');

// Autoログアウトの実装
$autologout = new MyApp\Controller\Auto_logout();
$autologout->run();

// ログインしているページはこの設定
$app = new MyApp\Controller\Admin_modify();
$app->run();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Admin modify user</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <div id="container">

    <!-- 入力項目のエラー -->
    <p class="err"><?= h($app->getErrors('input_error')); ?></p>

    <!-- メイン -->
    <p class="userinfo">
      <?= "ようこそ 管理人 " . h($app->admin_me()->admin) . "さん" ; ?>
    </p>


    <h1>ユーザ情報の修正</h1>
    <?php foreach($app->getValues()->user_detail as $user) : ?>
      <form action="admin_modify.php" method="post" id="change_pw">

        <!-- ID hidden -->
        <input type="hidden" name="id" value="<?= h($_GET['id']); ?>">

        <!-- メール -->
        <p>mail:
          <input type="text" name="email" placeholder="email" value="<?= h($user->email) ; ?>">
        </p>

        <!-- ユーザ名 -->
        <p>name:
          <input type="text" name="username" placeholder="username" value="<?= h($user->username) ; ?>">
        </p>

        <!-- 新しいパスワード -->
        <p>pass:
          <input type="password" name="password_new" placeholder="new passwd">
        </p>
        <p class="err">※pass:変更しない場合は空でOK</p>


        <!-- プレミアム -->
        <p>role:
          <input type="text" name="role" placeholder="username" value="<?= h($user->role) ; ?>">
        </p>


        <!-- トークン -->
        <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">

        <!-- パスワード変更送信 -->
        <div class="btn" onclick="click_to_change_password();">Modify</div>

        <!-- キャンセル to index.php-->
        <p class="fs12"><a href="admin_index.php">Cancel</a></p>

      </form>
    <?php endforeach; ?>

  </div>
  <script src="js/functions.js"></script>
</body>
</html>
