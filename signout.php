<?php

session_start();

// セッションにユーザーのIDが保持されtいたらサインインしている状態
// セッション変数の破棄
// ブラウザから破棄
$_SESSION = [];

// サーバー内のセッション変数のクリア
session_destroy();

header('Location: signin.php');
exit();
