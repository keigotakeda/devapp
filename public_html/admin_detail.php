<?php

// 管理者画面
// データベースなので変更なし
require_once(__DIR__ . '/../config/config.php');

// var_dump($_SESSION['me']);

// Autoログアウトの実装
$autologout = new MyApp\Controller\Auto_logout();
$autologout->run();

// コントローラを呼び出し
$app = new MyApp\Controller\Admin_detail();
$app->run();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>管理者</title>
  <link rel="stylesheet" href="css/styles.css">
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
  <div id="container">
    <!-- ログアウト -->
    <form action="logout.php" method="post" id="logout">
      <input type="submit" value="Logout">
      <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
    </form>

    <!-- メイン -->
    <p class="userinfo">
      <?= "ようこそ 管理人 " . h($app->admin_me()->admin) . "さん" ; ?>
    </p>

    <!-- エラー表示 -->
    <p class="err"><?= h($app->getErrors('paging_failed')); ?></p>
    <!-- テーブル取得 -->
    <table class="table table-hover">
      <tr>
        <th>id
        <th>email
        <th>username
        <th>created
        <th>modified
        <th>role
        <th>password
      <?php foreach($app->getValues()->user_detail as $user) : ?>
      <tr>
        <td><?= h($user->id) ; ?></td>
        <td><?= h($user->email) ; ?></td>
        <td><?= h($user->username) ; ?></td>
        <td><?= h($user->created) ; ?></td>
        <td><?= h($user->modified) ; ?></td>
        <td><?= h($user->role ? "有料" : "一般") ; ?></td>
        <td><?= h($user->password) ; ?></td>
      </tr>
      <?php endforeach; ?>
    </table>

    <!-- 管理者ログインへ -->
    <p class="fs12"><a href="admin_index.php">管理者トップ</a></p>

  </div>
</body>
</html>
