<?php

namespace MyApp\Controller;

// class Auto_logout extends \MyApp\Controller {
class Auto_logout {
  public function run() {

    // // ログアウトタイムの設定
    $auto_logout_time = 3600;

    // echo "set before = ";
    // var_dump($_SESSION["auto_logout"]);

    // ログアウトタイム処理
    // ログインした状態でページを読み込んだときに、ログアウトタイムを経過していれば突き返す
    // if (isset($_SESSION["auto_logout"]) &&  $_SESSION["auto_logout"] != '') {
    if (isset($_SESSION["auto_logout"])) {

      $logout_time = $_SESSION['auto_logout'];

      if(time() > $logout_time) {
        //セッションを破壊して強制ログアウト
        $_SESSION = [];

        if (isset($_COOKIE[session_name()])) {
          setcookie(session_name(), '', time() - 86400, '/');
        }

        session_destroy();

        // echo "自動ログアウト発動！";
        // exit;

        header('Location: ' . SITE_URL);
        exit;
      }
    }

    // ログアウトタイム設定
    // $_SESSION['auto_logout'] = '';
    $_SESSION['auto_logout'] = time() + $auto_logout_time;

    // echo "set after = ";
    // var_dump($_SESSION["auto_logout"]);

  }
}
