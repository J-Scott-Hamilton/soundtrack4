<?php

$ROOT = '../..';

include_once("$ROOT/admin/includes/security.php");
include_once("$ROOT/api/includes/common.php");

if (isset($_GET['delete']))
{
   api('tag', 'delete', array('tagId' => $_GET['tagId']));
}

if (isset($_GET['tag']))
{
   if (strlen($_GET['tag']) > 0)
   {
      $params = array();
      $params['name'] = $_GET['tag'];

      api('tag', 'create', $params);
   }
}

$ret = api('tag', 'read');
$tags = $ret->tags;

include_once("$ROOT/admin/includes/header.php");
include_once("$ROOT/admin/includes/body.php");

?>

<div id="main">

<a href="/admin">Admin</a>

<h1>Tags</h1>

<table class="admin">
<tr>
   <th width="30px"></th>
   <th>Tag</th>
</tr>

<?php if ($tags) { ?>
<?php foreach ($tags as $tag) { ?>

<tr>
   <td width="30px" align="center">
   <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
      <a href="?tagId=<?php echo $tag->tagId; ?>&delete=1"><img src="/admin/images/redx.jpg" width="24px" height="24px"></a>
   <?php } ?>
   </td>
   <td style="vertical-align:middle">
      <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
      <a href="./detail?id=<?php echo $tag->tagId; ?>">
      <?php } ?>
         <?php echo $tag->name; ?>
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

<label for="tag">New Tag:</label><br/>
<input name="tag" size="50" /><br/>
<br/>
<input type="submit" name="add" value="Add Tag" />

</form>

<?php } ?>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>