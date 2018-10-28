<?php

// 新規登録

require_once(__DIR__ . '/../config/config.php');

// Autoログアウトの実装
$autologout = new MyApp\Controller\Auto_logout();
$autologout->run();

// ログインしているページはこの設定
$app = new MyApp\Controller\Admin_delete();
$app->run(); // どこかでreturnすれば　NULLが返ってくる、のでme()が読み込まれない
// var_dump($app->me());

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>admin delete</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <div id="container">
    <!-- メイン -->
    <p class="userinfo">
      <?= "ようこそ " . h($app->me()->admin) . " さん" ; ?>
    </p>

    <h1>削除処理が失敗しました</h1>
      <p class="err"><?= h($app->getErrors('admin_delete')); ?></p>
      <!-- トークン -->
      <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">

      <!-- 管理者ログインへ -->
      <p class="fs12"><a href="admin_login.php">管理者トップ</a></p>
  </div>
</body>
</html>
