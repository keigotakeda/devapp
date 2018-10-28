<?php

namespace MyApp\Model;

class User extends \MyApp\Model {
  public function create($values) {
    $stmt = $this->db->prepare("INSERT INTO users (email, username, password, created, modified) VALUES (:email, :username,:password, now(), now())");
    $res = $stmt->execute([
      ':email' => $values['email'],
      ':username' => $values['username'],
      ':password' => password_hash($values['password'], PASSWORD_DEFAULT)
    ]);
    if ($res === false) {
      throw new \MyApp\Exception\DuplicateEmail();
    }
  }

  // // ＊＊＊要編集 テーブル名 変更時＊＊＊
  // // ログイン処理
  // public function login($values) {
  //   $stmt = $this->db->prepare("select * from users where email = :email");
  //   $stmt->execute([
  //     ':email' => $values['email']
  //   ]);
  //   $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
  //   $user = $stmt->fetch();
  //
  //   if(empty($user)) {
  //     throw new \MyApp\Exception\UnmatchEmailOrPassword();
  //   }
  //   if(!password_verify($values['password'], $user->password)) {
  //     throw new \MyApp\Exception\UnmatchEmailOrPassword();
  //   }
  //   return $user;
  // }






  // ＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊
  // ＊＊＊ ログイン処理 ＊＊＊＊＊＊＊＊
  // ＊＊＊ ログインロックの追加処理 ＊＊
  // ＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊
  // メールがあるか、で　datumに入れておく
  public function login($values) {

    // メールが存在するか
    $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([
      ':email' => $values['email']
    ]);
    $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
    $user = $stmt->fetch();
    // var_dump($user->email);
    // exit;

    // stdClassではなくて、普通の配列とする
    // $user = $stmt->fetch(\PDO::FETCH_ASSOC); //これだと配列になるダメ

    // 判定用フラグ
    $login_flg = false;

    // emailが存在していたら、作業を続行する
    if (!empty($user->email)) {
      // ロックテーブルを読み込んで情報を把握する
      $sql = 'SELECT * FROM user_login_lock WHERE user_id=:user_id;';
      $pre = $this->db->prepare($sql);
      // 値のバインド
      // $pre->bindValue(':user_id', $user['id'], \PDO::PARAM_STR);
      $pre->bindValue(':user_id', $user->id, \PDO::PARAM_INT);
      // SQLの実行
      $r = $pre->execute(); // XXX
      // SELECTした内容の取得
      $lock_datum = $pre->fetch(\PDO::FETCH_ASSOC);
      // とれてなければデフォルトの情報を入れる
      if (false === $lock_datum) {
          //
          $lock_datum['user_id'] = $user->id;
          $lock_datum['error_count'] = 0;
          $lock_datum['lock_time'] = '0000-00-00 00:00:00';
      }

      // 現在ロック中なら、時刻を確認
      if ('0000-00-00 00:00:00' !== $lock_datum['lock_time']) {
          // ロック時間が「現在以降」なら、ロックを一端外す
          if (time() > strtotime($lock_datum['lock_time'])) {
              $lock_datum['lock_time'] = '0000-00-00 00:00:00';
              $lock_datum['error_count'] = 0;
          }
      }

      // 最終的に「ロックされていなければ」以下の処理をする
      if ('0000-00-00 00:00:00' === $lock_datum['lock_time']) {
          // パスワードを比較して、その結果を代入する
          if (true === password_verify($values['password'], $user->password)) {
              // countのリセット
              $lock_datum['error_count'] = 0;
              // ログインフラグを立てる?????????????????????????
              $login_flg = true;
          } else {
              // countのインクリメント
              ++ $lock_datum['error_count'];
              // 一定回数(一端、５回)連続でエラーなら、ロックを入れる(一端、１時間=3600)
              if (5 <= $lock_datum['error_count']) {
                  $lock_datum['lock_time'] = date('Y-m-d H:i:s', time() + 120);
                  // XXX ここで「ユーザメールに"ログインロックがされた。心当たりがなければ運用に連絡して欲しい"的なmailを投げる等の処理を入れるのも有効
                  echo "メールする：認証が複数回失敗です。しばらくしてからアクセスしてね！";
              }
          }
      }

      // ロックテーブルに情報を入れる
      // XXX いわゆるupsertにはREPLACEとINSERT ON DUPLICATE KEY UPDATEがあるが、今回は「全てのカラムを入れる」ので、SQL文がシンプルなREPLACEで対応
      $sql = 'REPLACE INTO user_login_lock(user_id, error_count, lock_time) VALUES(:user_id, :error_count, :lock_time);';
      $pre = $this->db->prepare($sql);
      // 値のバインド
      $pre->bindValue(':user_id', $lock_datum['user_id'], \PDO::PARAM_STR);
      $pre->bindValue(':error_count', $lock_datum['error_count'], \PDO::PARAM_INT);
      $pre->bindValue(':lock_time', $lock_datum['lock_time'], \PDO::PARAM_STR);
      // SQLの実行
      $r = $pre->execute(); // XXX
    }
    // echo "login_flag= <br>";
    // var_dump($login_flg);


    // 最終的に「ログイン情報に不備がある」場合は、エラーとして突き返す
    // XXX ロジック的にあえて「emailのエラーなのかパスワードのエラーなのか」判別できないようにしてある：不必要情報への対策
    // エラーが出たら入力ページに遷移する
    if (false === $login_flg) {
        // ログイン失敗
        throw new \MyApp\Exception\UnmatchEmailOrPassword();
        // ログインページに突き返す
        header('Location: ./login.php');
        exit;
    }

    // ログイン成功
    return $user;
  }



  // ＊＊＊ プレミム登録・解除 ＊＊＊
  public function register($values) {

    // DBからユーザの確認
    $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([
      ':email' => $values['email']
    ]);
    $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
    $user = $stmt->fetch();

    if(empty($user)) {
      // throw new \MyApp\Exception\UnmatchEmailOrPassword();
      echo "Emailが不正です(hidden)";
    }
    if(password_verify($values['password_current'], $user->password)) {
      // プレミアム登録・解除　更新
      $stmt = $this->db->prepare("UPDATE users SET role = :register WHERE email = :email");

      $res = $stmt->execute([
        ':register' => $values['register'],
        ':email' => $values['email']
      ]);
      if($res == false) {
        echo "エラーが発生しました";
        exit;
      }

      //ユーザ情報 再読み込み role の更新
      $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
      $stmt->execute([
        ':email' => $values['email']
      ]);
      $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
      $user = $stmt->fetch();

      return $user;

    } else {
      throw new \MyApp\Exception\UnmatchChangepw();
    }
  }





  // ＊＊＊管理者権限　ユーザー情報修正＊＊＊
  public function admin_modify_user($values) {
    // DBから パスワードの変更

    $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");

    $params_id = (int)$values['id'];

    $stmt->bindParam(':id', $params_id, \PDO::PARAM_INT);

    $stmt->execute();

    $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');

    $user = $stmt->fetch();

    if(empty($user)) {
      // throw new \MyApp\Exception\UnmatchEmailOrPassword();
      echo "エラー：不正です(hidden)";
      exit;
    }
    if(!empty($user)) {

      $stmt = $this->db->prepare("UPDATE users SET email = :email, username = :username, password = :password, role = :role WHERE id = :id");

      $params_id = (int)$values['id'];
      $params_email = (string)$values['email'];
      $params_username = (string)$values['username'];

      // パスワードのバインド
      if($values['password_new'] != '') {
        // 新しいパスワードの入力があれば
        $params_password = (string)password_hash($values['password_new'], PASSWORD_DEFAULT);
      } else {
        $params_password = (string)$user->password; // h()不要
      }

      $params_role = (int)$values['role'];

      $stmt->bindParam(':id', $params_id, \PDO::PARAM_INT);
      $stmt->bindParam(':email', $params_email, \PDO::PARAM_STR);
      $stmt->bindParam(':username', $params_username, \PDO::PARAM_STR);
      $stmt->bindParam(':password', $params_password, \PDO::PARAM_STR);
      $stmt->bindParam(':role', $params_role, \PDO::PARAM_INT);

      $res = $stmt->execute();
      if($res == false) {
        echo "エラーです。メールアドレスが重複しているかも";
        exit;
      }

    } else {
      throw new \MyApp\Exception\UnmatchChangepw();
        // echo "パスワードの更新が失敗しました";-
        // exit;
    }
  }




  // ＊＊＊パスワード変更＊＊＊
  public function change_pw($values) {

    // DBから パスワードの変更
    $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([
      ':email' => $values['email']
    ]);
    $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
    $user = $stmt->fetch();

    if(empty($user)) {
      // throw new \MyApp\Exception\UnmatchEmailOrPassword();
      echo "不正なエラーです(hidden)";
    }
    if(password_verify($values['password_current'], $user->password)) {

      $stmt = $this->db->prepare("UPDATE users SET password = :password_new WHERE email = :email");

      $res = $stmt->execute([
        ':password_new' => password_hash($values['password_new'], PASSWORD_DEFAULT),
        ':email' => $values['email']
      ]);

    } else {
      throw new \MyApp\Exception\UnmatchChangepw();
        // echo "パスワードの更新が失敗しました";-
        // exit;
    }
  }




  // ＊＊＊ 退会処理 ＊＊＊
  public function delete_user($values) {

    // DBから ユーザー削除
    $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([
      ':email' => $values['email']
    ]);
    $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
    $user = $stmt->fetch();

    if(empty($user)) {
      // throw new \MyApp\Exception\UnmatchEmailOrPassword();
      echo "Emailが不正です、改変された可能性があります";
    }
    if(password_verify($values['password_current'], $user->password)) {

      $stmt = $this->db->prepare("DELETE FROM users WHERE email = :email");

      $res = $stmt->execute([
        ':email' => $values['email']
      ]);

    } else {
      throw new \MyApp\Exception\CantDeleteUser();
        // echo "パスワードの更新が失敗しました";
    }
  }

  // ＊＊＊ ページング処理 ＊＊＊
  public function paging($values) {

    $sort = $values['sort']; // ORDER BYは bind param できない注意

    $stmt = $this->db->prepare("SELECT * FROM users ORDER BY $sort LIMIT :offsetno , :countno");

    $params_offset = (int)$values['offset'];
    $params_count = (int)$values['count'];

    $stmt->bindParam(':offsetno', $params_offset, \PDO::PARAM_INT);
    $stmt->bindParam(':countno', $params_count, \PDO::PARAM_INT);

    $stmt->execute();

    $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');

    return $stmt->fetchAll();
  }



  // *******************************
  // ＊＊＊ サーチ　検索　処理 ＊＊＊
  // *******************************

  public function serching($values) {

    // SQLの準備
    $sql = 'SELECT * FROM users WHERE ' . implode(' AND ', $values['where_list']);
    // echo "<br>$sql = ";
    // echo $sql;
    // echo "<br>bind => ";
    // var_dump($values['bind_array']);

    $stmt = $this->db->prepare($sql);

    // バインド
    // $params_sql = (string)$values['bind_array'];
    foreach($values['bind_array'] as $key => &$val) {
      $stmt->bindParam($key, $val, \PDO::PARAM_STR);
    }
    unset($values['bind_array']);
    // ↑参考　http://www.irohabook.com/bindparam

    // SQL実行
    $res = $stmt->execute();

    if($res == false) {
      // 検索に失敗した
      throw new \MyApp\Exception\SearchingFailed();
      // echo "<br>FALSE";
      exit;
    } else {
      // echo "<br>SUCCESS";
    }

    $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');

    return $stmt->fetchAll();
  }



  // findAllもユーザー一覧習得するやつなのでいらないかも
  // ＊＊＊重要＊＊＊テーブル名変更時*
  public function findAllUser() {
    try {
      // トランザクションを意図的に導入
      $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
      $this->db->beginTransaction();

      $stmt = $this->db->query("SELECT * FROM users");
      $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');

      // コミット
      // $this->db->commit();
      $this->db->commit();

    } catch (\PDOException $e) {
      $this->db->rollBack();
      echo "トランザクションDBエラー";
      // echo "トランザクションエラー" . $e->getMessage();
      exit;
    }

    return $stmt->fetchAll();
  }


  // トータルユーザ数
  public function totalUsers() {
    $total = $this->db->query("SELECT count(*) FROM users")->fetchColumn();;
    return $total;
  }







  // ＊＊＊ 管理画面　ユーザ詳細を取得 ＊＊＊
  public function user_detail($values) {

    $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");

    $params_id = (int)$values['id'];

    $stmt->bindParam(':id', $params_id, \PDO::PARAM_INT);
    $stmt->execute();

    $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');

    return $stmt->fetchAll();
  }










  // ＊＊＊ リマインダー　メールの存在確認　インプット処理　その１ ＊＊＊
  public function reminder_email($values) {

    $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");

    $params_email = (string)$values['email'];

    $stmt->bindParam(':email', $params_email, \PDO::PARAM_STR);
    $res = $stmt->execute();
    if($res == false) {
      echo "エラーが発生しました";
    }

    // $stmt = $this->db->query("select * from users ORDER BY id limit 0, 2");
    $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');

    return $stmt->fetchAll();

  }
  // ＊＊＊ リマインダー　DBにトークンセット　インプット処理　その２ ＊＊＊
  public function reminder_set_token($values) {

    // データの受け取り
    $user_id = (string)$values['user_id'];
    $token = (string)$values['token'];
    // echo "結果：<br>";
    // var_dump($user_id);
    // var_dump($token);
    // exit;

    // トークンおよびユーザIDを「トークン管理テーブル」に入れる
    $sql = 'INSERT INTO reminder_token(token, user_id, created) VALUES(:token, :user_id, :created);';
    // SQLの準備
    $stmt = $this->db->prepare($sql);
    // 値のバインド
    $stmt->bindValue(':token', $token, \PDO::PARAM_STR);
    $stmt->bindValue(':user_id', $user_id, \PDO::PARAM_INT);
    $stmt->bindValue(':created', date(DATE_ATOM), \PDO::PARAM_STR);

    // $stmt->bindParam(':email', $params_email, \PDO::PARAM_STR);
    $res = $stmt->execute();
    if($res == false) {
      echo "エラーが発生しました";
      exit;
    }
  //トークンテーブルを作成したので終了

  }

  // ＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊
  // ＊＊＊ リマインダー パスワード変更 ＊＊＊
  // ＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊
  public function change_pw_reminder($values) {

    // 受け取った変数を取得
    $password_new = $values['password_new'];
    $token = $values['token'];

    // DBから パスワードの変更
    $stmt = $this->db->prepare("SELECT * FROM reminder_token WHERE token = :token");
    // バインド
    $stmt->bindValue(':token', $token, \PDO::PARAM_STR);

    $res = $stmt->execute();
    if($res == false) {
      echo "エラー：システムエラー";
      exit;
    }

    //　トークンチェック
    // データの取得(０件または１件なのが明確なので、fetchで)
    $datum = $stmt->fetch(\PDO::FETCH_ASSOC);
    if (true === empty($datum)) {
      // XXX 本当はもう少し丁寧なエラーページを出力する
      echo '無効なトークンです';
      exit;
    }

    // この時点で、トークンの有効無効にかかわらず「このトークンは不要(使い終わったかもしくは使えない)」なので、とっとと削除しておく
    $sql = 'DELETE FROM reminder_token WHERE token=:token';
    $stmt = $this->db->prepare($sql);
    // 値のバインド
    $stmt->bindValue(':token', $token, \PDO::PARAM_STR);
    // SQLの実行
    $r = $stmt->execute(); // XXX エラーチェックは一端オミット

    // 有効時間をチェックする
    if (time() > (strtotime($datum['created']) + 3600)) {
        // XXX 本当はもう少し丁寧なエラーページを出力する
        echo 'トークンの有効時間(１時間)を超えています。お手数ですが、改めて「<a href="./reminder_input.php">トークンの発行</a>」から操作をお願いいたします。';
        exit;
    }

    // ------------------------------
    // UPDATE文の作成と発行
    // ------------------------------
    // 準備された文(プリペアドステートメント)の用意
    $sql = 'UPDATE users SET password=:password, modified=:modified WHERE id=:id;';
    $stmt = $this->db->prepare($sql);
    // 値のバインド
    $stmt->bindValue(':id', $datum['user_id'], \PDO::PARAM_STR);
    // パスワードは「password_hash関数」を用いる：絶対に、何があっても「そのまま(平文で)」入れないこと！！
    $stmt->bindValue(':password', password_hash($password_new, PASSWORD_DEFAULT), \PDO::PARAM_STR);
    // 日付(MySQLのバージョンが高ければ"DEFAULT CURRENT_TIMESTAMP"に頼る、という方法も一つの選択肢)
    $stmt->bindValue(':modified', date(DATE_ATOM), \PDO::PARAM_STR);
    // SQLの実行
    $r = $stmt->execute();
    if (false === $r) {
        // XXX 本当はもう少し丁寧なエラーページを出力する
        // XXX ただし「emailのUNIQUE制約エラー」は、丁寧に表示すると「不必要情報」の脆弱性になるので注意！！
        echo 'システムでエラーが起きました、すみません';
        exit;
    }
  }



  // ＊＊＊ 管理画面で ユーザを削除　アドミンを削除ではない ＊＊＊
  public function admin_delete($values) {

    $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");

    $params_id = (int)$values['id'];

    $stmt->bindParam(':id', $params_id, \PDO::PARAM_INT);
    $stmt->execute();
  }

// ＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊
// ＊＊＊ アンケート送信処理 ＊＊＊
// ＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊
  public function enq_submit($values) {

    $stmt = $this->db->prepare("INSERT INTO enq1 (gender, old, taste, opinion) VALUES (:gender, :old, :taste, :opinion)");
    $res = $stmt->execute([
      ':gender' => (int)$values['gender'],
      ':old' => (int)$values['old'],
      ':taste' => (int)$values['taste'],
      ':opinion' => (string)$values['opinion']
    ]);
    // echo "kitakita";
    // var_dump($stmt);

    // exit;
    if ($res === false) {
      throw new \MyApp\Exception\EnqEmpty();
    }
  }


  // ＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊
  // ＊＊＊ CSVエクスポート ＊＊＊
  // ＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊
    /*
      define csv file information
    */
  public function export_csv() {

    if (isset($_POST["export_csv"])) {

      // DBからユーザの確認
      $stmt = $this->db->prepare("SELECT * FROM enq1");
      $stmt->execute();

      //CSV文字列生成
      $csvstr = "";

      $csvstr .= 'gender' . ",";
      $csvstr .= 'old' . ",";
      $csvstr .= 'taste' . "\r\n";

      while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        $csvstr .= $row['gender'] . ",";
        $csvstr .= $row['old'] . ",";
        $csvstr .= $row['taste'] . "\r\n";
      }

      //CSV出力
      $fileNm = "アンケート1.csv";
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename='.$fileNm);

      // 書き込み処理
      // echo mb_convert_encoding($csvstr, "SJIS", "UTF-8"); //Shift-JISに変換したい場合のみ
      echo $csvstr;

      // header('Location: ' . SITE_URL . index.php);
      exit();
    }
  }



  // ＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊
  // ＊＊＊ chart チャートグラフ ＊＊＊
  // ＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊
  public function findEnq() {
    $stmt = $this->db->query("select * from enq1");
    $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
    return $stmt->fetchAll();
  }






// ＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊
// ＊＊＊　これより上に書く　＊＊＊＊＊＊＊＊＊
// ＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊
}
