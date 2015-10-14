<?php

$sessionId = $_POST['sessionId'];

session_id($sessionId);
session_start();

$accountId = $_SESSION['accountId'];
   
$json = array('result' => false);

try
{
   session_destroy();
   
   $json['result'] = true;
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

?>
