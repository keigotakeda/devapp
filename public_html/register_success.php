<?php

// ユーザー一覧表示

require_once(__DIR__ . '/../config/config.php');

// var_dump($_SESSION['me']);

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
<body>
  <div id="container">

    <!-- メイン -->
    <p class="userinfo">
      <?= "ようこそ " . h($app->me()->username) . " さん ("  . h($app->me()->email) .")" ; ?>
    </p>
    <!-- キャンセル to index.php-->
    <p class="fs12">ランクを変更しました：現在<?= $app->me()->role ? "プレミアム" : "一般ユーザー" ;?>です</p>
    <p class="fs12"><a href="index.php">トップに戻る</a></p>


  </div>
</body>
</html>
