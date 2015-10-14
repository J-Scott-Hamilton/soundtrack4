<?php

$ROOT = '../..';

include_once("$ROOT/admin/includes/security.php");
include_once("$ROOT/api/includes/common.php");

$activityId = $_GET['activityId'];

$ret = api('activity', 'read');
$activities = $ret->activities;

if (!isset($activityId))
{
   $activityId = $activities[0]->activityId;
}
   
$ret = api('soundtrack', 'read', array('activityId' => $activityId));
$soundtracks = $ret->soundtracks;

//usort($soundtracks, sortByActivity);

include_once("$ROOT/admin/includes/header.php");
include_once("$ROOT/admin/includes/body.php");

?>

<div id="main">

<a href="/admin">Admin</a>

<h1>Soundtracks</h1>
<p>A Soundtrack is the combination of a profile, dayslice, activity and playlists.</p>

Showing soundtracks for: 
<select name="soundtrackActivity" onchange="document.location.href='?activityId=' + this.value">
   <?php foreach ($activities as $activity) { ?>
   <option value="<?php echo $activity->activityId; ?>" 
      <?php if ($activity->activityId == activityId) { echo " selected "; } ?>>
      <?php echo $activity->name; ?>
   </option>
   <?php } ?>
</select>

<br/>
<br/>

<table class="admin">
<tr>
   <th>Profile</th>
   <th>Dayslice</th>
   <th>Activity</th>
   <th></th>
</tr>

<?php 
if (count($soundtracks) > 0)
{
   foreach ($soundtracks as $soundtrack)
   {
?>

<tr>
   <td>
      <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
      <a href="/admin/profiles/detail?profileId=<?php echo $profile->profileId; ?>">
      <?php }?>
         <?php echo $soundtrack->profileName; ?>
      <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
      </a>
      <?php } ?>
   </td>
   <td><?php echo $soundtrack->daysliceName; ?></td>
   <td><?php echo $soundtrack->activityName; ?></td>
   <td style="vertical-align:middle">
      <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
      <a href="./detail?id=<?php echo $soundtrack->soundtrackId; ?>">Edit</a>
      <?php } ?>
   </td>
</tr>

<?php } ?>
<?php } ?>

</table>

<br/>
<hr/>
<br/>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>