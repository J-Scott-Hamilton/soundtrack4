<?php

$ROOT = '../..';

include_once("$ROOT/api/includes/common.php");

$ret = api('soundtrack', 'read');
$soundtracks = $ret->soundtracks;

include_once("$ROOT/admin/includes/header.php");
include_once("$ROOT/admin/includes/body.php");

?>

<div id="main">

<a href="/admin">Admin</a>

<h1>Soundtracks w/o Start Songs</h1>

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
      if ($soundtrack->hasStartSongs)
         continue;
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

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>