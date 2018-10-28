<?php
// index から来た場合
namespace MyApp\Controller;

class Change_pw extends \MyApp\Controller {
  public function run() {
    // ログインしているか
    if(!($this->isLoggedIn())) {
      header('Location: ' . SITE_URL . login.php);
      // exit;
    }
    // ポストがあるか
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->change_pwProcess();
    }
  }

  protected function change_pwProcess() {
    // validate
    try {
      $this->_validate();
    } catch (\MyApp\Exception\UnmatchChangepw $e) {
      $this->setErrors('change_pw', $e->getMessage());
    }

    // いらない? htmlでgetvaluesで表示するときに使う
    // $this->setValues('email', $app->me()->email);


    if ($this->hasError()) {
      return;
    } else {
      try {
        // postされたデータをデータベースと照合してユーザ情報 $user を習得
        $userModel = new \MyApp\Model\User();
        // $user = $userModel->change_pw([
        $userModel->change_pw([
          'password_new' => $_POST['password_new'],
          'email' => $_POST['email'],
          'password_current' => $_POST['password_current']
        ]);
      } catch (\MyApp\Exception\UnmatchChangepw $e) {
        $this->setErrors('change_pw', $e->getMessage());
        return;
      }

      // login処理
      // session_regenerate_id(true);
      // meはhtmlに表示しないのでいらない
      // $_SESSION['me'] = $passwords;

      // redirect to Home
      header('Location: ' . SITE_URL . '/change_pw_success.php');
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
    if (!isset($_POST['password_current']) || !isset($_POST['password_new']) || !isset($_POST['password_new_check'])) {
    // if (!isset($_POST['password_new_check'])) {
      echo "不正なフォームです。(Invalid Form!)";
      exit;
    }

    // 未入力があればエラー表示
    if ($_POST['password_current'] === '' ||
        $_POST['password_new'] === ''||
        $_POST['password_new_check'] === ''
    ) {
      throw new \MyApp\Exception\UnmatchChangepw();
      // throw new \MyApp\Exception\UnmatchChangepw();
    }

    // 新規PWと確認PWの一致チェック
    if ($_POST['password_new'] !== $_POST['password_new_check']) {
      // throw new \MyApp\Exception\EmptyInput();
      throw new \MyApp\Exception\UnmatchChangepw();
      // echo "新規パスワードと確認用パスワードが違います!";
      // exit;
    }
  }
}
