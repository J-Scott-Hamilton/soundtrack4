<?php

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
   $sql = null;
   if(!$params) $params = json_decode($_POST['json']);
   $soundtrackId = $params->soundtrackId;
   $playlistId = $params->playlistId;
   $startSongs = $params->startSongs;
   
   if (isset($params->weight))
   {
      $weight = $params->weight;
      
      if ($weight > 0)
      {
         $sql = "REPLACE INTO soundtrack_playlist (soundtrack_id, playlist_id, weight) VALUES ($soundtrackId, $playlistId, $weight)";
      }
      else
      {
         $sql = "DELETE FROM soundtrack_playlist WHERE soundtrack_id = $soundtrackId AND playlist_id = $playlistId";  
      }
   }
   
   if (isset($params->startSongs))
   {
      $set = ($params->startSongs ? 1 : 0);
      $sql = "UPDATE soundtrack_playlist SET start_songs = $set WHERE soundtrack_id = $soundtrackId AND playlist_id = $playlistId";
   }
   
   mysql_query($sql);

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
