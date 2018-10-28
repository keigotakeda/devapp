<?php
// index から来た場合
namespace MyApp\Controller;

class Enq_submit extends \MyApp\Controller {
  public function run() {
    // ログインしているか
    if(!($this->isLoggedIn())) {
      header('Location: ' . SITE_URL . login.php);
      // exit;
    }
    // ポストがあるか
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->enq_submit();
    }
  }

  protected function enq_submit() {
    // validate
    try {
      $this->_validate();
    } catch (\MyApp\Exception\EnqEmpty $e) {
      $this->setErrors('enq_empty', $e->getMessage());
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
        $userModel->enq_submit([
          'gender' => $_POST['gender'],
          'old' => $_POST['old'],
          'taste' => $_POST['taste'],
          'opinion' => $_POST['opinion']
        ]);
      } catch (\MyApp\Exception\EnqEmpty $e) {
        $this->setErrors('enq_empty', $e->getMessage());
        return;
      }

      // login処理
      // session_regenerate_id(true);
      // meはhtmlに表示しないのでいらない
      // $_SESSION['me'] = $passwords;

      // 送信完了の表示
      $this->setValues('submit_success', 'Thank you!');

      // redirect to Home
      // header('Location: ' . SITE_URL . '/enq.php');
      // exit;
    }
  }

  private function _validate() {
    // トークンチェック
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
      echo "Invalid Token!";
      exit;
    }
    // フォームの入力チェック
    if (!isset($_POST['gender']) || !isset($_POST['old']) || !isset($_POST['taste'])) {
    // if (!isset($_POST['password_new_check'])) {
      echo "不正なフォームです。(Invalid Form!)";
      exit;
    }

    // 未入力があればエラー表示
    if ($_POST['gender'] === '' ||
        $_POST['old'] === ''||
        $_POST['taste'] === ''
    ) {
      throw new \MyApp\Exception\EnqEmpty();
      // throw new \MyApp\Exception\UnmatchChangepw();
    }
  }
}
