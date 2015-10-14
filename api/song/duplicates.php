<?php

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();
   
   $params = json_decode(file_get_contents('php://input'));
   $sql = "SELECT * FROM song";
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
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

function songToJson($row)
{
   $song = array('songId' => intval($row['song_id']),
                'name' => $row['name'],
                'artist' => $row['artist'],
                'album' => $row['album'],
                'spotify' => $row['spotify'],
                'rdio' => $row['rdio'],
                'duration' => intval($row['duration']));
                
   if (isset($row['playlist_song_status']))
   {
      $song['playlistStatus'] = intval($row['playlist_song_status']);
   }
   
   return $song;
}

?>
