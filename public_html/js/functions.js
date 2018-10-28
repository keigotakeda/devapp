
"use strict";

// ログイン
function enter_to_login() {
  if(window.event.keyCode == 13) {
    document.getElementById("login").submit();
  }
}
function click_to_login() {
  document.getElementById('login').submit();
}

// サインイン（新規登録）
function enter_to_signup() {
  if(window.event.keyCode == 13) {
    document.getElementById("signup").submit();
  }
}
function click_to_signup() {
  document.getElementById('signup').submit();
}

// パスワード変更
function enter_to_change_password() {
  if(window.event.keyCode == 13) {
    document.getElementById("change_pw").submit();
  }
}
function click_to_change_password() {
  document.getElementById('change_pw').submit();
}

// アンケート送信
function enq_submit() {
  document.getElementById('enq_submit').submit();
}
