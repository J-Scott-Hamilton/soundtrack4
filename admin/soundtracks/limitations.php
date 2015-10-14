<?php

$ROOT = '../..';

set_time_limit(0);

include_once("$ROOT/api/includes/common.php");
include_once("$ROOT/api/includes/db.php");

$ret = api('soundtrack', 'read');
$soundtracks = $ret->soundtracks;

function sortByChunks($a, $b)
{
    if ($a->chunks == $b->chunks)
        return 0;

    return ($a->chunks < $b->chunks) ? -1 : 1;
}

$db = db_connect();

foreach ($soundtracks as $soundtrack)
{
   $sql = "SELECT playlist_id, weight, " .
            "(SELECT COUNT(*) FROM playlist_song WHERE soundtrack_playlist.playlist_id = playlist_song.playlist_id) as song_count, " .
            "(SELECT name FROM playlist WHERE soundtrack_playlist.playlist_id = playlist.playlist_id) as playlist_name " .
            "FROM soundtrack_playlist WHERE soundtrack_id = " . $soundtrack->soundtrackId;
          
   $rows = mysql_query($sql);
   $chunks = 1000;
   $playlists = array();
   
   while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
   {
      $playlistId = $row['playlist_id'];
      $name = $row['playlist_name'];
      $weight = $row['weight'];
      $songCount = $row['song_count'];

      $playlistChunks = (int)($songCount / $weight);
      $p = new stdClass;
      
      $p->playlistId = $playlistId;
      $p->name = $name;
      $p->weight = $weight;
      $p->songCount = $songCount;
      $p->chunks = $playlistChunks;
      
      $playlists[] = $p;
      
      $chunks = min($chunks, $playlistChunks);
   }
   
   usort($playlists, sortByChunks);
   
   $soundtrack->playlists = $playlists;
   $soundtrack->chunks = $chunks;   
}

usort($soundtracks, sortByChunks);

include_once("$ROOT/admin/includes/header.php");
include_once("$ROOT/admin/includes/body.php");

?>

<div id="main">

<a href="/admin">Admin</a>

<h1>Soundtrack Limitations</h1>

<br/>

<table class="admin">
<tr>
   <th>Profile</th>
   <th>Dayslice</th>
   <th>Activity</th>
   <th>100-Song Chunks</th>
   <th></th>
</tr>

<?php 
foreach ($soundtracks as $soundtrack)
{
?>

<tr>
   <td><?php echo $soundtrack->profileName; ?></td>
   <td><?php echo $soundtrack->daysliceName; ?></td>
   <td><?php echo $soundtrack->activityName; ?></td>
   <td><?php echo $soundtrack->chunks; ?></td>
   <td style="vertical-align:middle">
      <a href="./detail?id=<?php echo $soundtrack->soundtrackId; ?>">Edit</a>
   </td>
</tr>

<tr>
   <td></td>
   <td colspan="4">
      <table>
         <tr>
            <th>Playlist</th>
            <th>Songs</th>
            <th>Weight</th>
            <th>Chunks</th>
         </tr>
      <?php foreach ($soundtrack->playlists as $playlist) { ?>
         <tr>
            <td>
               <a href="../playlists/detail?playlistId=<?php echo $playlist->playlistId; ?>">
               <?php echo $playlist->name; ?>
               </a>
            </td>
            <td><?php echo $playlist->songCount; ?></td>
            <td><?php echo $playlist->weight; ?></td>
            <td><?php echo $playlist->chunks; ?></td>
         </tr>
      <?php } ?>
      </table>
   </td>
</tr>

<?php } ?>

</table>

</div>

<?php db_close($db); ?>
      
<?php include("$ROOT/admin/includes/footer.php"); ?>