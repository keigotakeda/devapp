<?php
// index から来た場合
namespace MyApp\Controller;

class Register_premium extends \MyApp\Controller {
  public function run() {
    // ログインしているか
    if(!($this->isLoggedIn())) {
      header('Location: ' . SITE_URL . login.php);
      // exit;
    }
    // ポストがあるか
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->register_process();
    }
  }

  protected function register_process() {
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
        $user = $userModel->register([
          'register' => $_POST['register'],
          'email' => $_POST['email'],
          'password_current' => $_POST['password_current']
        ]);
      } catch (\MyApp\Exception\UnmatchChangepw $e) {
        $this->setErrors('change_pw', $e->getMessage());
        return;
      }

      // login処理
      // ログイン時にSSIDを新規作成＝＞CSRF, ハイジャック対策
      // session_regenerate_id(true);
      // セッションが正しいか me で確認
      $_SESSION['me'] = $user;

      // redirect to Home
      header('Location: ' . SITE_URL . '/register_success.php');
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
    if (!isset($_POST['password_current']) || !isset($_POST['register'])) {
    // if (!isset($_POST['password_new_check'])) {
      echo "不正なフォームです。(Invalid Form!)";
      exit;
    }

    // 未入力があればエラー表示
    if ($_POST['password_current'] === '' ||
        $_POST['register'] === ''
    ) {
      throw new \MyApp\Exception\UnmatchChangepw();
      // throw new \MyApp\Exception\UnmatchChangepw();
    }
  }
}
