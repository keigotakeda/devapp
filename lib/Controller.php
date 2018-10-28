<?php

namespace MyApp;

class Controller {

  private $_errors;
  private $_values;

  public function __construct() {

    // 最初にトークンをセット
    if(!isset($_SESSION['token'])) {
      $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(16));
    }

    $this->_errors = new \stdClass();
    $this->_values = new \stdClass();
  }

  protected function setValues($key, $value) {
    $this->_values->$key = $value;
  }

  public function getValues() {
    return $this->_values;
  }

  protected function setErrors($key, $error) {
    $this->_errors->$key = $error;
  }

  public function getErrors($key) {
    return isset($this->_errors->$key) ?  $this->_errors->$key : '';
  }

  protected function hasError() {
    return !empty(get_object_vars($this->_errors));
  }

// ユーザ　Controller用
  protected function isLoggedIn() {
    // $_SESSION['me']
    return isset($_SESSION['me']) && !empty($_SESSION['me']);
  }

// 管理者　Controller用
  protected function isAdminLoggedIn() {
    // $_SESSION['me']
    return isset($_SESSION['admin']) && !empty($_SESSION['admin']);
  }

// Viewからアクセスする用
  public function me() {
    return $this->isLoggedIn() ? $_SESSION['me'] : null;
  }

// Viewからアクセスする用
  public function admin_me() {
    return $this->isAdminLoggedIn() ? $_SESSION['admin'] : null;
  }

}
