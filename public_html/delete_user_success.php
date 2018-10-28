<?php

// ユーザー一覧表示

// require_once(__DIR__ . '/../config/config.php');
  session_start();

  $_SESSION = [];

  if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 86400, '/');
  }

  session_destroy();

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
    <!-- 退会処理完了-->
    <p class="fs12">退会手続きが完了しました</p>
    <p class="fs12">ご利用ありがとうございました！</p>
    <p class="fs12"><a href="index.php">トップへ</a></p>
  </div>
</body>
</html>
