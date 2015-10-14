<?php

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
      
   if (!$params->songId)
      throw new Exception('missing songId');

   $songId = $params->songId;

   mysql_query("UPDATE song SET status = (-2) WHERE song_id = $songId");

   $json['result'] = true;
   
   db_close($db);
}
catch (Exception $e)
{
   db_close($db);
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

?>
