<?php

session_start();

if ($_SERVER['HTTP_HOST'] == 'soundtrack4.tryllo.com' or true)
{
   $_SESSION['ADMINUSER'] = 'admin';
}
else if (isset($_SERVER["REMOTE_USER"]))
{
   $_SESSION['ADMINUSER'] = $_SERVER["REMOTE_USER"];
}
else if (!isset($_SESSION['ADMINUSER']))
{
   echo "Restricted";
   exit();
}

?>
