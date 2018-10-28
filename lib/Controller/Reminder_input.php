<?php

namespace MyApp\Controller;

class Reminder_input extends \MyApp\Controller {
  public function run() {
    if($this->isLoggedIn()) {
      header('Location: ' . SITE_URL);
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
    } catch (\MyApp\Exception\InvalidEmail $e) {
      // echo $e->getMessage();
      // exit;
      $this->setErrors('email', $e->getMessage());
    }

    $this->setValues('email', $_POST['email']);

    if ($this->hasError()) {
      return;
    } else {
      // create user
      // try {
      // エラーを出したくないのでtry catchしない
        $userModel = new \MyApp\Model\User();
        $email = $_POST['email']; // これは肝!for $datumに格納
        $datum = array(); // これは肝!for $datumに格納
        $datum = $userModel->reminder_email([
          'email' => $email
        ]);
        // echo "これでどうだ<br>";
        // var_dump($datum[0]->id); // stdClassの影響で配列[0]に格納されてる
        // exit;
      // } catch (\MyApp\Exception\DuplicateEmail $e) {
      //   $this->setErrors('email', $e->getMessage());
      //   return;
      // }
      // redirect to login

      // header('Location: ' . SITE_URL . '/reminder_input.php');
      // exit;

    // ++++++ ここまでで　ユーザデータはゲット

    // ++++++ ここから
    // emailが存在していたら、作業を続行する
      if (false !== $datum) {
        // トークンを作成
        $token = hash('sha512', openssl_random_pseudo_bytes(128));
        //var_dump($token);
        $userModel->reminder_set_token([
          'user_id' => $datum[0]->id,
          'token' => $token
        ]);


        // mail用の本文を作成する
    $site_url = SITE_URL . '/reminder_password_input.php?t=' . $token;
    $mail_body = <<<EOD
以下のURLに、１時間以内にアクセスして、パスワードを再設定してください。
{$site_url}
EOD;
    var_dump($mail_body); // XXX このvar_dumpはセキュリティ的には危険なので、確認が終わったら確実に消す事！！
    // mailを送信する
    // XXX mail送信処理は一端オミットします

      // 処理がうまくいったので、
      $this->setValues('is_reminder_mail_submit', true);
      }
    }
  }

  private function _validate() {
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
      echo "Invalid Token!";
      exit;
    }

    if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
      throw new \MyApp\Exception\InvalidEmail();
    }
  }
}
