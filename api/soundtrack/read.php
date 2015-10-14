<?php

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();

   $params = json_decode(file_get_contents('php://input'));

   $soundtrackId = $params->soundtrackId;
   $daysliceId = $params->daysliceId;
   $activityId = $params->activityId;
   $profileId = $params->profileId;
   $songId = 10;  // $params->songId;
   
   $sql = "SELECT s.*,";
   $sql .= "(SELECT name FROM activity WHERE activity.activity_id = s.activity_id) as activityName, ";
   $sql .= "(SELECT name FROM profile WHERE profile.profile_id = s.profile_id) as profileName, ";
   $sql .= "(SELECT name FROM dayslice WHERE dayslice.dayslice_id = s.dayslice_id) as daysliceName, ";
   $sql .= "(SELECT COUNT(*) FROM soundtrack_playlist as sp WHERE sp.soundtrack_id = s.soundtrack_id AND sp.start_songs = 1) as has_start_songs, ";
   $sql .= "(SELECT SUM(weight) FROM soundtrack_playlist as sp WHERE sp.soundtrack_id = s.soundtrack_id) as percent_full ";
   $sql .= "FROM soundtrack s";

   if ($soundtrackId)
   {
      $sql .= " WHERE soundtrack_id = $soundtrackId";
      
      $rows = mysql_query($sql);
   
      if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $soundtrack = soundtrackToJson($row);
         
         // Also, read the playlists
         
         $sql = "SELECT * FROM soundtrack_playlist,playlist WHERE soundtrack_id = $soundtrackId AND soundtrack_playlist.playlist_id = playlist.playlist_id";
         
         $rows = mysql_query($sql);
         $playlists = array();
         
         while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
         {
            $playlists[] = soundtrackPlaylistToJson($row);
         }
   
         $soundtrack['playlists'] = $playlists;
         
         $json['soundtrack'] = $soundtrack;
         $json['result'] = true;
      }
   }
   else 
   {
      if ($profileId)
      {
         $sql .= " WHERE profile_id = $profileId";
      }
      else if ($daysliceId)
      {
         $sql .= " WHERE dayslice_id = $daysliceId";
      }
      else if ($activityId)
      {
         $sql .= " WHERE activity_id = $activityId";
      }
      else if ($songId)
      {
         $sql .= " WHERE soundtrack_id IN (SELECT soundtrack_id FROM soundtrack_playlist WHERE playlist_id IN (SELECT playlist_id FROM playlist_song WHERE song_id = $songId))";
      }
   
      $rows = mysql_query($sql);
      $soundtracks = array();
   
      while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $soundtracks[] = soundtrackToJson($row);
      }

      $json['soundtracks'] = $soundtracks;
      $json['result'] = true;   
   }
   
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

function soundtrackToJson($row)
{
   $json = array();
   $json['soundtrackId'] = intval($row['soundtrack_id']);
   $json['activityId'] = intval($row['activity_id']);
   $json['daysliceId'] = intval($row['dayslice_id']);
   $json['profileId'] = intval($row['profile_id']);
   $json['shuffle'] = (intval($row['shuffle']) == 1);   
   $json['hasStartSongs'] = intval($row['has_start_songs']);;
   $json['percentFull'] = intval($row['percent_full']);;

   if (isset($row['activityName']))
   {
      $json['activityName'] = $row['activityName'];
   }
   
   if (isset($row['profileName']))
   {
      $json['profileName'] = $row['profileName'];
   }
   
   if (isset($row['daysliceName']))
   {
      $json['daysliceName'] = $row['daysliceName'];
   }
   
   return $json;
}

function soundtrackPlaylistToJson($row)
{
   $json = array();
   $json['playlistId'] = intval($row['playlist_id']);
   $json['name'] = $row['name'];
   $json['weight'] = intval($row['weight']);
   $json['startSongs'] = intval($row['start_songs']);
   
   return $json;
}

?>
