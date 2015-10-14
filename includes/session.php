<?php

session_start();
$st4Session = isset($_SESSION['ST4']) ? $_SESSION['ST4'] : null;

// If we don't have a valid session...

if (!$st4Session)
{
   require_once __DIR__ . '/api.php';

   // Auto-login?

   $loginType = isset($_COOKIE['st4lt']) ? $_COOKIE['st4lt'] : '';

   if (strcmp($loginType, 'fb') == 0)
   {
      $accessToken = $_COOKIE['st4fat'];
      $st4Session = api('session', 'create', array('facebookAccessToken' => $accessToken));
      $_SESSION['ST4'] = $st4Session;
   }
}

?>