<?php

set_time_limit(0);

// Make sure there's at least 100 songs in the queue

require_once("../session/lookup.php");
require_once("../includes/db.php");
require_once("./funcs.php");

$json = array('result' => false);

try
{

   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));

   if(!$accountId) $accountId = $params->accountId;

   if (!$accountId)
      throw new Exception('invalid-accountId');
   
   $soundtrackId = $params->soundtrackId;

   // Always start fresh -- so erase any existing queues
   
   mysql_query("DELETE FROM account_queue WHERE account_id = $accountId");
   
   // Find a start song
   
   $sql = "SELECT * FROM playlist_song, song WHERE playlist_id IN " .
               "(SELECT playlist_id FROM soundtrack_playlist WHERE soundtrack_id = $soundtrackId AND start_songs = 1) " .
               "AND playlist_song.song_id = song.song_id " .
               "AND playlist_song.song_id NOT IN (SELECT song_id FROM account_song WHERE account_id = $accountId AND action = (-2)) " .
               "AND playlist_song.song_id NOT IN (SELECT song_id FROM account_song WHERE account_id = $accountId AND `timestamp` >= DATE_SUB(NOW(), INTERVAL 1 DAY)) " .
               "AND playlist_song.song_id NOT IN (SELECT song_id FROM account_queue WHERE account_id = $accountId) " .
               "AND playlist_song.song_id NOT IN (SELECT song_id FROM soundtrack_exclude_song WHERE soundtrack_id = $soundtrackId) " .
               "AND playlist_song.status = 0 AND song.status = 0 " .
               "ORDER BY RAND() LIMIT 1";

   $rows = mysql_query($sql);
   $startSong = null;
   
   if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
   {
      $startSong = $row;
   }
   
   addSongsToQueue($accountId, $soundtrackId, $startSong, 1);

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
