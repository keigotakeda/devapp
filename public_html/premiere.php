<?php

// ユーザー一覧表示

require_once(__DIR__ . '/../config/config.php');

// タイムアウトするまでの時間
ini_set( 'session.gc_maxlifetime', 10 );  // 秒(デフォルト:1440)

// var_dump($_SESSION['me']);

// // Autoログアウトの実装
$autologout = new MyApp\Controller\Auto_logout();
$autologout->run();

$app = new MyApp\Controller\Index();
$app->run();



// プレミア会員のみOK
// 無料会員 role === 0 なら突き返す
if($app->me()->role == 0) {
  header('Location: ' . SITE_URL . '/index.php');
  exit;
}

// var_dump($app->me());
// $app->getValues()->users

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Home</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>
  <div id="container" class="premiere_bg">
    <!-- プレミア会員 -->
    <p class="fs12"><a href="index.php">一般ページへ</a></p>

    <!-- ログアウト -->
    <form action="logout.php" method="post" id="logout">
      <input type="submit" value="Logout">
      <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
    </form>
    <!-- 退会処理 -->
    <p class="fs12"><a href="delete_user.php">退会処理</a></p>

    <!-- パスワード変更 -->
    <p class="fs12"><a href="change_pw.php">パスワード変更</a></p>
    <!-- <form action="change_pw.php" method="post">
      <input type="submit" value="Change passwd">
      <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
    </form> -->


    <!-- メイン -->
    <p class="userinfo">
      <?= "プレミア会員ページ " . h($app->me()->username) . " 様 ("  . h($app->me()->email) .")" ; ?>
    </p>

    <h1>Users <span class="fs12"><?= count($app->getValues()->users) ;?></span></h1>
    <ul>
      <!-- いらなくなるプログラム -->
      <?php foreach($app->getValues()->users as $user) : ?>
      <li><?= h($user->email) ; ?></li>
      <?php endforeach; ?>
      <!-- / いらなくなるプログラム -->
    </ul>
  </div>
</body>
</html>
