<?php
namespace MyApp\Exception;

class CantDeleteUser extends \Exception {
  protected $message = 'ユーザの削除処理に失敗しました! パスワードを確認してください';
}
