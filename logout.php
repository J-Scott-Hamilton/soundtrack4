<?php

session_start();

require_once __DIR__ . '/includes/fb.php';

setcookie('fbs_'.$facebook->getAppId(), '', time()-100, '/');
setcookie('st4lt', '', time()-100, '/');
setcookie('st4fat', '', time()-100, '/');

$_SESSION['ST4'] = $st4Session = null;

session_destroy();

header('Location: http://' . $_SERVER['HTTP_HOST']);

?>