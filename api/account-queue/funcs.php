<?php

function addSongsToQueue($accountId, $soundtrackId, $startSong, $startingSequence = 1)
{
   // Find the playlists we will build from
   
   $sql = "SELECT * FROM soundtrack_playlist WHERE soundtrack_id = $soundtrackId";
   
   $rows = mysql_query($sql);
   $songs = array();
   $songIds = array();

   if ($startSong)
   {
      $startSongId = intval($startSong['song_id']);
      $songIds[] = $startSongId;
   }

   while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
   {
      $playlistId = $row['playlist_id'];
      $weight = $row['weight'];

      // TODO: What if there aren't enough songs from this playlist?
   
      $sql = "SELECT * FROM playlist_song JOIN song ON playlist_song.song_id = song.song_id WHERE playlist_id = $playlistId " .
               "AND (playlist_song.song_id NOT IN (SELECT song_id FROM account_queue WHERE account_id = $accountId)) " .
               "AND (playlist_song.song_id NOT IN (SELECT song_id FROM account_song WHERE account_id = $accountId AND action = (-2))) " .
               "AND (playlist_song.song_id NOT IN (SELECT song_id FROM account_song WHERE account_id = $accountId AND `timestamp` >= DATE_SUB(NOW(), INTERVAL 1 DAY))) " .
               "AND (playlist_song.song_id NOT IN (SELECT song_id FROM soundtrack_exclude_song WHERE soundtrack_id = $soundtrackId)) ";

      if (count($songIds) > 0)
      {
         $sql .= "AND (playlist_song.song_id NOT IN (" . implode(',', $songIds) . ")) ";
      }               
      
      $sql .= "AND playlist_song.status = 0 AND song.status = 0 ORDER BY RAND() LIMIT $weight";
      
      //echo $sql;
      
      $songRows = mysql_query($sql);
      $songCount = 0;

      while ($row = mysql_fetch_array($songRows, MYSQL_ASSOC))
      {
         $songId = intval($row['song_id']);
         $songIds[] = $songId;

         $songs[] = $row;
         $songCount++;
      }
   }
   
   // Randomize the songs

   shuffle($songs);
   
   // Prepend the start song
   
   if ($startSong)
   {
      array_unshift($songs, $startSong);
   }
   
   // Try to make sure the same artist isn't too close to themselves
   // Some soundtracks don't want this

   $separateArtists = true;   
   $sql = "SELECT shuffle FROM soundtrack WHERE soundtrack_id = $soundtrackId";
   $rows = mysql_query($sql);

   if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
   {
      $separateArtists = intval($row['shuffle']);
   }
   
   if ($separateArtists)
   {
      $songCount = count($songs);
      
      for ($i = 1; $i < ($songCount - 1); $i++)
      {
         $artistA = $songs[$i-1]['artist'];
         $artistB = $songs[$i]['artist'];
         
         if (strcmp(strtolower($artistA), strtolower($artistB)) == 0)
         {
            // Found a match
            // Just swap B with C
            
            $save = $songs[$i];
            $songs[$i] = $songs[$i+1];
            $songs[$i+1] = $save;
         }
      }
   }
   
   // Append to the queue
   
   $sequence = $startingSequence;
   
   foreach ($songs as $song)
   {
      $songId = $song['song_id'];
      $playlistId = $song['playlist_id'];
      
      mysql_query("INSERT INTO account_queue (account_id, soundtrack_id, playlist_id, song_id, sequence) VALUES ($accountId, $soundtrackId, $playlistId, $songId, $sequence)");
      $sequence++;
   }
   
   
}

?>
