<?php

// phpinfo();

// 管理者画面
// データベースなので変更なし
require_once(__DIR__ . '/../config/config.php');

// var_dump($_SESSION['me']);

// Autoログアウトの実装
$autologout = new MyApp\Controller\Auto_logout();
$autologout->run();

// コントローラを呼び出し
$app = new MyApp\Controller\Admin_index();
$app->run();

// 検索入力結果（ユーザ）
// $search = array();
// $search = $app->getValues()->users_per_page;


//Viewでの　検索結果　と　件数表示の切り替え変数
// 三項演算子 Ex... $a = $bool ? 1 : 2;
$is_search = false;
$is_search = ($app->getValues()->is_search) ? true : false;


// ページング　＆　ソート処理
$from = (int)h($app->getValues()->from);
$to = (int)h($app->getValues()->to);
$page = (int)h($app->getValues()->page);
// トータルページ処理
$totalPages = (int)h($app->getValues()->totalPages);
// 不正な$pageが入ったらトップに戻す
if($totalPages < $page) {
  // 管理者ログイン失敗なら戻る
  $page = '';
  header('Location: ' . SITE_URL . '/admin_login.php');
  exit;
}


// var_dump($app->getValues()->users_per_page);

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

    <!-- 検索 -->
    <div class="row">
      <h3 class="text_center">検索</h3>
      <hr>
      <!-- <span class="col-md-6"> -->
      <!-- 厳密に検索 -->
      <form action="./admin_index.php" method="post">
        <div class="search text_align_right">
          <span class="">「名前」<input name="search_username" value="<?php echo h(@$search['search_username']); ?>"></span>
        </div>

        <div class="search text_align_left">
          <span class="">「メール」<input name="search_email" value="<?php echo h(@$search['search_email']); ?>"></span>
        </div>
      <!-- 部分一致検索 -->
        <div class="search text_align_right">
          <span class="">名前(部分一致)<input name="search_like_username" value="<?php echo h(@$search['search_like_username']); ?>"></span>
        </div>

        <div class="search text_align_left">
          <span class="">メール(部分一致)<input name="search_like_email" value="<?php echo h(@$search['search_like_email']); ?>"></span>
        </div>
      <!-- 作成日検索 -->
        <div class="search witdh_full">
          <span class="">「作成日(YYYY-MM-DD)」<input name="search_created_from" value="<?php echo h(@$search['search_created_from']); ?>">～<input name="search_created_to" value="<?php echo h(@$search['search_created_to']); ?>"></span>
        </div>
        <div class="search witdh_full">
          <span class=""><button class="btn">検索</button></span>
        </div>

      <!-- フォームここまで -->
      </form>


    </div>

    <?php if (true === $is_search) : ?>
      現在、以下の項目で検索をかけています。
      <!-- 検索解除 -->
      <a class="" href="./admin_index.php">検索解除</a>
      <br>
      <!-- エラー：検索処理が失敗しました -->
      <p class="err"><?= h($app->getErrors('searching_failed')); ?></p>
      <p>
        <?php
            foreach($app->getValues()->search_lists as $k => $v) {
              switch ($k) {
                case 'search_username':
                    echo "名前: "; break;
                case 'search_email':
                    echo "メール: "; break;
                case 'search_like_username':
                    echo "名前（部分一致）: "; break;
                case 'search_like_email':
                    echo "メール（部分一致）: "; break;
                case 'search_created_from':
                    echo "作成日(from): "; break;
                case 'search_created_to':
                    echo "作成日(to): "; break;

              }
              // echo h($k), ': ', h($v), "<br>\n";
              echo h($v), "<br>\n";
            }
        ?>
      </p>
    <?php endif;?>

    <!-- 件数表示 検索時は表示しない -->
    <?php if(false === $is_search) : ?>
      <p>全<?= h($app->getValues()->total); ?>件中、<?= h($app->getValues()->from); ?>件〜<?= h($app->getValues()->to); ?>件を表示しています。</p>
    <?php endif;?>

    <!-- エラー表示 -->
    <p class="err"><?= h($app->getErrors('paging_failed')); ?></p>

    <!-- テーブル取得 -->
    <table class="table table-hover">
      <tr class="table_th_color_setting">
        <th width="60px">id</th>
        <th>email
        <th>username
        <th>created
        <th>modified
        <th>詳細
        <th>修正
        <th>削除
      <tr>
        <!-- sortのリンク -->
        <td><?php $app->a_tag_print('id','▲'); ?> <?php $app->a_tag_print('id_desc','▼'); ?>
        <td><!-- emailは無し -->
        <td><?php $app->a_tag_print('username','▲'); ?> <?php $app->a_tag_print('username_desc','▼'); ?>
        <td><?php $app->a_tag_print('created', '▲'); ?> <?php $app->a_tag_print('created_desc','▼'); ?>
        <td><?php $app->a_tag_print('modified', '▲'); ?> <?php $app->a_tag_print('modified_desc','▼'); ?>
        <td>
        <td>
        <td>
      <!-- ユーザテーブル -->
      <?php foreach($app->getValues()->users_per_page as $user) : ?>
      <tr>
        <td><?= h($user->id) ; ?></td>
        <td><?= h($user->email) ; ?></td>
        <td><?= h($user->username) ; ?></td>
        <td><?= h($user->created) ; ?></td>
        <td><?= h($user->modified) ; ?></td>
        <!-- 詳細 -->
        <td><a class="btn" href="./admin_detail.php?id=<?php echo urlencode($user->id); ?>">詳細</a>
          <!-- 詳細 -->
        <td><a class="btn btn-default" href="./admin_modify.php?id=<?php echo urlencode($user->id); ?>">修正</a>
        <!-- 削除 -->
        <td>
          <form action="./admin_delete.php" method="post">
            <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
            <input type="hidden" name="id" value="<?= h($user->id) ; ?>">
            <button class="btn btn-danger" onClick="return confirm('本当に削除しますか？');">削除</button>
          </form>
      </tr>
      <?php endforeach; ?>
    </table>

    <!-- ページング機能 -->
    <div class="text_center">
      <ul class="pagination">

        <!-- 前 -->
        <?php if ($page > 1) : ?>
        <li><a href="?sort=<?= urlencode($app->getValues()->sort_for_a_tag) ; ?>&page=<?php echo $page-1; ?>">前</a></li>
        <?php endif; ?>

        <!-- 数字欄 -->
        <?php// for ($i = 1; $i <= $totalPages; $i++) : ?>
        <?php

          $page_width = 2; // *** ページからの前後の数 ***
          // 見直し必要あり
          $page_from = ($page - $page_width > 0) ? $page - $page_width : 1;
          $page_to = ($page + $page_width < $totalPages) ? $page + $page_width : $totalPages ;
        ?>
        <?php for ($i = $page_from; $i <= $page_to; $i++) : ?>

          <!-- 現在のページなら強調(strong) -->
          <?php if ($page == $i) : ?>
          <li class="strong"><a href="?sort=<?= urlencode($app->getValues()->sort_for_a_tag) ; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a></li>

          <!-- 現在のページではない数字 -->
          <?php else: ?>
          <li><a href="?sort=<?= urlencode($app->getValues()->sort_for_a_tag) ; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a></li>

          <?php endif; ?>
        <?php endfor; ?>

        <!-- 後 -->
        <?php if ($page < $totalPages) : ?>
        <li><a href="?sort=<?= urlencode($app->getValues()->sort_for_a_tag) ; ?>&page=<?php echo $page+1; ?>">次</a></li>
        <?php endif; ?>

      </ul>
    </div>
  </div>
  <script src="js/functions.js"></script>
</body>
</html>
