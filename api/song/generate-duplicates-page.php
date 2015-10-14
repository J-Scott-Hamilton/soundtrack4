<?php

set_time_limit(0);

require_once("../includes/db.php");

$db = db_connect();

$params = json_decode(file_get_contents('php://input'));

$sql  = "SELECT s.*, ";
$sql .= "(SELECT name FROM activity WHERE activity.activity_id = s.activity_id) as activityName, ";
$sql .= "(SELECT name FROM profile WHERE profile.profile_id = s.profile_id) as profileName, ";
$sql .= "(SELECT name FROM dayslice WHERE dayslice.dayslice_id = s.dayslice_id) as daysliceName ";
$sql .= "FROM soundtrack s";

$soundtracks = mysql_query($sql);
$badDuplicates = array();
$threshold = 4;

while ($row = mysql_fetch_array($soundtracks, MYSQL_ASSOC))
{
   $soundtrackId = $row['soundtrack_id'];
   $soundtrackName = $row['profileName'] . ' : ' . $row['daysliceName'] . ' : ' . $row['activityName'];
   
   $songPlaylists = array();
   
   $sql = "SELECT * FROM soundtrack_playlist,playlist WHERE soundtrack_id = $soundtrackId AND playlist.playlist_id = soundtrack_playlist.playlist_id";
   $playlists = mysql_query($sql);
   
   while ($row = mysql_fetch_array($playlists, MYSQL_ASSOC))
   {
      $playlistId = $row['playlist_id'];
      $playlistName = $row['name'];
      $playlistRdio = $row['rdio'];

      $sql = "SELECT * FROM playlist_song,song WHERE playlist_id = $playlistId AND song.song_id = playlist_song.song_id";
      $songs = mysql_query($sql);
      
      while ($row = mysql_fetch_array($songs, MYSQL_ASSOC))
      {
         $songId = $row['song_id'];
         
         if (!array_key_exists($songId, $songPlaylists))
         {
            $songName = $row['name'];
            $songArtist = $row['artist'];
            
            $songPlaylists[$songId] = array(
                                          'songName' => $songName,
                                          'songArtist' => $songArtist,
                                          'playlists' => array());
         }
         
         $songPlaylists[$songId]['playlists'][] = array(
                                                    'playlistId' => $playlistId,
                                                    'playlistName' => $playlistName,
                                                    'playlistRdio' => $playlistRdio);
      }
   }
   
   $duplicates = array();
   
   foreach ($songPlaylists as $songId => $data)
   {
      if (count($data['playlists']) >= $threshold)
      {
         $duplicates[$songId] = $data;
      }
   }
   
   if (count($duplicates) > 0)
   {
      $badDuplicates[$soundtrackId] = array('soundtrackName' => $soundtrackName, 'duplicates' => $duplicates);
   }
}

$body = '<div style="font-size:small"><table>';
$totalCount = 0;

foreach ($badDuplicates as $soundtrackId => $dups)
{
   // Soundtrack
   
   $body .= '<tr><td colspan="3"><a href="/admin/soundtracks/detail?id=' . $soundtrackId . '">' . $dups['soundtrackName'] . '</a></td></tr>';
   
   foreach ($dups['duplicates'] as $songId => $data)
   {
      // Song
         
      $duplicateCount = count($data['playlists']);
      $totalCount++;
      
      $body .= '<tr><td>&nbsp;&nbsp;&nbsp;</td><td colspan="2">' . $data['songName'] . " by " . $data['songArtist'] . '</td></tr>';

      foreach ($data['playlists'] as $playlist)
      {
         // Playlist
         
         $playlistId = $playlist['playlistId'];
         $playlistName = $playlist['playlistName'];
         $playlistRdio = $playlist['playlistRdio'];

         $body .= '<tr><td>&nbsp;</td><td>&nbsp;&nbsp;&nbsp;</td><td><a href="/admin/playlists/detail?playlistId=' . $playlistId . '">' . $playlistName . '</td></tr>';
      }
      
      $body .= '<tr><td>&nbsp;</td><td colspan="2"><hr></td></tr>';
   }
      
   $body .= '<tr><td colspan="3"><hr></td></tr>';
}

$body .= "<table></div>";

db_close($db);

$html = file_get_contents("song-duplication.htm");
$html = str_replace("{BODY}", $body, $html);
$f = fopen("../../admin/songs/duplication.htm", "w");
fwrite($f, $html);
fclose($f);

echo "There are $totalCount instances of song duplication of $threshold or more";

?>
