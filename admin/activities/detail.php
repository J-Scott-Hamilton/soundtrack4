<?php

$ROOT = '../..';
$activityId = $_GET['id'];

include_once("$ROOT/api/includes/common.php");

if (isset($_GET['update']))
{
   if (strlen($_GET['activity']) > 0)
   {
      $params = array();
      $params['name'] = $_GET['activity'];
      $params['activityId'] = $activityId;

      api('activity', 'update', $params);
   }
}
else if (isset($_GET['delete']))
{
   api('activity', 'delete', array('activityId' => $activityId));
   header("Location: http://" . $_SERVER['HTTP_HOST'] . '/admin/activities');
   exit();
}

$ret = api('activity', 'read', array('activityId' => $activityId));
$activity = $ret->activity;

include("$ROOT/admin/includes/header.php");

?>

<style>

table.admin td
{
   vertical-align: middle;
}

</style>

<script>
</script>

<?php include("$ROOT/admin/includes/body.php"); ?>

<div id="main">

<a href="/admin">Admin</a> : 
<a href="/admin/activities">Activities</a>

<h1><?php echo $activity->name; ?></h1>

<br/>
<hr/>
<br/>

<form>
<label for="activity">Update Activity:</label><br/>
<input name="activity" size="50" value="<?php echo $activity->name; ?>" /><br/>
<input type="hidden" name="id" value="<?php echo $activityId; ?>" />
<br/>
<input type="submit" name="update" value="Update Activity" />
</form>

<br/>
<hr/>
<br/>

<a href="?id=<?php echo $activityId; ?>&delete=1">Delete this Activity</a>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>