<?php
namespace MyApp\Exception;

class PagingFailed extends \Exception {
  protected $message = 'Paging Failed. cound not get users!';
}
