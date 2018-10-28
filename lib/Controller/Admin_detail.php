<?php

namespace MyApp\Controller;

class Admin_detail extends \MyApp\Controller {
  public function run() {
    if(!$this->isAdminLoggedIn()) {
      // 管理者ログイン失敗なら戻る
      header('Location: ' . SITE_URL . '/admin_login.php');
      exit;
    }

    // $_GETされたユーザデータを取得する

    // $sql = "select * from users limit " . $offset . "," . $count;
    try {
      if(!isset($_GET['id']) || $_GET['id'] === '') {
        // 正しい入力がされていないので、突き返す
        header('Location: ' . SITE_URL . '/admin_login.php');
        exit;
      } else {
        $id = $_GET['id'];
        $userModel = new \MyApp\Model\User();
        $this->setValues('user_detail', $userModel->user_detail([
          'id' => $id
          ])
        );
      }
    } catch (\MyApp\Exception\PagingFailed $e) {
      $this->setErrors('paging_failed', $e->getMessage());
      return;
    }
  }
}
