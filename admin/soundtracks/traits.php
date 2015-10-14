<?php

$ROOT = '../..';

include_once("$ROOT/api/includes/common.php");

$songs = array();

$soundtrackId = $_GET['soundtrackId'];
$profileId = $_GET['profileId'];

if (isset($_GET['soundtrackId']))
{
   $ret = api('soundtrack', 'traits', array('soundtrackId' => $soundtrackId));
   $songs = $ret->songs;
}

if (isset($_GET['profileId']))
{
   $ret = api('soundtrack', 'read', array('profileId' => $profileId));
   $soundtracks = $ret->soundtracks;
}

$ret = api('profile', 'read');
$profiles = $ret->profiles;

include_once("$ROOT/admin/includes/header.php");
include_once("$ROOT/admin/includes/body.php");

?>

<div id="main">

<a href="/admin">Admin</a>

<h1>Soundtrack Traits</h1>

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

<h2>Traits</h2>

<?php foreach ($songs as $song) { ?>

<!-- TODO -->

<?php } ?>
<?php } ?>

</table>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>