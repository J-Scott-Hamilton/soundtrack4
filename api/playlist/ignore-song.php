<?php

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
      
   $songId = $params->songId;
   $playlistId = $params->playlistId;
   
   if (!$songId)
      throw new Exception('songId-required');

   if (!$playlistId)
      throw new Exception('playlistId-required');
   
   $sql = "UPDATE playlist_song SET status = (-2) WHERE song_id = $songId AND playlist_id = $playlistId LIMIT 1";
   $rows = mysql_query($sql);
   
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
