<?php

set_time_limit(0);

function clean($str)
{
   // Throw away things in parens, unless first thing
   // and then just throw away the parens

   if (strpos($str, "(") == 0)
   {
      $str = preg_replace("/[\(\)]/", "", $str);
   }

   $str = preg_replace("/\([^)]+\)/", "", $str);
   
   // Throw away things in brackets

   $str = preg_replace("/\[[^\]]+\]/", "", $str);
   
   // Remove everything except 0-9, a-z and spaces

   $str = strtolower($str);
   $str = preg_replace("/[^0-9,a-z, ']+/", "", $str);
   $str = preg_replace('/[[:blank:]]+/', ' ', $str);
   $str = trim($str);
   
   return $str;
}

include_once("../includes/common.php");
include_once("../includes/db.php");

$db = db_connect();

echo "Finding songs in Spotify...\n";

try
{
   $sql = "SELECT * FROM song WHERE status = 0 AND rdio IS NOT NULL AND spotify IS NULL";
   $rows = mysql_query($sql);
   
   while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
   {
      $songId = $row['song_id'];
      $name = clean($row['name']);
      $artist = clean($row['artist']);
      $album = clean($row['album']);

      $found = false;
      $searches = array();
      
      $searches[] = "$name $artist $album";
      $searches[] = "$name $artist";
      $searches[] = "$name $album";
      $searches[] = $name;
      
      foreach ($searches as $search)
      {
         $search = urlencode($search);
      
         echo "searching for: $search\n";
         
         sleep(1);   // Not so fast -- spotify gets mad
         
         $spotifyApiUrl = "http://ws.spotify.com/search/1/track.json?q=$search";
         
         $ret = json_decode(file_get_contents($spotifyApiUrl));
         
         if (!$ret)
         {
            echo "Something went wrong\n";
            
            $to = 'john@jalbano.net';
            
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers .= 'To: John <john@jalbano.net>' . "\r\n";
            $headers .= 'From: Admin <noreply@soundtrack4.com>' . "\r\n";
            
            $subject = "ST4: Spotify lookups busted";
            
            mail($to, $subject, '', $headers);      
            
            exit();
         }
         
         //echo "spotify.read = " . print_r($ret, true) . "\n";
         
         for ($i = 0; $i < MIN(10, $ret->info->num_results); $i++)
         {
            $t = $ret->tracks[$i];
           
            $spotifyName = clean(preg_replace("/\([^)]+\)/", "", $t->name));
            $spotifyArtist = clean(preg_replace("/\([^)]+\)/", "", $t->artists[0]->name));
            $spotifyAlbum = clean(preg_replace("/\([^)]+\)/", "", $t->album->name));
            $spotifyLink = substr($t->href, strlen("spotify:track:"));
      
            //echo "\tcomparing: $name, $spotifyName, match = " . (strncmp($name, $spotifyName, MIN(strlen($name), strlen($spotifyName))) == 0) . "\n";
            //echo "\tcomparing: $artist, $spotifyArtist, match = " . (strncmp($artist, $spotifyArtist, MIN(strlen($artist), strlen($spotifyArtist))) == 0) . "\n";
            //echo "\tcomparing: $album, $spotifyAlbum, match = " . (strncmp($album, $spotifyAlbum, MIN(strlen($album), strlen($spotifyAlbum))) == 0) . "\n";
            
            if (strlen($spotifyName) && strlen($spotifyAlbum) && strlen($spotifyArtist))
            {
               if ((strncmp($name, $spotifyName, MIN(strlen($name), strlen($spotifyName))) == 0) &&
                   (strncmp($album, $spotifyAlbum, MIN(strlen($album), strlen($spotifyAlbum))) == 0) &&
                   (strncmp($artist, $spotifyArtist, MIN(strlen($artist), strlen($spotifyArtist))) == 0))
               {
                  echo "\t\tFound $songId in spotify as: $spotifyLink\n";
                  
                  mysql_query("UPDATE song SET spotify = '$spotifyLink' WHERE song_id = $songId");

                  $found = true;
                  break;
               }
            }
         }
               
         if ($found)
            break;
      }
      
      if (!$found)
      {
         mysql_query("UPDATE song SET spotify = '?' WHERE song_id = $songId");
      }
   }

   $to = 'john@jalbano.net';
   
   $headers  = 'MIME-Version: 1.0' . "\r\n";
   $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
   $headers .= 'To: John <john@jalbano.net>' . "\r\n";
   $headers .= 'From: Admin <noreply@soundtrack4.com>' . "\r\n";
   
   $subject = "ST4: Spotify lookups done.";
   
   mail($to, $subject, '', $headers);      

   echo "Nothing left to do...quitting\n";
}
catch (Exception $e)
{
   echo "Exception: " . $e->getMessage() . "\n";
}

db_close($db);

echo "Done\n";

?>
