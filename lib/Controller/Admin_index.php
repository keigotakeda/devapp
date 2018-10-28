<?php

namespace MyApp\Controller;

class Admin_index extends \MyApp\Controller {
  public function run() {
    if(!$this->isAdminLoggedIn()) {
      // 管理者ログイン失敗なら戻る
      header('Location: ' . SITE_URL . '/admin_login.php');
      exit;
    }

    // ***************************************
    // $_GET['p'] ページング処理
    // **************************************
    // validate処理
    if(preg_match('/^[1-9][0-9]*$/', $_GET['page'])) {
      $page = (int)$_GET['page'];
    } else {
      $page = 1;
    }
    // SELECT * FROM users LIMIT offset, count;
    // page offset  count
    // 1    0       5
    // 2    5       5
    // 3    10      5
    // SQL作成  USERS_PER_PAGEはconfig.php
    $offset = USERS_PER_PAGE * ($page - 1);
    // var_dump($offset);
    $count = USERS_PER_PAGE;
    // var_dump($count);


    // **************************************
    // ソートパラメタの取得
    // **************************************
    $sort = (string)@$_GET['sort'];
    // デフォルトの設定
    if ('' === $sort) {
        $sort = 'id'; // デフォルト
    }

    // ソート条件の付与
    // (第一種)ホワイトリストによるチェック
    $sql_sort = '';
    $sort_list = array (
        'id' => 'id',
        'id_desc' => 'id DESC',
        'username' => 'username',
        'username_desc' => 'username DESC',
        'created' => 'created',
        'created_desc' => 'created DESC',
        'modified' => 'modified',
        'modified_desc' => 'modified DESC',
    );
    if (true === isset($sort_list[$sort])) {
      // $sql_sort = ' ORDER BY ' . $sort_list[$sort];
      $sql_sort = $sort_list[$sort];

      // ついでに a_tag_print() 用に準備しておく
      $this->setValues('sort_for_a_tag', $sort);

    } else {
        // いつまでも「無駄な条件」を持っていても意味がないので、消しておく
        $sort = '';
    }
    // var_dump($sql_sort);


    // **************************************
    // 検索パラメタの取得
    // **************************************
    // (第一種)ホワイトリストの準備
    $search_list = array (
        'search_email',
        'search_username',
        'search_created_from',
        'search_created_to',
        'search_like_username',
        'search_like_email'
    );
    // データの取得
    $search = array();
    foreach($search_list as $row) {
        if ((true === isset($_POST[$row]))&&('' !== $_POST[$row]) ) {
            $search[$row] = $_POST[$row];
        }
    }
    // var_dump($search);
    // 検索 文字列 取得から処理

    // 「検索条件がある」場合の検索条件の付与
    $bind_array = array();
    if (false === empty($search)) {

      // View に渡す処理
      // Viewでの　検索結果　と　件数表示の切り替え変数
      $this->setValues('is_search', true);
      // ViewでのXXXで検索していますの部分
      $this->setValues('search_lists', $search);


      $where_list = array();
      // 値を把握する

      if (true === isset($search['search_email'])) {
          // WHERE句に入れる文言を設定する
          $where_list['email'] = 'email = :email';
          // BINDする値を設定する
          $bind_array[':email'] = $search['search_email'];
      }

      if (true === isset($search['search_username'])) {
          // WHERE句に入れる文言を設定する
          $where_list['username'] = 'username = :username';
          // BINDする値を設定する
          $bind_array[':username'] = $search['search_username'];
          // var_dump($bind_array[':username']);
      }

      if (true === isset($search['search_created_from'])) {
          // WHERE句に入れる文言を設定する
          $where_list['created_from'] = 'created >= :created_from';
          // 日付を簡単に整える
          $search['search_created_from'] = date('Y-m-d', strtotime($search['search_created_from']));
          // BINDする値を設定する
          $bind_array[':created_from'] = $search['search_created_from'] . ' 00:00:00';
      }
      //
      if (true === isset($search['search_created_to'])) {
          // WHERE句に入れる文言を設定する
          $where_list['created_to'] = 'created <= :created_to';
          // 日付を簡単に整える
          $search['search_created_to'] = date('Y-m-d', strtotime($search['search_created_to']));
          // BINDする値を設定する
          $bind_array[':created_to'] = $search['search_created_to'] . ' 23:59:59';
      }
      //
      // if (true === isset($search['search_created'])) {
      //     // WHERE句に入れる文言を設定する
      //     $where_list[] = 'created BETWEEN :created_from AND :created_to';
      //     // 日付を簡単に整える
      //     $search['search_created'] = date('Y-m-d', strtotime($search['search_created']));
      //     // BINDする値を設定する
      //     $bind_array[':created_from'] = $search['search_created'] . ' 00:00:00';
      //     $bind_array[':created_to'] = $search['search_created'] . ' 23:59:59';
      // }

      // LIKE句 username
      if (true === isset($search['search_like_username'])) {
          // WHERE句に入れる文言を設定する
          $where_list['like_username'] = 'username LIKE :like_username';
          // BINDする値を設定する
          //$bind_array[':like_name'] = $search['search_like_name'] . '%'; // 前方一致の場合
          //$bind_array[':like_name'] = '%' . $search['search_like_name'] . '%'; // 部分一致の場合
          $bind_array[':like_username'] = '%' . like_escape($search['search_like_username']) . '%'; // 部分一致、%や_はエスケープ、の場合
      }

      // LIKE句 Email
      if (true === isset($search['search_like_email'])) {
          // WHERE句に入れる文言を設定する
          $where_list['like_email'] = 'email LIKE :like_email';
          // BINDする値を設定する
          //$bind_array[':like_post'] = $search['search_like_post'] . '%'; // 前方一致の場合
          //$bind_array[':like_post'] = '%' . $search['search_like_post'] . '%'; // 部分一致の場合
          $bind_array[':like_email'] = '%' . like_escape($search['search_like_email']) . '%'; // 部分一致、%や_はエスケープ、の場合
      }


      // WHERE句を合成してSQL文につなげる
      // $sql_search = 'SELECT * FROM users WHERE ' . implode(' AND ', $where_list);
      // XXX 「sort条件」は現在指定の値を持越し。「何かデフォルトでリセットしたい」ような場合はここで$sort変数に適切な値を代入する

      // echo "bind_array ";
      // var_dump($bind_array);
      // echo "wherelist ";
      // var_dump($where_list);

      try {
        $userModel = new \MyApp\Model\User();
        $this->setValues('users_per_page', $userModel->serching([
          'where_list' => $where_list,
          'bind_array' => $bind_array
          ])
        );
      } catch (\MyApp\Exception\SearchingFailed $e) {
        // 検索に失敗した
        $this->setErrors('searching_failed', $e->getMessage());
        return;
      }
    }








    // **************************************
    // ページ数に該当するユーザデータを取得する（検索がない場合）
    // **************************************

    // $sql = "select * from users limit " . $offset . "," . $count;

    //　検索がない場合　以下のページング、ソート処理を行う
    if (true === empty($search)) {
      try {
        $userModel = new \MyApp\Model\User();
        $this->setValues('users_per_page', $userModel->paging([
          'offset' => $offset,
          'count' => $count,
          'sort' => $sql_sort
          ])
        );
      } catch (\MyApp\Exception\PagingFailed $e) {
        $this->setErrors('paging_failed', $e->getMessage());
        return;
      }


      // トータルユーザ数
      // $this->setValues('total', $userModel->totalUsers());
      $userModel_total = new \MyApp\Model\User();
      $this->setValues('total', $total = $userModel_total->totalUsers());

      $this->setValues('page', $page);
      $this->setValues('totalPages', $totalPages = (int)ceil($total / USERS_PER_PAGE));

      // echo "total pages = ";
      // var_dump($totalPages);
      // exit;

      $this->setValues('from', $from = $offset + 1);
      $this->setValues('to', $to = ($offset + USERS_PER_PAGE) < $total ? ($offset + USERS_PER_PAGE) : $total);
    }
    // ＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊
    // ＊＊＊　基本はこれより上に書く　＊＊＊＊＊＊＊＊＊
    // ＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊
  }

  // 管理者ページ => ソートしているマークの赤色表記
  // sortのAエレメント出力用関数
  public function a_tag_print($type, $out) {
      if ($type === $this->getValues()->sort_for_a_tag) {
          echo "<a class='bg-danger text-danger' href='./admin_index.php?sort={$type}'>{$out}</a>";
      } else {
          echo "<a class='text-muted' href='./admin_index.php?sort={$type}'>{$out}</a>";
      }
  }
}
