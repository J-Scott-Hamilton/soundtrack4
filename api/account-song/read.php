<?php

require_once("../session/lookup.php");
require_once("../includes/db.php");

$json = array('result' => false);

try
{
   if (!$accountId)
      throw new Exception('accountId-required');

   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
   
   $sql = "SELECT * FROM account_song,song WHERE account_id = $accountId AND " .
               "account_song.song_id = song.song_id ORDER BY timestamp DESC";
   
   $rows = mysql_query($sql);
   $songs = array();
   
   while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
   {
      $songs[] = songToJson($row);
   }

   $json['songs'] = $songs;   
   $json['result'] = true;
   
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
                'timestamp' => $row['timestamp'],
                'spotify' => $row['spotify'],
                'rdio' => $row['rdio']);
}

?>
