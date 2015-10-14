<?php

set_time_limit(0);

require_once("./sync-func.php");
require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));

   $playlistId = isset($_GET['playlistId']) ? $_GET['playlistId'] : $params->playlistId;

   if (!$playlistId)
      throw new Exception('playlistId-required');

   syncPlaylist($playlistId);

   $json['result'] = true;
   
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

?>
