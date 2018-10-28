<?php

/**
 * ユーザ入力のエスケープ処理
 */
function h($s) {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

/**
 * LIKE句用のエスケープ処理
 */
function like_escape($s) {
    return str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $s);
}
