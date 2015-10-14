<?php

$ROOT = '../..';

include_once("$ROOT/api/includes/common.php");
include_once("$ROOT/includes/rdio/rdio.php");

$rdio = new Rdio(array('9xpz5b9neg4fyrgq37y2ft9s', 'XnbQME3KA7'));

$songs = array();

$soundtrackId = $_GET['soundtrackId'];
$profileId = $_GET['profileId'];
if (isset($_GET['soundtrackId']))
{

   $params = array('accountId' => 1, 'soundtrackId' => $soundtrackId);
   $ret = api('account-queue', 'refresh', $params);
print_r($params);   
   // Read the queue
   
   $ret = api('account-queue', 'read', $params);
   $songs = $ret->songs;
   
   $ret = api('soundtrack', 'read', array('soundtrackId' => $soundtrackId));
   $theSoundtrack = $ret->soundtrack;
}

if (isset($_GET['profileId']))
{
   $ret = api('soundtrack', 'read', array('profileId' => $profileId));
   $soundtracks = $ret->soundtracks;
}

$ret = api('profile', 'read');
$profiles = $ret->profiles;

include_once("$ROOT/admin/includes/header.php");

?>

<script src="https://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
<script src="/js/vendor/jquery.rdio.js"></script>

<script type="text/javascript">
 var duration = 1; // track the duration of the currently playing track
 var rdioQueue = Array();
 var rdioQueueIndex = 0;
 var startedPlaying = false;
 var trackPlaying = null;
 var transitioningToNextSong = false;

 <?php if ($songs) { foreach ($songs as $song) { ?>
 rdioQueue.push('<?php echo $song->rdio; ?>');
 <?php } } ?>
 
 $(document).ready(function() 
 {
   $('#api').bind('ready.rdio', function(e, userInfo) 
   {
      $(this).rdio().play(rdioQueue[rdioQueueIndex]);
   });

   $('#api').bind('positionChanged.rdio', function(e, position) 
   {
     $('#position').css('width', Math.floor(100*position/duration)+'%');
   });
   
   $('#api').bind('playingTrackChanged.rdio', function(e, playingTrack, sourcePosition) 
   {
      //alert("playingTrackChanged: " + playingTrack);
      
      trackPlaying = playingTrack;
      
      if (playingTrack) 
      {
         duration = playingTrack.duration;

         $('#art').attr('src', playingTrack.icon);
         $('#track').text(playingTrack.name);
         $('#album').text(playingTrack.album);
         $('#artist').text(playingTrack.artist);
      }
   });
   
   $('#api').bind('playStateChanged.rdio', function(e, playState) 
   {
      if (playState == 0) 
      {
         $('#play').show();
         $('#pause').hide();
      } 
      else if ((playState == 2) && 
               (startedPlaying) && 
               (trackPlaying == null) && 
               (!transitioningToNextSong))
      {
         // Move to next track
         
         transitioningToNextSong = true;
      
         $('#api').rdio().play(rdioQueue[++rdioQueueIndex]);
      }
      else
      {
         if (playState == 1)
         {
            startedPlaying = true;
         }
                     
         transitioningToNextSong = false;
            
         $('#play').hide();
         $('#pause').show();
      }
   });
      
   var playback_token = "GA9QorMR_____zl4cHo1YjluZWc0ZnlyZ3EzN3kyZnQ5c3NvdW5kdHJhY2s0LmNvbdnYdGntuW9sPCUwynTB0b4=";
   var domain = "soundtrack4.com";
   
   // this is a valid playback token for localhost.
   // but you should go get your own for your own domain.
   $('#api').rdio(playback_token);

   $('#previous').click(function() 
   {
      $('#api').rdio().play(rdioQueue[--rdioQueueIndex]);
   });
   
   $('#play').click(function() { $('#api').rdio().play(); });
   $('#pause').click(function() { $('#api').rdio().pause(); });
   
   $('#next').click(function() 
   {
      $('#api').rdio().play(rdioQueue[++rdioQueueIndex]);
   });
 });
 
 function excludeSong()
 {
   var json = {
            soundtrackId: <?php echo $soundtrackId; ?>,
            songRdio: rdioQueue[rdioQueueIndex]
         };
         
   $.post('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/api/soundtrack/exclude-song'; ?>', 
   {
      json: JSON.stringify(json)
   },
   function(data)
   {
      alert('Song excluded');
   });
 }
 
</script>

<?php include_once("$ROOT/admin/includes/body.php"); ?>

<div id="main">

<a href="/admin">Admin</a>

<h1>Songs by Soundtrack</h1>

<form>
<table>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="soundtrackProfile">Profile:</label></td>
   <td>
         <select name="soundtrackProfile" onchange="document.location.href='?profileId=' + this.value">
         <option value="">Select</option>
         <?php foreach ($profiles as $profile) { ?>
         <option value="<?php echo $profile->profileId; ?>" 
            <?php if ($profile->profileId == $profileId) { echo " selected "; } ?>>
            <?php echo $profile->name; ?>
         </option>
         <?php } ?>
      </select>
   </td>
</tr>
<?php if ($profileId) { ?>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="soundtrack">Soundtrack:</label></td>
   <td>
      <select name="soundtrack" onchange="document.location.href='?profileId=<?php echo $profileId; ?>&soundtrackId=' + this.value">
         <option value="">Select</option>
         <?php foreach ($soundtracks as $soundtrack) { ?>
         <option value="<?php echo $soundtrack->soundtrackId; ?>" 
            <?php if ($soundtrack->soundtrackId == $soundtrackId) { echo " selected "; } ?>>
            <?php echo $soundtrack->daysliceName . " : " . $soundtrack->activityName; ?>
         </option>
         <?php } ?>
      </select>
   </td>
</tr>
<?php } ?>
</table>
</form>

<br/>
<br/>
<hr/>
<br/>

<?php if ($songs) { ?>

<h2>Songs</h2>

<div id="api"></div>
<table>
   <tr>
      <td>
         <img id="art" src="" height="200" width="200" style="padding-right: 20px">
      </td>
   </tr>
   <tr>
      <td style="text-align:left;width:100%">
         <div>
          <div><b>Track: </b><span id="track"></span></div>
          <div><b>Album: </b><span id="album"></span></div>
          <div><b>Artist: </b><span id="artist"></span></div>
          <br/>
          <div>
            <b>Position: </b>
            <span style="display:inline-block;width:200px;border:1px solid black;">
            <span id="position" style="display:inline-block;background-color:#666">&nbsp;</span>
            </span></div>
            <br/>
            <div>
            <button id="previous">&lt;&lt;</button>
            <button id="play">|&gt;</button>
            <button id="pause">||</button>
            <button id="next">&gt;&gt;</button>
            </div>
            <br/>
         </div>
         <a href="javascript:excludeSong()">Exclude this Song from this Soundtrack</a>
         <br/><br/>
      </td>
   </tr>
</table>

<table class="admin">
<tr>
   <th></th>
   <th>Name</th>
   <th>Artist</th>
   <th>Album</th>
   <th>Spotify</th>
   <th>Rdio</th>
</tr>

<?php foreach ($songs as $song) { ?>

<tr>
   <td style="vertical-align:middle"><a href="javascript:$('#api').rdio().play('<?php echo $song->rdio; ?>');">Play</a></td>
   <td style="vertical-align:middle"><a href="./detail?id=<?php echo $song->songId; ?>"><?php echo $song->name; ?></a></td>
   <td style="vertical-align:middle"><?php echo $song->artist; ?></td>
   <td style="vertical-align:middle"><?php echo $song->album; ?></td>
   <td style="vertical-align:middle"><?php if ($song->spotify) { echo '<a target="_blank" href="http://open.spotify.com/track/' . $song->spotify . '">' . $song->spotify . '</a>'; } ?></td>
   <td style="vertical-align:middle"><?php if ($song->rdio) { echo '<a target="_blank" href="http://rd.io/x/' . $song->rdio . '">' . $song->rdio . '</a>'; } ?></td>
</tr>

<?php } ?>
<?php } ?>

</table>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>
