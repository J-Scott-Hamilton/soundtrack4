<?php

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();
   
   $params = json_decode(file_get_contents('php://input'));

   $songId = $params->songId;
   $playlistId = $params->playlistId;
   $rdio = $params->rdio;
   $spotify = $params->spotify;
   $offset = isset($params->offset) ? $params->offset : 0;
   $pageSize = isset($params->pageSize) ? $params->pageSize : 10;
   $search = isset($params->search) ? $params->search : null;
   
   if ($songId)
   {
      $sql = "SELECT * FROM song WHERE song_id = $songId";
      $rows = mysql_query($sql);
      
      if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $json['song'] = songToJson($row);
         $json['result'] = true;
      }
   }
   else if ($playlistId)
   {
      $sql = "SELECT *, playlist_song.status as playlist_song_status FROM playlist_song LEFT JOIN song ON playlist_song.song_id = song.song_id " .
               "WHERE playlist_id = $playlistId ORDER BY song.song_id ASC";
               
      $rows = mysql_query($sql);
      $songs = array();
      
      while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $songs[] = songToJson($row);
      }
   
      $json['songs'] = $songs;
      $json['result'] = true;
   }
   else if ($rdio)
   {
      $sql = "SELECT * FROM song WHERE rdio = '$rdio' LIMIT 1";
      $rows = mysql_query($sql);
      
      if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $json['song'] = songToJson($row);
         $json['result'] = true;
      }
   }
   else if ($spotify)
   {
      $sql = "SELECT * FROM song WHERE spotify = '$spotify' LIMIT 1";
      $rows = mysql_query($sql);
      
      if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $json['song'] = songToJson($row);
         $json['result'] = true;
      }
   }
   else if ($search)
   {
      $sql = "SELECT * FROM song WHERE MATCH (name,artist,album) AGAINST('$search')";
      $rows = mysql_query($sql);
      $songs = array();
      
      while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $songs[] = songToJson($row);
      }
   
      $json['songs'] = $songs;
      $json['result'] = true;
   }
   else
   {
      $sql = "SELECT * FROM song ORDER BY name ASC LIMIT $pageSize OFFSET $offset";
      $rows = mysql_query($sql);
      $songs = array();
      
      while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $songs[] = songToJson($row);
      }
   
      $json['songs'] = $songs;
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
