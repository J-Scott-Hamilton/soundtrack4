<?php

$ROOT = '../..';

include_once("$ROOT/admin/includes/security.php");
include_once("$ROOT/api/includes/common.php");

function sortByDayslice($a, $b)
{
   return strcmp($a->daysliceName, $b->daysliceName);
}

if (isset($_GET['delete']))
{
   api('soundtrack', 'delete', array('soundtrackId' => $_GET['soundtrackId']));
}

if (isset($_GET['soundtrackProfile']))
{
   $profileId = $_GET['soundtrackProfile'];
   $activityId = $_GET['soundtrackActivity'];
   $daysliceId = $_GET['soundtrackDayslice'];

   if ($profileId && $activityId && $daysliceId)
   {
      $params = array();
      $params['profileId'] = $profileId;
      $params['activityId'] = $activityId;
      $params['daysliceId'] = $daysliceId;
   
      api('soundtrack', 'create', $params);
   }   
}

$profileId = $_GET['profileId'];

$ret = api('profile', 'read');
$profiles = $ret->profiles;

$ret = api('dayslice', 'read');
$dayslices = $ret->dayslices;

$ret = api('activity', 'read');
$activities = $ret->activities;

if (!isset($profileId))
{
   $profileId = $profiles[0]->profileId;
}
   
$ret = api('soundtrack', 'read', array('profileId' => $profileId));
$soundtracks = $ret->soundtracks;

usort($soundtracks, sortByDayslice);

include_once("$ROOT/admin/includes/header.php");
include_once("$ROOT/admin/includes/body.php");

?>

<div id="main">

<a href="/admin">Admin</a>

<h1>Soundtracks</h1>
<p>A Soundtrack is the combination of a profile, dayslice, activity and playlists.</p>

Showing soundtracks for: 
<select name="soundtrackProfile" onchange="document.location.href='?profileId=' + this.value">
   <?php foreach ($profiles as $profile) { ?>
   <option value="<?php echo $profile->profileId; ?>" 
      <?php if ($profile->profileId == $profileId) { echo " selected "; } ?>>
      <?php echo $profile->name; ?>
   </option>
   <?php } ?>
</select>

<br/>
<br/>

<table class="admin">
<tr>
   <th></th>
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
   <td width="30px" align="center">
      <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
      <a href="?soundtrackId=<?php echo $soundtrack->soundtrackId; ?>&delete=1"><img src="/admin/images/redx.jpg" width="24px" height="24px"></a>
      <?php } ?>
   </td>
   <td>
      <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
      <a href="/admin/profiles/detail?profileId=<?php echo $profile->profileId; ?>">
      <?php } ?>
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

<?php if ($_SESSION['ADMINUSER'] == "admin") { ?>

<form>
<table>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="soundtrackProfile">Profile:</label></td>
   <td width="100%">
      <select name="soundtrackProfile">
         <option value="">Select</option>
      <?php foreach ($profiles as $profile) { ?>
         <option value="<?php echo $profile->profileId; ?>"><?php echo $profile->name; ?></option>
      <?php } ?>
      </select>
   </td>
</tr>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="soundtrackDayslice">Dayslice:</label></td>
   <td>
      <select name="soundtrackDayslice">
         <option value="">Select</option>
      <?php foreach ($dayslices as $dayslice) { ?>
         <option value="<?php echo $dayslice->daysliceId; ?>"><?php echo $dayslice->name; ?></option>
      <?php } ?>
      </select>
   </td>
</tr>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="soundtrackActivity">Activity:</label></td>
   <td>
      <select name="soundtrackActivity">
         <option value="">Select</option>
      <?php foreach ($activities as $activity) { ?>
         <option value="<?php echo $activity->activityId; ?>"><?php echo $activity->name; ?></option>
      <?php } ?>
      </select>
   </td>
</tr>
<tr>
   <td></td>
   <td><input type="submit" name="add" value="Add Soundtrack" /></td>
</tr>
</table>
</form>

<?php } ?>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>