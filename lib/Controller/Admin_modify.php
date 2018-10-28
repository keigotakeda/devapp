<?php
// index から来た場合
namespace MyApp\Controller;

class Admin_modify extends \MyApp\Controller {
  public function run() {
    // ログインしているか
    if(!($this->isAdminLoggedIn())) {
      header('Location: ' . SITE_URL . login.php);
      // exit;
    }
    // ポストがあるか
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 修正プロセス
        $this->admin_modify();
    } else {
      // 修正画面の読み込み時
      $this->select_modify_userProcess();
    }
  }

  // 修正画面にユーザーの現在の情報を抽出
  protected function select_modify_userProcess() {
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



  // 修正プロセス *********************************
  protected function admin_modify() {
    // validate
    try {
      $this->_validate();
    } catch (\MyApp\Exception\InputError $e) {
      $this->setErrors('input_error', $e->getMessage());
    }

    // いらない? htmlでgetvaluesで表示するときに使う?
    // $this->setValues('email', $app->me()->email);


    if ($this->hasError()) {
      return;
    } else {
      try {
        // postされたデータをデータベースと照合してユーザ情報 $user を習得
        $userModel = new \MyApp\Model\User();
        // $user = $userModel->change_pw([
        $userModel->admin_modify_user([
          'id' => $_POST['id'],
          'password_new' => $_POST['password_new'],
          'email' => $_POST['email'],
          'username' => $_POST['username'],
          'role' => $_POST['role']
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
    if (!isset($_POST['username']) || !isset($_POST['email']) || !isset($_POST['password_new']) || !isset($_POST['role'])) {
    // if (!isset($_POST['password_new_check'])) {
      echo "不正なフォームです。(Invalid Form!)";
      exit;
    }

    // 未入力があればエラー表示
    if ($_POST['username'] === '' ||
        $_POST['email'] === ''||
        // $_POST['password_new'] === ''||
        $_POST['role'] === ''
    ) {
      throw new \MyApp\Exception\InputError();
      // throw new \MyApp\Exception\UnmatchChangepw();
    }
  }
}
