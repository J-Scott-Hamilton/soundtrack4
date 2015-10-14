<?php

$ROOT = '../..';

include_once("$ROOT/admin/includes/security.php");
include_once("$ROOT/api/includes/common.php");

if (isset($_GET['delete']))
{
   api('activity', 'delete', array('activityId' => $_GET['activityId']));
}

if (isset($_GET['activity']))
{
   if (strlen($_GET['activity']) > 0)
   {
      $params = array();
      $params['name'] = $_GET['activity'];

      api('activity', 'create', $params);
   }
}

$ret = api('activity', 'read');
$activities = $ret->activities;

include_once("$ROOT/admin/includes/header.php");
include_once("$ROOT/admin/includes/body.php");

?>

<div id="main">

<a href="/admin">Admin</a>

<h1>Activities</h1>

<table class="admin">
<tr>
   <th></th>
   <th>Activity</th>
</tr>

<?php if ($activities) { ?>
<?php foreach ($activities as $activity) { ?>

<tr>
   <td width="30px" align="center">
      <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
      <a href="?activityId=<?php echo $activity->activityId; ?>&delete=1"><img src="/admin/images/redx.jpg" width="24px" height="24px"></a>
      <?php } ?>
   </td>
   <td style="vertical-align:middle">
      <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
      <a href="./detail?id=<?php echo $activity->activityId; ?>">
      <?php } ?>
         <?php echo $activity->name; ?>
      <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
      </a>
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

<label for="activity">New Activity:</label><br/>
<input name="activity" size="50" /><br/>
<br/>
<input type="submit" name="add" value="Add Activity" />

</form>

<?php } ?>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>