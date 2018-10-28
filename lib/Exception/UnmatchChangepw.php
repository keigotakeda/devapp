<?php
namespace MyApp\Exception;

class UnmatchChangepw extends \Exception {
  protected $message = 'パスワードの正当性が確認できませんでした（unmatch password!）!';
}
