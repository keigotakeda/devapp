<?php

// ユーザー一覧表示
require_once(__DIR__ . '/../config/config.php');

// タイムアウトするまでの時間
ini_set( 'session.gc_maxlifetime', 10 );  // 秒(デフォルト:1440)

// // Autoログアウトの実装
$autologout = new MyApp\Controller\Auto_logout();
$autologout->run();

$app = new MyApp\Controller\Enq_submit();
$app->run();
// $app->me()
// $app->getValues()->users

// 送信完了
$submit_success = $app->getValues()->submit_success;

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
    <!-- トップページ -->
    <p class="fs12"><a href="index.php">会員トップページ</a></p>
    <p class="fs12"><a href="enq_csv.php">CSV出力</a></p>
    <p class="fs12"><a href="enq_chart.php">CSVチャート</a></p>

    <!-- ログアウト -->
    <form action="logout.php" method="post" id="logout">
      <input type="submit" value="Logout">
      <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
    </form>

    <!-- メイン -->
    <p class="userinfo">
      <?= "ようこそ " . h($app->me()->username) . " さん ("  . h($app->me()->email) . ") " . h($app->me()->role ? "プレミアム" : "一般ユーザー") ;?>
    </p>

    <h1 class="text_center">アンケート</h1>

    <!-- 送信完了の場合メッセージ表示 -->
    <?php if ($submit_success != '') : ?>
      <div class="submit_success text_center"><?= h($submit_success) ;?></div>
    <?php endif;?>

    <!-- 送信内容が空でエラー -->
    <p class="err"><?= h($app->getErrors('enq_empty')); ?></p>

    <form action="enq.php" method="post" id="enq_submit" class="text_center">

      <!-- ID hidden -->
      <input type="hidden" name="id" value="<?= h($_GET['id']); ?>">

      <!--  アンケート -->
      <p>性別:
        <label><input class="radio" type="radio" name="gender" value="1" checked>男性</label>
        <label><input class="radio"  type="radio" name="gender" value="2">女性</label>
      </p>

      <p>年代:
        <label><input class="radio" type="radio" name="old" value="1">10代</label>
        <label><input class="radio"  type="radio" name="old" value="2">20代</label>
        <label><input class="radio"  type="radio" name="old" value="3" checked>30代</label>
        <label><input class="radio"  type="radio" name="old" value="4">40代</label>
        <label><input class="radio"  type="radio" name="old" value="5">50代</label>
      </p>

      <p>味の評価:
        <label><input class="radio" type="radio" name="taste" value="1">まあまあ</label>
        <label><input class="radio"  type="radio" name="taste" value="2" checked>おいしい</label>
        <label><input class="radio"  type="radio" name="taste" value="3">すごくおいしい</label>
      </p>


      <textarea class="margin_bottom_1em" name="opinion" rows="6" cols="60" placeholder="ご意見あれば、こちらへそうぞ"></textarea>

      <!-- トークン -->
      <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">

      <!-- アンケート送信 -->
      <div class="btn" onclick="enq_submit();">送信</div>

    </form>

  </div>
<script src="js/functions.js"></script>
<!-- 送信完了のためのjQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script>
$(function() {
  $('.submit_success').fadeOut(2500);
});
</script>
</body>
</html>
