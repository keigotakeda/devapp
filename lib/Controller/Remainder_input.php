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
    // View用
    $this->setValues('email', $_POST['email']);

    if ($this->hasError()) {
      return;
    } else {
      // create user
      // try {
      // エラーを出したくないのでtry catchしない
        $userModel = new \MyApp\Model\User();
        // Viewで表示する必要がないためsetValiesしない
        // $datum = array(); // 必要ある？
        $email = $_POST['email'];
        exit;
        $this->setValues('datum', $userModel->reminder_email([
          // 'email' => $_POST['email']
          'email' => $email
          ])
        );

        // 参考
        // $this->setValues('user_detail', $userModel->user_detail([
        //   'id' => $id
        //   ])
        // );



        // $datum = $this->getValues()->datum->email;

        echo "できた！！！！！datum Contro================ ";
        // var_dump($datum);
        exit;


      // } catch (\MyApp\Exception\DuplicateEmail $e) {
      //   $this->setErrors('email', $e->getMessage());
      //   return;
      // }
      // redirect to login

      // header('Location: ' . SITE_URL . '/reminder_input.php');
      // exit;
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
