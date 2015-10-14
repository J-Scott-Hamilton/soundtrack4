<?php

$ROOT = '../..';

include_once("$ROOT/api/includes/common.php");

$daysliceId = $_GET['daysliceId'];

$ret = api('dayslice', 'read');
$dayslices = $ret->dayslices;

if (!isset($daysliceId))
{
   $daysliceId = $dayslices[0]->daysliceId;
}
   
$ret = api('soundtrack', 'read', array('daysliceId' => $daysliceId));
$soundtracks = $ret->soundtracks;

include_once("$ROOT/admin/includes/header.php");
include_once("$ROOT/admin/includes/body.php");

?>

<div id="main">

<a href="/admin">Admin</a>

<h1>Soundtracks</h1>
<p>A Soundtrack is the combination of a profile, dayslice, activity and playlists.</p>

Showing soundtracks for: 
<select name="soundtrackDayslice" onchange="document.location.href='?daysliceId=' + this.value">
   <?php foreach ($dayslices as $dayslice) { ?>
   <option value="<?php echo $dayslice->daysliceId; ?>" 
      <?php if ($dayslice->daysliceId == $daysliceId) { echo " selected "; } ?>>
      <?php echo $dayslice->name; ?>
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

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>