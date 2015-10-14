<?php

$ROOT = '../..';

include_once("$ROOT/api/includes/common.php");

$songs = array();

if (isset($_GET['queue']))
{
   $parts = explode(':', $_GET['queue']);
   
   $accountId = $parts[0];
   $soundtrackId = $parts[1];
}

$ret = api('account-queue', 'read');
$queues = $ret->queues;

if ($accountId)
{
   $ret = api('account-queue', 'read', array('accountId' => $accountId, 'soundtrackId' => $soundtrackId));
   $queueSongs = $ret->songs;

   $ret = api('account-song', 'read', array('accountId' => $accountId));
   $heardSongs = $ret->songs;
}

include_once("$ROOT/admin/includes/header.php");

?>

<?php include_once("$ROOT/admin/includes/body.php"); ?>

<div id="main">

<a href="/admin">Admin</a>

<h1>Songs by User</h1>

<br/>
<br/>
<hr/>
<br/>

<form>
<select name="queue" onchange="document.location.href='?queue=' + this.value">
   <option>Select</option>
   <?php 
   foreach ($queues as $queue)
   {
      $value = $queue->accountId . ':' . $queue->soundtrackId;
      $name = $queue->firstName . ' : ' . $queue->profileName . ' : ' . $queue->daysliceName . ' : ' . $queue->activityName;
      
      echo "<option value=\"$value\"";
      
      if (($queue->accountId == $accountId) && ($queue->soundtrackId == $soundtrackId)) 
      {
         echo " selected ";
      }
      
      echo ">$name</option>";
   }
   ?>
</select>
</form>

<?php if ($queueSongs) { ?>

<h2>Songs in their queue</h2>

<table class="admin">
<tr>
   <th>Name</th>
   <th>Artist</th>
   <th>Album</th>
</tr>

<?php foreach ($queueSongs as $song) { ?>

<tr>
   <td style="vertical-align:middle"><?php echo $song->name; ?></td>
   <td style="vertical-align:middle"><?php echo $song->artist; ?></td>
   <td style="vertical-align:middle"><?php echo $song->album; ?></td>
</tr>

<?php } ?>
<?php } ?>

</table>

<?php if ($heardSongs) { ?>

<h2>Songs they've heard</h2>

<table class="admin">
<tr>
   <th>Name</th>
   <th>Artist</th>
   <th>Album</th>
   <th>Timestamp</th>
</tr>

<?php foreach ($heardSongs as $song) { ?>

<tr>
   <td style="vertical-align:middle"><?php echo $song->name; ?></td>
   <td style="vertical-align:middle"><?php echo $song->artist; ?></td>
   <td style="vertical-align:middle"><?php echo $song->album; ?></td>
   <td style="vertical-align:middle"><?php echo $song->timestamp; ?></td>
</tr>

<?php } ?>
<?php } ?>

</table>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>