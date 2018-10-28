<?php
// index から来た場合
namespace MyApp\Controller;

class Enq_chart extends \MyApp\Controller {
  public function run() {
    // ログインしているか
    if(!($this->isLoggedIn())) {
      header('Location: ' . SITE_URL . login.php);
      // exit;
    }
    // // ポストがあるか
    // if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //   $this->enq_chart();
    // }
    $this->enq_chart();
  }

  protected function enq_chart() {

    if ($this->hasError()) {
      return;
    } else {
      try {
        // postされたデータをデータベースと照合してユーザ情報 $user を習得
        $userModel = new \MyApp\Model\User();
        // $user = $userModel->change_pw([
        $this->setValues('enq_data', $userModel->findEnq());
        // echo $userModel->findEnq();
        // eixt;

      } catch (\MyApp\Exception\EnqEmpty $e) {
        $this->setErrors('enq_empty', $e->getMessage());
        return;
      }

      // redirect to Home

      // echo "ダウンロードできましたか？";
      // header('Location: ' . SITE_URL . '/enq.php');
      // exit;
    }
  }
}
