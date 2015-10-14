<?php

set_time_limit(0);
/*
// Make sure there's at least 100 songs in the queue

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
   
   $soundtrackId = $params->soundtrackId;
   $accountId = $params->accountId;
   
   // Find a song from one of the 'start_song' playlists
   // that isn't in their queue yet.

   $sql = "SELECT * FROM playlist_song, song WHERE playlist_id IN " .
               "(SELECT playlist_id FROM soundtrack_playlist WHERE soundtrack_id = $soundtrackId AND start_songs = 1) " .
               "AND playlist_song.song_id = song.song_id " .
               "AND playlist_song.status = 0 " .
               "AND playlist_song.song_id NOT IN " .
                  "(SELECT song_id FROM account_queue WHERE account_id = $accountId AND played IS NOT NULL AND played < DATE_SUB(NOW(), INTERVAL 1 DAY)) LIMIT 1";
   
   //echo $sql;
   
   $rows = mysql_query($sql);

   if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
   {
      $json['song'] = songToJson($row);
      $json['result'] = true;
   }
   
   db_close($db);
}
catch (Exception $e)
{
   db_close($db);
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

function songToJson($row)
{
   return array('songId' => intval($row['song_id']),
                'name' => $row['name'],
                'artist' => $row['artist'],
                'album' => $row['album'],
                'spotify' => $row['spotify'],
                'rdio' => $row['rdio']);
}
*/

?>
