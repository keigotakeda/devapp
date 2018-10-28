<?php

// ini_set('display_errors', 1);
// error_reporting(E_ALL & ~E_NOTICE);

define('DSN', 'mysql:host=localhost;dbname=your_db');
define('DB_USERNAME', 'your_user');
define('DB_PASSWORD', 'your_password');

define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST']);

require_once(__DIR__ . '/../lib/functions.php');
require_once(__DIR__ . '/autoload.php');

// session_start();
// セッションスタート
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

// ページのユーザデータ数
define('USERS_PER_PAGE', 5);

// 日付関数(date)を(後で)使うのでタイムゾーンの設定
// vagrantではphp.iniの変更が大変なためそちらがutcになってるので、この記述は意味ないので、消していい
date_default_timezone_set('Asia/Tokyo');

// ログアウトタイムの設定
// $auto_logout_time = 30;
