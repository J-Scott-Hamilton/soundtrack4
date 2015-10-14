<?php

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();

   $json['result'] = true;
   
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

?>
