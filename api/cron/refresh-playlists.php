<?php

set_time_limit(0);

echo "Refreshing Playlists...\n";

require_once("../includes/db.php");
require_once("../playlist/sync-func.php");

$db = db_connect();

echo "Connected to database...\n";

do
{
   try
   {
      $sql = "SELECT * FROM playlist WHERE status = 0 AND sync = 1 AND rdio IS NOT NULL AND rdio != '' LIMIT 1";
      $rows = mysql_query($sql);
      $playlistId = null;
      
      if ($rows && ($row = mysql_fetch_array($rows, MYSQL_ASSOC)))
      {
         $playlistId = $row['playlist_id'];
         $playlistName = $row['name'];
      }
       
      if ($playlistId)
      {
         echo "Refreshing playlist: $playlistId - $playlistName...\n";
         
         syncPlaylist($playlistId);
   
         echo "Finished playlist.\n\n";
         
         sleep(5);
      }
      else
      {
         $to = 'john@jalbano.net';
         
         $headers  = 'MIME-Version: 1.0' . "\r\n";
         $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
         $headers .= 'To: John <john@jalbano.net.com>' . "\r\n";
         $headers .= 'From: Admin <noreply@soundtrack4.com>' . "\r\n";
         
         $subject = "ST4: Playlist refreshes done.";
         
         mail($to, $subject, '', $headers);      

         echo "Nothing left to do...quitting\n";
         exit();
      }
   }
   catch (Exception $e)
   {
      echo "Exception: " . $e->getMessage() . "\n";
   }
}
while (true);

db_close($db);

echo "Done\n";

?>
