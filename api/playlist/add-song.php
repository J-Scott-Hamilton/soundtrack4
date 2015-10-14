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
   
   $sql = "INSERT INTO playlist_song (song_id, playlist_id) VALUES ($songId, $playlistId) ON DUPLICATE KEY UPDATE song_id = song_id";
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
