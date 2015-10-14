<?php

require_once("../../includes/rdio/rdio.php");
require_once("../includes/common.php");

define('RDIO_CONSUMER_KEY', '9xpz5b9neg4fyrgq37y2ft9s');
define('RDIO_CONSUMER_SECRET', 'XnbQME3KA7');

function get_redirect_url($url)
{
   $redirect_url = null;

   $url_parts = @parse_url($url);
   
   if (!$url_parts) return false;
   if (!isset($url_parts['host'])) return false; //can't process relative URLs
   if (!isset($url_parts['path'])) $url_parts['path'] = '/';

   $sock = fsockopen($url_parts['host'], (isset($url_parts['port']) ? (int)$url_parts['port'] : 80), $errno, $errstr, 30);

   if (!$sock) 
      return false;

   $request = "HEAD " . $url_parts['path'] . (isset($url_parts['query']) ? '?'.$url_parts['query'] : '') . " HTTP/1.1\r\n";
   $request .= 'Host: ' . $url_parts['host'] . "\r\n";
   $request .= "Connection: Close\r\n\r\n";

   fwrite($sock, $request);

   $response = '';

   while (!feof($sock)) $response .= fread($sock, 8192);
   
   fclose($sock);

   if (preg_match('/^Location: (.+?)$/m', $response, $matches))
   {
      if ( substr($matches[1], 0, 1) == "/" )
         return $url_parts['scheme'] . "://" . $url_parts['host'] . trim($matches[1]);
      else
         return trim($matches[1]);
   } 

   return false;
}

function emailMe($trackName, $album, $artist, $rdioUrl)
{
	$to = 'john@jalbano.net';
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'To: John <john@jalbano.net.com>' . "\r\n";
	$headers .= 'From: Admin <noreply@soundtrack4.com>' . "\r\n";
	
	$message = "Couldn't find Spotify match for:<br><br>";
	$message .= "Track: $trackName<br>";
	$message .= "Album: $album<br>";
	$message .= "Artist: $artist<br>";
	$message .= "Rdio: <a href=\"$rdioUrl\">$rdioUrl</a><br>";
	
	$subject = "ST4: Can't find song in Spotify";
	
	mail($to, $subject, $message, $headers);
}

function syncPlaylist($playlistId)
{
   echo "syncPlaylist\n";
   
   $rdio = new Rdio(array(RDIO_CONSUMER_KEY, RDIO_CONSUMER_SECRET));

   if (!$playlistId)
      throw new Exception('playlistId-required');

   $sql = "SELECT * FROM playlist WHERE playlist_id = $playlistId";
   
   $rows = mysql_query($sql);
   $row = mysql_fetch_array($rows, MYSQL_ASSOC);
   
   $rdioUrl = $row['rdio'];

   echo "rdio = $rdioUrl" . "\n";

   $playlistUrl = get_redirect_url($rdioUrl); 
   
   echo "url = $playlistUrl" . "\n";
   
   if (!ereg("(.+)\/playlists\/([^/]+)/(.+)", $playlistUrl, $regs))
   {
      // That didn't work, try one more time
      
      $playlistUrl = get_redirect_url($playlistUrl); 
   
      echo "url = $playlistUrl" . "\n";
      
      if (!ereg("(.+)\/playlists\/([^/]+)/(.+)", $playlistUrl, $regs))
      {
         mysql_query("UPDATE playlist SET status = -2 WHERE playlist_id = $playlistId");
         throw new Exception("bad-rdio-url: $playlistUrl");
      }
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
   
   // Update the rdioId
   
   $ret = api('playlist', 'update', array('playlistId' => $playlistId, 'rdioId' => $rdioId));
   
   echo "playlist.update = " . json_encode($ret) . "\n";
      
   // Process tracks

   $rdioKeys = array();   
   $trackKeys = $playlist->trackKeys;
   
   foreach ($trackKeys as $trackKey)
   {
      $rdioKeys[] = "'$trackKey'";
      
      // Add to our database if doesn't exist yet
      
      echo "looking for song: " . $trackKey . "\n";
      
      $ret = api('song', 'read', array('rdio' => $trackKey));

      echo "song.read = " . json_encode($ret) . "\n";
      
      if (!$ret->result)
      {
         $params = array();
         $params['keys'] = $trackKey;
      
         $search = $rdio->call('get', $params);
      
         $result = $search->result;
         $track = $result->$trackKey;
         
         // If this is a song we can't play in the US, skip it
         
         if (!$track->canStream)
            continue;

         $album = $track->album;
         $artist = $track->artist;
         $name = $track->name;
         $duration = $track->duration;
         
         echo "track = $name - $artist - $album\n";
   
         // We need the spotify url too

         $spotify = null;

         /*
         $search = "$name $artist $album";
         $search = urlencode($search);
         
         //$search = str_replace(":", "", $search);
         //$search = str_replace("&", "", $search);
         //$search = str_replace("-", "", $search);
         //$search = str_replace(" ", "+", $search);
         //$search = str_replace("++", "+", $search);
         
         $spotifyApiUrl = "http://ws.spotify.com/search/1/track.json?q=$search";
         
         $ret = json_decode(file_get_contents($spotifyApiUrl));
         
         //echo "spotify.read = " . print_r($ret, true) . "\n";
         
         if ($ret->info->num_results > 0)
         {
            $t = $ret->tracks[0];
            
            if ((strncmp($t->name, $name, MIN(strlen($t->name), strlen($name))) == 0) &&
                (strncmp($t->album->name, $album, MIN(strlen($t->album->name), strlen($album))) == 0) &&
                (strncmp($t->artists[0]->name, $artist, MIN(strlen($t->artists[0]->name), strlen($artist))) == 0))
            {
               $spotify = $t->href;
         
               // Remove spotify:track:
            
               $spotify = substr($spotify, strlen("spotify:track:"));
            
               echo "Found spotify match: " . $spotify . "\n";
            }
         }
         */
         
         $params = array('name' => $name,
                         'artist' => $artist,
                         'album' => $album,
                         'rdio' => $trackKey,
                         'duration' => $duration);

         if ($spotify)
         {
            $params['spotify'] = $spotify;
         }
   
         echo "song.create.params = " . json_encode($params) . "\n";
         
         $ret = api('song', 'create', $params);
         
         echo "song.create = " . json_encode($ret) . "\n";
         
         $songId = $ret->songId;
      }
      else
      {
         $songId = $ret->song->songId;
         
         // TODO: Update spotify playlist
      }      
          
      echo "songId = " . $songId . "\n";
      
      // Add song to soundtrack
   
      if ($songId)
      {
         $params = array();
         $params['songId'] = $songId;
         $params['playlistId'] = $playlistId;
         
         $ret = api('playlist', 'add-song', $params);
         
         echo "playlist.add-song = " . print_r($ret, true) . "\n";
      }
         
      //echo "Breaking out for debugging purposes!!!!\n";
      //break;
   }

   // Done with the sync, update the playlist as such
   
   echo "UPDATE playlist SET sync = 0 WHERE playlist_id = $playlistId\n";
   
   mysql_query("UPDATE playlist SET sync = 0 WHERE playlist_id = $playlistId");

   // Mark any songs that are no longer in the playlist
      
   $sql = "UPDATE playlist_song SET status = -1 " .
            "WHERE playlist_id = $playlistId " .
            "AND song_id NOT IN (SELECT song_id FROM song WHERE rdio IN (" . implode(",", $rdioKeys) . "))";

   //echo $sql;
   
   mysql_query($sql);
}

function findSongsToRemoveFromPlaylist($playlistId)
{
   $rdio = new Rdio(array(RDIO_CONSUMER_KEY, RDIO_CONSUMER_SECRET));

   if (!$playlistId)
      throw new Exception('playlistId-required');

   $sql = "SELECT * FROM playlist WHERE playlist_id = $playlistId";
   
   $rows = mysql_query($sql);
   $row = mysql_fetch_array($rows, MYSQL_ASSOC);
   
   $rdioUrl = $row['rdio'];

   echo "rdio = $rdioUrl" . "\n";

   $playlistUrl = get_redirect_url($rdioUrl); 
   
   echo "url = $playlistUrl" . "\n";
   
   if (!ereg("(.+)\/playlists\/([^/]+)/(.+)", $playlistUrl, $regs))
   {
      mysql_query("UPDATE playlist SET status = -2 WHERE playlist_id = $playlistId");
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

   $rdioKeys = array();   
   $trackKeys = $playlist->trackKeys;
   
   foreach ($trackKeys as $trackKey)
   {
      $rdioKeys[] = "'$trackKey'";
   }
   
   $sql = "UPDATE playlist_song SET status = -1 " .
            "WHERE playlist_id = $playlistId " .
            "AND song_id NOT IN (SELECT song_id FROM song WHERE rdio IN (" . implode(",", $rdioKeys) . "))";

   mysql_query($sql);
   
   // Done with the sync, update the playlist as such
   
   mysql_query("UPDATE playlist SET songs_to_remove_status = 1 WHERE playlist_id = $playlistId");
}

?>