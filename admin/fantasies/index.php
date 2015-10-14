<?php

$ROOT = '../..';

include_once("$ROOT/admin/includes/security.php");
include_once("$ROOT/api/includes/common.php");

if (isset($_GET['delete']))
{
   api('fantasy', 'delete', array('fantasyId' => $_GET['fantasyId']));
}

if (isset($_GET['fantasy']))
{
   if (strlen($_GET['fantasy']) > 0)
   {
      $params = array();
      $params['name'] = $_GET['fantasy'];

      api('fantasy', 'create', $params);
   }
}

$ret = api('fantasy', 'read');
$fantasies = $ret->fantasies;

include_once("$ROOT/admin/includes/header.php");
include_once("$ROOT/admin/includes/body.php");

?>

<div id="main">

<a href="/admin">Admin</a>

<h1>Fantasies</h1>

<table class="admin">
<tr>
   <th></th>
   <th>Fantasy</th>
</tr>

<?php if ($fantasies) { ?>
<?php foreach ($fantasies as $fantasy) { ?>

<tr>
   <td width="30px" align="center">
      <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
      <a href="?fantasyId=<?php echo $fantasy->fantasyId; ?>&delete=1"><img src="/admin/images/redx.jpg" width="24px" height="24px"></a>
      <?php } ?>
   </td>
   <td style="vertical-align:middle">
      <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
      <a href="./detail?id=<?php echo $fantasy->fantasyId; ?>">
      <?php } ?>
         <?php echo $fantasy->name; ?>
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

<label for="fantasy">New Fantasy:</label><br/>
<input name="fantasy" size="50" /><br/>
<br/>
<input type="submit" name="add" value="Add Fantasy" />

</form>

<?php } ?>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>