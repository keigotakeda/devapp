<?php

namespace MyApp\Model;

class Admin extends \MyApp\Model {

  // 管理者 ログイン 処理
  public function login($values) {
    $stmt = $this->db->prepare("SELECT * FROM admins WHERE admin = :admin");
    $stmt->execute([
      ':admin' => $values['admin']
    ]);
    $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
    $admin = $stmt->fetch();

    if(empty($admin)) {
      throw new \MyApp\Exception\UnmatchAdminOrPassword();
    }
    if(!password_verify($values['password'], $admin->password)) {
      throw new \MyApp\Exception\UnmatchAdminOrPassword();
    }
    return $admin;
  }
}
