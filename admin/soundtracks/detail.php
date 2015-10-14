<?php

$ROOT = '../..';
$soundtrackId = $_GET['id'];

include_once("$ROOT/api/includes/common.php");

if (isset($_GET['copy']))
{
   $copySoundtrackId = $_GET['copySoundtrackId'];
   
   if ($copySoundtrackId)
   {
      $ret = api('soundtrack', 'copy', array('soundtrackId' => $soundtrackId, 'copySoundtrackId' => $copySoundtrackId));
   }
}

$ret = api('soundtrack', 'read', array('soundtrackId' => $soundtrackId));
$soundtrack = $ret->soundtrack;

$ret = api('playlist', 'read');
$playlists = $ret->playlists;

$playlistWeights = array();
$playlistStartSongs = array();
$weightTotal = 0;

foreach ($soundtrack->playlists as $playlist)
{
   $playlistWeights[$playlist->playlistId] = $playlist->weight;
   $playlistStartSongs[$playlist->playlistId] = $playlist->startSongs;
   $weightTotal += $playlist->weight;
}

include("$ROOT/admin/includes/header.php");

?>

<script>

function onWeightBlur(obj, playlistId)
{
   var weight = parseInt(obj.value);
   
   if (!weight)
      weight = 0;
      
   var json = {
            soundtrackId: <?php echo $soundtrackId; ?>,
            playlistId: playlistId,
            weight: weight
         };
         
   $.post('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/api/soundtrack-playlist/update'; ?>', 
   {
      json: JSON.stringify(json)
   });

   if (weight > 0) 
   {
      $('#start_songs_' + playlistId).removeAttr("disabled");
   }
   else 
   {
      $('#start_songs_' + playlistId).removeAttr("checked");
      $('#start_songs_' + playlistId).attr("disabled", true);
   }
   
   var sum = 0;

   $(".weight").each(function()
   {
      if (!isNaN(this.value) && this.value.length != 0)
      {
         sum += parseInt(this.value);
      }
   });
   
   $("#weight_total").val(sum);
}

function toggleStartSongs(playlistId)
{
   var isChecked = $('#start_songs_' + playlistId).is(':checked');

   var json = {
            soundtrackId: <?php echo $soundtrackId; ?>,
            playlistId: playlistId,
            startSongs: isChecked
         };
         
   $.post('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/api/soundtrack-playlist/update'; ?>', 
   {
      json: JSON.stringify(json)
   });
}

function toggleShuffle()
{
   var isChecked = $('#shuffle_soundtrack').is(':checked');

   var json = {
            soundtrackId: <?php echo $soundtrackId; ?>,
            shuffle: isChecked
         };
         
   $.post('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/api/soundtrack/update'; ?>', 
   {
      json: JSON.stringify(json)
   });
}

</script>

<?php include("$ROOT/admin/includes/body.php"); ?>

<div id="main">

<a href="/admin">Admin</a> : 
<a href="/admin/soundtracks">Soundtracks</a>

<h2><?php echo $soundtrack->profileName . " : " . $soundtrack->daysliceName . " : " . $soundtrack->activityName; ?></h2>

<input id="shuffle_soundtrack" type="checkbox" 
   <?php echo ($soundtrack->shuffle) ? " checked" : ""; ?> 
      onchange="toggleShuffle();" />&nbsp;Shuffle Songs

<?php 

if ($weightTotal == 0) 
{
   $ret = api('soundtrack', 'read');
   $soundtracks = $ret->soundtracks;

?>

<form>
This soundtrack is like:
<select name="copySoundtrackId">
   <option>--------------------</option>
   <?php 
   foreach ($soundtracks as $st) 
   {
      if ($st->percentFull == 0)
         continue;
   ?>
   <option value="<?php echo $st->soundtrackId; ?>"><?php echo $st->profileName . " : " . $st->daysliceName . " : " . $st->activityName; ?></option>
   <?php } ?>
</select>
<input type="submit" name="copy" value="Copy" />
<input type="hidden" name="id" value="<?php echo $soundtrackId; ?>" />
</form>

<?php } ?>

<br/>

<h2>Playlists</h2>

<table class="admin">
<tr>
   <th>Playlist</th>
   <th>Weight (%)</th>
   <th>Start Songs</th>
</tr>

<?php foreach ($playlists as $playlist) { ?>
<tr>
   <td style="vertical-align:middle;width:100%;">
      <a href="<?php echo $playlist->rdioUrl; ?>" target="_blank">
         <?php echo $playlist->name; ?>
      </a>
   </td>
   <td>
      <input style="border: 1px solid #000000;" class="weight" id="weight_<?php echo $playlist->playlistId; ?>" type="text" size="10" 
         value="<?php echo $playlistWeights[$playlist->playlistId]; ?>" 
         onblur="onWeightBlur(this, <?php echo $playlist->playlistId; ?>);" />
   </td>
   <td style="text-align:center;vertical-align:middle;">
      <input id="start_songs_<?php echo $playlist->playlistId; ?>" type="checkbox" 
         <?php echo (!isset($playlistWeights[$playlist->playlistId])) ? 'disabled="disabled"' : ''; ?> 
         <?php echo (isset($playlistStartSongs[$playlist->playlistId]) && $playlistStartSongs[$playlist->playlistId]) ? "checked" : ""; ?> 
         onchange="toggleStartSongs(<?php echo $playlist->playlistId; ?>);" />
   </td>
</tr>
<?php } ?>

<tr>
   <td></td>
   <td>
      <input id="weight_total" type="text" disabled="disabled" size="10" value="<?php echo $weightTotal; ?>" />
   </td>
   <td></td>
</tr>

</table>

<h3>IMPORTANT: You need to tab away from the weight field for the new value to take.</h3>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>