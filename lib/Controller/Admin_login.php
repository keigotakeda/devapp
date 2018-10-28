<?php

namespace MyApp\Controller;

class Admin_login extends \MyApp\Controller {
  public function run() {
    if($this->isAdminLoggedIn()) {
      header('Location: ' . SITE_URL . '/admin_index.php');
      exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->postProcess();
    }
  }

  protected function postProcess() {
    // validate
    try {
      $this->_validate();
    } catch (\MyApp\Exception\EmptyPost $e) {
      $this->setErrors('empty_post', $e->getMessage());
    }

    $this->setValues('admin', $_POST['admin']);

    if ($this->hasError()) {
      return;
    } else {
      try {
        // Model -> Admin.phpを開く
        $adminModel = new \MyApp\Model\Admin();
        $admin = $adminModel->login([
          'admin' => $_POST['admin'],
          'password' => $_POST['password']
        ]);
      } catch (\MyApp\Exception\UnmatchAdminOrPassword $e) {
        $this->setErrors('unmatch', $e->getMessage());
        return;
      }

      // login処理
      // ログイン時にSSIDを新規作成＝＞CSRF, ハイジャック
      session_regenerate_id(true);
      // セッションが正しいか me で確認
      // $_SESSION['me'] = $admin; // 脆弱性：ユーザ、アドミン行き来可能
      $_SESSION['admin'] = $admin;

      // redirect to Admin
      header('Location: ' . SITE_URL . '/admin_index.php');
      exit;
    }
  }

  private function _validate() {
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
      echo "Invalid Token!";
      exit;
    }

    if (!isset($_POST['admin']) || !isset($_POST['password'])) {
      echo "Invalid Form!";
      exit;
    }

    if ($_POST['admin'] === '' || $_POST['password'] === '') {
      throw new \MyApp\Exception\EmptyPost();
    }
  }
}
