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




// $app->me()
// $app->getValues()->users

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Home</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg_user">

  <!-- コンテナ -->
  <div id="container">
    <!-- イメージアップローダー -->
    <p class="fs12"><a href="todos.php">Todo App!</a></p>

    <!-- アンケートページへ -->
    <p class="fs12"><a href="enq.php">アンケート</a></p>

    <!-- プレミア会員 -->
    <p class="fs12"><a href="premiere.php">プレミア会員画面へ</a></p>

    <!-- プレミア登録・解除 -->
    <p class="fs12"><a href="register_premium.php">プレミア会員登録・解除</a></p>

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
      <?= "ようこそ " . h($app->me()->username) . " さん ("  . h($app->me()->email) . ") " . h($app->me()->role ? "プレミアム" : "一般ユーザー") ;?>
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
