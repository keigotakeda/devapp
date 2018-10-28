<?php

// ユーザー一覧表示
require_once(__DIR__ . '/../config/config.php');

// // Autoログアウトの実装
$autologout = new MyApp\Controller\Auto_logout();
$autologout->run();

$app = new MyApp\Controller\Index();
$app->run();

?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Todos</title>
  <!-- bootstrap CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <!-- CSS -->
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/todos.css">
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


    <!-- ****************** -->
    <!-- TODOS　プログラム -->
    <!-- ****************** -->
    <div id="app" class="todo_container">
    <!-- <div id="app"> -->
      <h1>
        <button v-on:click="purge" class="btn_size">終了タスク削除</button>
        My Todos
        <span class="info">({{ remaining.length }} / {{ todos.length }})</span>
      </h1>

      <ul>
        <li v-for="(todo, index) in todos">
          <input type="checkbox" v-model="todo.isDone">
          <span v-bind:class="{done: todo.isDone}">{{ todo.title }}</span>
          <span v-on:click="deleteItem(index)" class="command">[x]</span>
        </li>
        <li v-show="!todos.length">タスクがないよ！</li>
      </ul>


      <form v-on:submit="addItem">
      <!-- <form @submit.prevent="addItem"> -->
        <input type="text" v-model="newItem">
        <input type="submit" class="btn_size" value="タスク追加">
      </form>

    </div>
  </div>
<!-- <script src="js/functions.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/vue"></script>
<script src="js/todos.js"></script>

</body>
</html>
