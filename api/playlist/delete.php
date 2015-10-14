<?php

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));

   $playlistId = $params->playlistId;

   if ($playlistId)
   {
      $sql = "DELETE FROM playlist WHERE playlist_id = $playlistId";
      $rows = mysql_query($sql);

      $sql = "DELETE FROM playlist_song WHERE playlist_id = $playlistId";
      $rows = mysql_query($sql);

      $json['result'] = true;
   }
      
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

?>
