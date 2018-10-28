<?php
// index から来た場合
namespace MyApp\Controller;

class Delete_user extends \MyApp\Controller {
  public function run() {
    // ログインしているか
    if(!($this->isLoggedIn())) {
      header('Location: ' . SITE_URL . login.php);
      // exit;
    }
    // ポストがあるか
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->delete_userProcess();
    }
  }

  protected function delete_userProcess() {
    // validate
    try {
      $this->_validate();
    } catch (\MyApp\Exception\CantDeleteUser $e) {
      $this->setErrors('delete_user', $e->getMessage());
    }

    if ($this->hasError()) {
      return;
    } else {
      try {
        // postされたデータをデータベースと照合してユーザ情報 $user を習得
        $userModel = new \MyApp\Model\User();
        // $user = $userModel->change_pw([
        $userModel->delete_user([
          'email' => $_POST['email'],
          'password_current' => $_POST['password_current']
        ]);
      } catch (\MyApp\Exception\CantDeleteUser $e) {
        $this->setErrors('delete_user', $e->getMessage());
        return;
      }

      // login処理
      // session_regenerate_id(true);
      // meはhtmlに表示しないのでいらない
      // $_SESSION['me'] = $passwords;

      // redirect to Home
      header('Location: ' . SITE_URL . '/delete_user_success.php');
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
    if (!isset($_POST['password_current'])) {
    // if (!isset($_POST['password_new_check'])) {
      echo "不正なフォームです。(Invalid Form!)";
      exit;
    }

    // 未入力があればエラー表示
    if ($_POST['password_current'] === '') {
      throw new \MyApp\Exception\CantDeleteUser();
      // throw new \MyApp\Exception\UnmatchChangepw();
    }
  }
}
