<?php

$ROOT = '../..';

include_once("$ROOT/admin/includes/security.php");
include_once("$ROOT/api/includes/common.php");

if (isset($_GET['profileName']))
{
   if (strlen($_GET['profileName']) > 0)
   {
      api('profile', 'create', array('name' => $_GET['profileName']));
   }
}

$ret = api('profile', 'read');
$profiles = $ret->profiles;

include_once("$ROOT/admin/includes/header.php");
include_once("$ROOT/admin/includes/body.php");

?>

<div id="main">

<a href="/admin">Admin</a>

<h1>Profiles</h1>

<table class="admin">
<tr>
   <th>Name</th>
</tr>

<?php foreach ($profiles as $profile) { ?>

<tr>
   <td>
      <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
      <a href="./detail?profileId=<?php echo $profile->profileId; ?>">
      <?php } ?>
      
      <?php echo $profile->name; ?>
      
      <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
      </a>
      <?php } ?>      
      <p><?php echo $profile->description; ?></p>
   </td>
</tr>

<?php } ?>

</table>

<br/>
<hr/>
<br/>

<?php if ($_SESSION['ADMINUSER'] == "admin") { ?>

<form>
<label for="profileName">Name:</label><br/>
<input name="profileName" size="50" /><br/>
<br/>
<input type="submit" name="add" value="Add Profile" /><br/>
</form>

<?php } ?>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>