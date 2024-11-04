<?php

session_start();

session_unset();

setcookie('account','', time() - 3600);
setcookie('key','', time() - 3600);

$_SESSION = [];

session_destroy();

header("Location: login.php");
exit;

?>