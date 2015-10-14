<?php

set_time_limit(0);

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();

   $params = json_decode(file_get_contents('php://input'));

   $playlistId = $params->playlistId;
   $rdioId = $params->rdioId;
   $songId = $params->songId;
   
   if ($playlistId)
   {
      $sql = "SELECT *, " .
               "(SELECT COUNT(*) FROM playlist_song WHERE playlist_song.playlist_id = $playlistId AND playlist_song.status = 0) as playlist_song_count " .
               "FROM playlist WHERE playlist_id = $playlistId";
               
      $rows = mysql_query($sql);
   
      if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $json['playlist'] = playlistToJson($row);
         $json['result'] = true;
      }
   }
   else if ($rdioId)
   {
      $sql = "SELECT * FROM playlist WHERE rdio_id = '$rdioId'";
      $rows = mysql_query($sql);
   
      if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $json['playlist'] = playlistToJson($row);
         $json['result'] = true;
      }
   }
   else if ($songId)
   {
      $sql = "SELECT * FROM playlist WHERE playlist_id IN (SELECT playlist_id FROM playlist_song WHERE song_id = $songId)";

      $rows = mysql_query($sql);
      $playlists = array();
   
      while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $playlists[] = playlistToJson($row);
      }
   
      $json['playlists'] = $playlists;
      $json['result'] = true;   
   }
   else
   {
      $sql = "SELECT *, " .
               "(SELECT COUNT(*) FROM playlist_song WHERE playlist.playlist_id = playlist_song.playlist_id) as playlist_song_count, " .
               "(SELECT COUNT(*) FROM playlist_song WHERE playlist.playlist_id = playlist_song.playlist_id AND playlist_song.status = (-1)) as song_to_delete_count " .
               "FROM playlist";
      
      $rows = mysql_query($sql);
      $playlists = array();
   
      while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $playlists[] = playlistToJson($row);
      }
   
      $json['playlists'] = $playlists;
      $json['result'] = true;   
   }
   
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

function playlistToJson($row)
{
   $json = array();
   $json['playlistId'] = intval($row['playlist_id']);
   $json['name'] = $row['name'];
   $json['rdioUrl'] = $row['rdio'];
   $json['rdioId'] = $row['rdio_id'];
   $json['sync'] = $row['sync'];
   $json['status'] = intval($row['status']);
   $json['comment'] = $row['comment'];
   
   if (isset($row['playlist_song_count']))
   {
      $json['songCount'] = intval($row['playlist_song_count']);
   }
   
   if (isset($row['song_to_delete_count']))
   {
      $json['songsToDeleteCount'] = intval($row['song_to_delete_count']);
   }
   
   return $json;
}

?>
