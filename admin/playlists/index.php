<?php

$ROOT = '../..';

include_once("$ROOT/admin/includes/security.php");
include_once("$ROOT/api/includes/common.php");

if (isset($_GET['delete']))
{
   api('playlist', 'delete', array('playlistId' => $_GET['playlistId']));
}

if (isset($_GET['playlistName']))
{
   if (strlen($_GET['playlistName']) > 0)
   {
      $ret = api('playlist', 'create', array('name' => $_GET['playlistName'], 
                                             'rdioUrl' => $_GET['rdio'],
                                             'comment' => $_GET['comment'],
                                             'sync' => ($_GET['sync'] == "on") ? 1 : 0));
      
      $playlistId = $ret->playlistId;
      
      // Start building playlist

      $ret = api('playlist', 'refresh', array('playlistId' => $_GET['playlistId']));
      
      //echo "playlist.refresh = " . json_encode($ret) . "<br><hr><br>\n";
   }
}

$ret = api('playlist', 'read');
$playlists = $ret->playlists;

include_once("$ROOT/admin/includes/header.php");

?>

<script>

function refreshPlaylist(playlistId)
{
   var json = {
            playlistId: playlistId
         };
         
   $.post('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/api/playlist/refresh'; ?>', 
   {
      json: JSON.stringify(json)
   },
   function(data)
   {
      $('#status_' + playlistId).attr("src", "<?php echo "$SITEROOT/images/ajax-loader.gif"; ?>");
      $('#status_' + playlistId).addClass("refreshing");
      
      setTimeout("updatePlaylistsStatus();", 5000);
   });
}

function updatePlaylistsStatus()
{
   $('img.refreshing').each(function(i, el)
   {
      var rowId = el.id;
      
      if (el.id)
      {
         playlistId = parseInt(el.id.split('_')[1]);

         var json = {
                  playlistId: playlistId
               };
               
         $.post('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/api/playlist/read'; ?>', 
         {
            json: JSON.stringify(json)
         },
         function(data)
         {
            //alert(data);
            
            var j = jQuery.parseJSON(data);

            if (j.playlist.sync == 0)
            {
               $('#status_' + playlistId).attr("src", "<?php echo "$SITEROOT/images/checkmark.jpg"; ?>");
               $('#status_' + playlistId).removeClass("refreshing");
               $('#song_count_' + playlistId).text(j.playlist.songCount);
            }
            else if (j.playlist.sync == -2)
            {
               $('#status_' + playlistId).attr("src", "<?php echo "$SITEROOT/images/alert.jpg"; ?>");
               $('#status_' + playlistId).removeClass("refreshing");
            }
         });
      }
   });
   
   setTimeout("updatePlaylistsStatus();", 5000);
}

</script>

<?php include_once("$ROOT/admin/includes/body.php"); ?>

<div id="main">

<a href="/admin">Admin</a>

<h1>Playlists</h1>
<p>Playlists are the things you create in Rdio and will later map to profiles and activities to create soundtracks.</p>

<table id="playlists" class="admin">
<tr>
   <th></th>
   <th>Name</th>
   <th>Songs</th>
   <th>Songs To Delete</th>
   <th style="text-align:center">Status</th>
   <th>Rdio</th>
   <th>Sync</th>
   <!--
   <th>Chestnut</th>
   <th>Billboard</th>
   <th>Currated</th>
   <th width="30px"></th>
   -->
</tr>

<?php foreach ($playlists as $playlist) { ?>

<tr id="row_<?php echo $playlist->playlistId; ?>">

   <td width="30px" align="center">
      <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
      <a href="?playlistId=<?php echo $playlist->playlistId; ?>&delete=1"><img src="/admin/images/redx.jpg" width="24px" height="24px"></a>
      <?php } ?>
   </td>
   <td style="vertical-align:middle">
      <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
      <a href="./detail?playlistId=<?php echo $playlist->playlistId; ?>">
      <?php } ?>
         <?php echo $playlist->name; ?>
      <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
      </a>
      <?php } ?>
      <?php if ($playlist->comment) { echo "<br/>" . $playlist->comment; } ?>
   </td>
   <td style="text-align:center;vertical-align:middle">
      <span id="song_count_<?php echo $playlist->playlistId; ?>"><?php echo $playlist->songCount; ?></span>
   </td>
   <td style="text-align:center;vertical-align:middle">
      <?php if ($playlist->songsToDeleteCount > 0) { ?>
      <img src="<?php echo "$SITEROOT/images/alert.jpg"; ?>" title="This playlist has songs marked for deletion" />
      <?php } ?>
   </td>
   <td style="text-align:center;vertical-align:middle">
      <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
      <?php if ($playlist->sync == 0) { ?>
      <a style="cursor:pointer" onclick="refreshPlaylist(<?php echo $playlist->playlistId; ?>);">
      <img class="statusIcon" id="status_<?php echo $playlist->playlistId; ?>" src="<?php echo "$SITEROOT/images/checkmark.jpg"; ?>" title="Ready" />
      </a>
      <?php } elseif ($playlist->sync == 1) { ?>
      <a style="cursor:pointer" onclick="refreshPlaylist(<?php echo $playlist->playlistId; ?>);">
      <img class="statusIcon" id="status_<?php echo $playlist->playlistId; ?>" src="<?php echo "$SITEROOT/images/refresh.jpg"; ?>" title="Needs Refresh" />
      </a>
      <?php } elseif ($playlist->sync == -1) { ?>
      <img class="statusIcon refreshing" id="status_<?php echo $playlist->playlistId; ?>" src="<?php echo "$SITEROOT/images/ajax-loader.gif"; ?>" title="Refrehing" />
      <?php } elseif ($playlist->sync == -2) { ?>
      <a style="cursor:pointer" onclick="refreshPlaylist(<?php echo $playlist->playlistId; ?>);">
      <img class="statusIcon" id="status_<?php echo $playlist->playlistId; ?>" src="<?php echo "$SITEROOT/images/alert.jpg"; ?>" title="Error" />
      </a>
      <?php } ?>
      <?php } ?>
   </td>
   <td style="vertical-align:middle">
      <?php if ($playlist->rdioUrl) { ?>
      <a href="<?php echo $playlist->rdioUrl; ?>">Open</a>
      <?php } ?>
   </td>
   <td style="vertical-align:middle"><?php echo $playlist->sync; ?></td>
</tr>

<?php } ?>

</table>

<br/>
<hr/>
<br/>

<?php if ($_SESSION['ADMINUSER'] == "admin") { ?>

<form>
<table>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="playlistName">Name:</label></td>
   <td><input name="playlistName" size="50" /></td>
</tr>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="rdio">Rdio Playlist:</label></td>
   <td><input name="rdio" size="50" /><br/></td>
</tr>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="rdio">Synchronize:</label></td>
   <td><input name="sync" type="checkbox" checked="checked" /><br/></td>
</tr>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="comment">Comment:</label></td>
   <td><input name="comment" size="50" /><br/></td>
</tr>
<tr>
   <td></td>
   <td style="font-size:12px">(example: http://www.rdio.com/people/jalbano/playlists/564818/Soundtrack4/)</td>
</tr>
<tr>
   <td></td>
   <td><input type="submit" name="add" value="Add Playlist" /></td>
</tr>
</table>
</form>

<?php } ?>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>