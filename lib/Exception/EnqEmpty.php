<?php
namespace MyApp\Exception;

class EnqEmpty extends \Exception {
  protected $message = 'アンケートで未入力の項目があります';
}
