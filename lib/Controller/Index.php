<?php

namespace MyApp\Controller;

class Index extends \MyApp\Controller {
  public function run() {
    if(!$this->isLoggedIn()) {
      // login
      header('Location: ' . SITE_URL . '/login.php');
      exit;
    }

    // get users info
    // user一覧を表示する＝＞不要になると思う-
    $userModel = new \MyApp\Model\User();
    $this->setValues('users', $userModel->findAllUser());
  }
}
