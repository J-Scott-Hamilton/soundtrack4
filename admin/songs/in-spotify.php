<?php

set_time_limit(0);

$ROOT = '../..';

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

include_once("$ROOT/api/includes/common.php");
include_once("$ROOT/api/includes/db.php");

$db = db_connect();
$offset = (isset($_GET['offset'])) ? $_GET['offset'] : 0;
$limit = 50;

$sql = "SELECT * FROM song WHERE spotify IS NULL LIMIT $limit OFFSET $offset";
$rows = mysql_query($sql);
$songs = array();

while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
{
   $name = $row['name'];
   $artist = $row['artist'];
   $album = $row['album'];
   
   $songs[] = array('rdio.song' => $name, 'rdio.artist' => $artist, 'rdio.album' => $album);
}

//$songs[] = array('rdio.song' => '', 'rdio.artist' => '', 'rdio.album' => '');
//$songs[] = array('rdio.song' => 'Ni**as In Paris', 'rdio.artist' => 'JAY Z & Kanye West', 'rdio.album' => 'Watch The Throne (Deluxe Edition)');
//$songs[] = array('rdio.song' => 'Same Damn Time', 'rdio.artist' => 'Future', 'rdio.album' => 'Streets Callin');
//$songs[] = array('rdio.song' => 'You Don\'t Know Her Like I Do', 'rdio.artist' => 'Brantley Gilbert', 'rdio.album' => 'Halfway To Heaven (Deluxe Edition)');
//$songs[] = array('rdio.song' => '(Kissed You) Good Night', 'rdio.artist' => 'Gloriana', 'rdio.album' => '(Kissed You) Good Night');
//$songs[] = array('rdio.song' => 'We Are Young (feat. Janelle Monáe)', 'rdio.artist' => 'fun.', 'rdio.album' => 'Some Nights');
//$songs[] = array('rdio.song' => 'One Day At A Time (Hard Rock Sofa Radio) [feat. Black Dogs]', 'rdio.artist' => 'Jus Jack', 'rdio.album' => 'One Day At A Time (Feat. Black Dogs)');
//$songs[] = array('rdio.song' => '9pm (Till I Come)', 'rdio.artist' => 'Various Artists', 'rdio.album' => '90\'s Dance');
 	 	
$results = array();

foreach ($songs as $song)
{
   // Throw away things in parens

   $name = clean($song['rdio.song']);
   $artist = clean($song['rdio.artist']);
   $album = clean($song['rdio.album']);

   $found = false;
   $searches = array();
   
   $searches[] = "$name $artist $album";
   $searches[] = "$name $artist";
   $searches[] = "$name $album";
   $searches[] = $name;
   
   foreach ($searches as $search)
   {
      $search = urlencode($search);
   
      if (count($songs) < 10)
         echo "searching for: $search<br>\n";
      
      $spotifyApiUrl = "http://ws.spotify.com/search/1/track.json?q=$search";
      
      $ret = json_decode(file_get_contents($spotifyApiUrl));
      
      //echo "spotify.read = " . print_r($ret, true) . "\n";
      
      for ($i = 0; $i < MIN(10, $ret->info->num_results); $i++)
      {
         $t = $ret->tracks[$i];
         $spotify = $t->href;
        
         $song['spotify.song'] = $t->name;
         $song['spotify.artist'] = $t->artists[0]->name;
         $song['spotify.album'] = $t->album->name;
         $song['spotify.link'] = $spotify;
   
         $spotifyName = clean(preg_replace("/\([^)]+\)/", "", $t->name));
         $spotifyArtist = clean(preg_replace("/\([^)]+\)/", "", $t->artists[0]->name));
         $spotifyAlbum = clean(preg_replace("/\([^)]+\)/", "", $t->album->name));
   
         if (count($songs) < 10)
         {
            echo "\tcomparing: $name, $spotifyName, match = " . (strncmp($name, $spotifyName, MIN(strlen($name), strlen($spotifyName))) == 0) . "<br>\n";
            echo "\tcomparing: $artist, $spotifyArtist, match = " . (strncmp($artist, $spotifyArtist, MIN(strlen($artist), strlen($spotifyArtist))) == 0) . "<br>\n";
            echo "\tcomparing: $album, $spotifyAlbum, match = " . (strncmp($album, $spotifyAlbum, MIN(strlen($album), strlen($spotifyAlbum))) == 0) . "<br>\n";
         }
         
         if (strlen($spotifyName) && strlen($spotifyAlbum) && strlen($spotifyArtist))
         {
            if ((strncmp($name, $spotifyName, MIN(strlen($name), strlen($spotifyName))) == 0) &&
                (strncmp($album, $spotifyAlbum, MIN(strlen($album), strlen($spotifyAlbum))) == 0) &&
                (strncmp($artist, $spotifyArtist, MIN(strlen($artist), strlen($spotifyArtist))) == 0))
            {
               $song['match'] = true;
               break;
            }
         }
      }
            
      if ($song['match'])
         break;
   }

   $results[] = $song;
}

include_once("$ROOT/admin/includes/header.php");
include_once("$ROOT/admin/includes/body.php");

?>

<div id="main">

<a href="/admin">Admin</a>

<h1>Songs in Spotify</h1>

<table class="admin">
<tr>
   <th colspan="3">Rdio</th>
   <td></td>
   <th colspan="3">Spotify</th>
</tr>
<tr>
   <th>Song</th>
   <th>Artist</th>
   <th>Album</th>
   <td></td>
   <th>Song</th>
   <th>Artist</th>
   <th>Album</th>
</tr>

<?php foreach ($results as $song) { ?>
<tr>
   <td><?php echo $song['rdio.song']; ?></td>
   <td><?php echo $song['rdio.artist']; ?></td>
   <td><?php echo $song['rdio.album']; ?></td>
   <td><?php echo ($song['match']) ? "<img src=\"/images/checkmark.jpg\" />" : "&nbsp;"; ?></td>
   <td><a href="<?php echo $song['spotify.link']; ?>"><?php echo $song['spotify.song']; ?></a></td>
   <td><?php echo $song['spotify.artist']; ?></td>
   <td><?php echo $song['spotify.album']; ?></td>
</tr>
<?php } ?>

</table>

<br>

<a href="?offset=<?php echo ($offset + $limit); ?>">Next &gt;&gt;</a>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>