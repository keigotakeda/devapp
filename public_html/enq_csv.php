<?php

// ユーザー一覧表示
require_once(__DIR__ . '/../config/config.php');

// // Autoログアウトの実装
$autologout = new MyApp\Controller\Auto_logout();
$autologout->run();

$app = new MyApp\Controller\Enq_csv();
$app->run();
// $app->me()
// $app->getValues()->users
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>CSV</title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg_user">

  <!-- コンテナ -->
  <div id="container">
    <!-- トップページ -->
    <p class="fs12"><a href="index.php">会員トップページ</a></p>

    <!-- ログアウト -->
    <form action="logout.php" method="post" id="logout">
      <input type="submit" value="Logout">
      <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
    </form>

    <!-- メイン -->
    <p class="userinfo">
      <?= "ようこそ " . h($app->me()->username) . " さん ("  . h($app->me()->email) . ") " . h($app->me()->role ? "プレミアム" : "一般ユーザー") ;?>
    </p>

    <h1>CSV出力</h1>
    <form action="enq_csv.php" method="post">
      Download
    <input type="submit" name="export_csv" value="ダウンロード" />
    </form>


  </div>
<script src="js/functions.js"></script>
</body>
</html>
