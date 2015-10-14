<?php

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
   
   $toSoundtrackId = $params->soundtrackId;
   $fromSoundtrackId = $params->copySoundtrackId;

   if (!$toSoundtrackId)
      throw new Exception('required-soundtrackId');
      
   if (!$fromSoundtrackId)
      throw new Exception('required-copySoundtrackId');
      
   // First, clear the target soundtrack
   
   mysql_query("DELETE FROM soundtrack_playlist WHERE soundtrack_id = $toSoundtrackId");
   
   // Now copy

   $sql = "SELECT * FROM soundtrack_playlist,playlist WHERE soundtrack_id = $fromSoundtrackId AND soundtrack_playlist.playlist_id = playlist.playlist_id";
   
   $rows = mysql_query($sql);
   
   while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
   {
      $playlistId = intval($row['playlist_id']);
      $weight = intval($row['weight']);
      $startSongs = intval($row['start_songs']);
   
      mysql_query("REPLACE INTO soundtrack_playlist (soundtrack_id, playlist_id, weight) VALUES ($toSoundtrackId, $playlistId, $weight)");
      
      if ($startSongs)
      {
         mysql_query("UPDATE soundtrack_playlist SET start_songs = 1 WHERE soundtrack_id = $toSoundtrackId AND playlist_id = $playlistId");
      }
   }
   
   $json['result'] = true;
   
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

?>
