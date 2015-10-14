<?php

set_time_limit(0);

echo "Finding unavailable songs...\n";

require_once("../includes/db.php");
require_once("../playlist/sync-func.php");

$db = db_connect();

do
{
   try
   {
      $rdio = new Rdio(array(RDIO_CONSUMER_KEY, RDIO_CONSUMER_SECRET));
   
      $sql = "SELECT * FROM playlist WHERE status = 0 AND unavailable_songs_status = 0 AND rdio IS NOT NULL AND rdio != '' LIMIT 1";
      $rows = mysql_query($sql);
      $playlistId = null;
      
      if ($rows && ($row = mysql_fetch_array($rows, MYSQL_ASSOC)))
      {
         $playlistId = $row['playlist_id'];
         $playlistName = $row['name'];
      }
       
      if ($playlistId)
      {
         echo "Checking playlist: $playlistId - $playlistName...\n";
         
         $rdioUrl = $row['rdio'];
      
         echo "rdio = $rdioUrl" . "\n";
      
         $playlistUrl = get_redirect_url($rdioUrl); 
         
         echo "url = $playlistUrl" . "\n";
         
         if (!ereg("(.+)\/playlists\/([^/]+)/(.+)", $playlistUrl, $regs))
         {
            mysql_query("UPDATE playlist SET status = -4 WHERE playlist_id = $playlistId");
            throw new Exception("bad-rdio-url: $playlistUrl");
         }
            
         $rdioId = 'p' . $regs[2];
         
         echo "id = $rdioId" . "\n";
         
         $params = array();
         
         $params['keys'] = $rdioId;
         $params['extras'] = 'trackKeys';
         
         $search = $rdio->call('get', $params);
         
         if (strcmp($search->status, "ok") != 0)
         {
            echo "rdio.call.get = " . json_encode($search) . "\n";
            mysql_query("UPDATE playlist SET status = -2 WHERE playlist_id = $playlistId");
            return;
         }
         
         $result = $search->result;
         $playlist = $result->$rdioId;
         $trackKeys = $playlist->trackKeys;
         
         // Process tracks
      
         foreach ($trackKeys as $trackKey)
         {
            $ret = api('song', 'read', array('rdio' => $trackKey));
      
            echo "song.read = " . json_encode($ret) . "\n";
            
            if (!$ret->result)
               continue;

            $songId = $ret->song->songId;

            if ($ret->song->status == (-5))
               continue;
               
            $params = array();
            $params['keys'] = $trackKey;
         
            $search = $rdio->call('get', $params);
         
            $result = $search->result;
            $track = $result->$trackKey;
            $album = $track->album;
            $artist = $track->artist;
            $name = $track->name;
            $duration = $track->duration;
            $canStream = $track->canStream;
            
            if (!$canStream)
            {
               echo "Unavailable: $name - $artist - $album\n";
               
               mysql_query("UPDATE song SET status = -5 WHERE song_id = $songId");
            }
         }
   
         mysql_query("UPDATE playlist SET unavailable_songs_status = 1 WHERE playlist_id = $playlistId");

         echo "Finished playlist.\n\n";

         sleep(3);
      }
      else
      {
         $to = 'john@jalbano.net';
         
         $headers  = 'MIME-Version: 1.0' . "\r\n";
         $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
         $headers .= 'To: John <john@jalbano.net.com>' . "\r\n";
         $headers .= 'From: Admin <noreply@soundtrack4.com>' . "\r\n";
         
         $subject = "ST4: Done finding unavailable songs.";
         
         mail($to, $subject, '', $headers);      

         echo "Done\n";
         
         exit();
      }
   }
   catch (Exception $e)
   {
      echo "Exception: " . $e->getMessage() . "\n";
   }
}
while (true);

?>
