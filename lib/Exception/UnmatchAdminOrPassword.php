<?php
namespace MyApp\Exception;

class UnmatchAdminOrPassword extends \Exception {
  protected $message = 'Admin/Password do not match!';
}
