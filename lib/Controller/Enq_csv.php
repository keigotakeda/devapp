<?php
// index から来た場合
namespace MyApp\Controller;

class Enq_csv extends \MyApp\Controller {
  public function run() {
    // ログインしているか
    if(!($this->isLoggedIn())) {
      header('Location: ' . SITE_URL . login.php);
      // exit;
    }
    // ポストがあるか
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->enq_csv();
    }
  }

  protected function enq_csv() {

    if ($this->hasError()) {
      return;
    } else {
      try {
        // postされたデータをデータベースと照合してユーザ情報 $user を習得
        $userModel = new \MyApp\Model\User();
        // $user = $userModel->change_pw([
        $userModel->export_csv();

      } catch (\MyApp\Exception\EnqEmpty $e) {
        $this->setErrors('enq_empty', $e->getMessage());
        return;
      }

      // redirect to Home

      echo "ダウンロードできましたか？";
      // header('Location: ' . SITE_URL . '/enq.php');
      // exit;
    }
  }
}
