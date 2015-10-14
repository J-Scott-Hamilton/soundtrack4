<?php

$ROOT = '../..';
include_once("$ROOT/api/includes/common.php");

$playlistId = $_GET['playlistId'];

if (isset($_GET['delete']))
{
   api('playlist', 'delete', array('playlistId' => $playlistId));
   header("Location: http://" . $_SERVER['HTTP_HOST'] . '/admin/playlists');
   exit();
}
else if (isset($_GET['update']))
{
   $ret = api('playlist', 'update', array('playlistId' => $playlistId,
                                          'name' => $_GET['playlistName'], 
                                          'rdioUrl' => $_GET['rdio'],
                                          'comment' => $_GET['comment'],
                                          'sync' => ($_GET['sync'] == "on") ? 1 : 0));
}

$ret = api('playlist', 'read', array('playlistId' => $playlistId));
$playlist = $ret->playlist;

$ret = api('soundtrack', 'read', array('playlistId' => $playlistId));
$soundtracks = $ret->soundtracks;

$ret = api('song', 'read', array('playlistId' => $playlistId));
$songs = $ret->songs;

include_once("$ROOT/admin/includes/header.php");

?>

<script>

function syncPlaylist()
{
   var json = {
            playlistId: <?php echo $playlistId; ?>
         };
         
   $.post("<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/api/playlist/sync'; ?>", 
   {
      json: JSON.stringify(json)
   }, function(data, status, jqxhr){
      location.reload();
   });
}

function deleteFromPlaylist(songId)
{
   var json = {
            playlistId: <?php echo $playlistId; ?>,
            songId: songId
         };
         
   $.post('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/api/playlist/delete-song'; ?>', 
   {
      json: JSON.stringify(json)
   },
   function(data)
   {
      $('#status_' + songId).attr("src", "<?php echo "$SITEROOT/images/checkmark.jpg"; ?>");
   });
}

function ignoreFromRdio(songId)
{
   var json = {
            playlistId: <?php echo $playlistId; ?>,
            songId: songId
         };
         
   $.post('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/api/playlist/ignore-song'; ?>', 
   {
      json: JSON.stringify(json)
   },
   function(data)
   {
      $('#status_ignore_' + songId).attr("src", "<?php echo "$SITEROOT/images/checkmark.jpg"; ?>");
   });
}

$(document).ready(function()
{   
});

</script>

<?php include_once("$ROOT/admin/includes/body.php"); ?>

<div id="main">

<a href="/admin">Admin</a> : 
<a href="/admin/playlists">Playlists</a>

<h1><?php echo $playlist->name; ?></h1>

<hr/>

<form>
<table>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="playlistName">Name:</label></td>
   <td><input name="playlistName" size="50" value="<?php echo htmlentities($playlist->name, ENT_QUOTES); ?>" /></td>
</tr>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="rdio">Rdio Playlist:</label></td>
   <td><input name="rdio" size="50" value="<?php echo $playlist->rdioUrl; ?>" /><br/></td>
</tr>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="rdio">Synchronize:</label></td>
   <td><input name="sync" type="checkbox" <?php if ($playlist->sync) { echo "checked=\"checked\""; } ?> /><br/></td>
</tr>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="comment">Comment:</label></td>
   <td><input name="comment" size="50" value="<?php echo htmlentities($playlist->comment, ENT_QUOTES); ?>" /><br/></td>
</tr>
<tr>
   <td></td>
   <td><input type="submit" name="update" value="Update Playlist" /></td>
</tr>
</table>
<input type="hidden" name="playlistId" value="<?php echo $playlistId; ?>" />
</form>

<br/>
<hr/>

<h3>Songs (<a href="javascript:syncPlaylist();">Refresh</a>)</h3>

<table id="songs" class="admin">
   <tr>
      <th>Track</th>
      <th>Artist</th>
      <th>Album</th>
      <th>Starter Song</th>
      <th>Deleted From Rdio</th>
      <th>Ignore From Rdio</th>
   </tr>
<?php foreach ($songs as $song) { ?>
   <tr>
      <td><a href="/admin/songs/detail?songId=<?php echo $song->songId; ?>"><?php echo $song->name; ?></a></td>
      <td><?php echo $song->artist; ?></td>
      <td><?php echo $song->album; ?></td>
      <td></td>
      <td style="text-align:center;vertical-align:middle">
         <?php if ($song->playlistStatus == (-1)) { ?>
         <a style="cursor:pointer" onclick="deleteFromPlaylist(<?php echo $song->songId; ?>);">
         <img class="statusIcon" id="status_<?php echo $song->songId; ?>" src="<?php echo "$SITEROOT/images/alert.jpg"; ?>" title="Delete From Playlist" />
         </a>
         <?php } ?>
      </td>
      <td style="text-align:center;vertical-align:middle">
         <?php if ($song->playlistStatus == 0) { ?>
         <a style="cursor:pointer" onclick="ignoreFromRdio(<?php echo $song->songId; ?>);">
         <img class="statusIcon" id="status_ignore_<?php echo $song->songId; ?>" src="<?php echo "$SITEROOT/admin/images/redx.jpg"; ?>" width="24px" height="24px" title="Ignore From Rdio" />
         </a>
         <?php } ?>
      </td>
   </tr>
<?php } ?>
</table>

<br/>
<hr/>

<h3>Soundtracks</h3>
<p>This playlist is used in the following soundtracks...</p>

<table class="admin">
   <tr>
      <th>Profile</th>
      <th>Day-Slice</th>
      <th>Activity</th>
   </tr>
<?php foreach ($soundtracks as $soundtrack) { ?>
   <tr>
      <td><a href="/admin/profiles/detail?profileId=<?php echo $profile->profileId; ?>"><?php echo $soundtrack->profileName; ?></a></td>
      <td><?php echo $soundtrack->daysliceName; ?></td>
      <td><?php echo $soundtrack->activityName; ?></td>
   </tr>
<?php } ?>
</table>

<br/>
<hr/>
<br/>

<a href="?playlistId=<?php echo $playlistId; ?>&delete=1">Delete this Playlist</a>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>
