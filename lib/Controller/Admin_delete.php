<?php
// index から来た場合
namespace MyApp\Controller;

class Admin_delete extends \MyApp\Controller {
  public function run() {
    // ログインしているか
    if(!($this->isAdminLoggedIn())) {
      header('Location: ' . SITE_URL . '/admin_login.php');
      // exit;
    }
    // ポストがあるか
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->admin_delete_Process();
    }
  }

  protected function admin_delete_Process() {
    // validate
    try {
      $this->_validate();
    } catch (\MyApp\Exception\CantDeleteUser $e) {
      $this->setErrors('admin_delete', $e->getMessage());
    }

    if ($this->hasError()) {
      return;
    } else {
      try {
        // postされたデータをデータベースと照合してユーザ情報 $user を習得
        $userModel = new \MyApp\Model\User();
        // $user = $userModel->change_pw([
        $userModel->admin_delete([
          'id' => (int)$_POST['id']
        ]);
      } catch (\MyApp\Exception\CantDeleteUser $e) {
        $this->setErrors('admin_delete', $e->getMessage());
        return;
      }

      // login処理
      // session_regenerate_id(true);
      // meはhtmlに表示しないのでいらない
      // $_SESSION['me'] = $passwords;

      // redirect to Home
      header('Location: ' . SITE_URL . '/admin_index.php');
      exit;
    }
  }

  private function _validate() {
    // トークンチェック
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
      echo "Invalid Token!";
      exit;
    }
    // フォームの入力チェック
    if (!isset($_POST['id'])) {
    // if (!isset($_POST['password_new_check'])) {
      echo "不正なフォームです。(Invalid Form!)";
      exit;
    }

    // 未入力があればエラー表示
    if ($_POST['id'] === '') {
      throw new \MyApp\Exception\CantDeleteUser();
      // throw new \MyApp\Exception\UnmatchChangepw();
    }
  }
}
