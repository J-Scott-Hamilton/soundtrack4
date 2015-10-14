<?php

$ROOT = '../..';
$tagId = $_GET['id'];

include_once("$ROOT/api/includes/common.php");

if (isset($_GET['delete']))
{
   if (!isset($_GET['tagChoiceId']))
   {
      api('tag', 'delete', array('tagId' => $tagId));
      header("Location: http://" . $_SERVER['HTTP_HOST'] . '/admin/tags');
      exit();
   }
   else
   {
      api('tag-choice', 'delete', array('tagChoiceId' => $_GET['tagChoiceId']));
   }
}

if (isset($_GET['choice']))
{
   $params = array();
   $params['tagId'] = $tagId;
   $params['name'] = $_GET['choice'];
   
   $ret = api('tag-choice', 'create', $params);
}

$ret = api('tag', 'read', array('tagId' => $tagId));
$tag = $ret->tag;
$choices = $tag->choices;

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
<a href="/admin/tags">Tags</a>

<h1><?php echo $tag->name; ?></h1>

<h2>Choices</h2>

<table class="admin">
<tr>
   <th width="30px"></th>
   <th>Choice</th>
</tr>

<?php 
if ($choices)
{
   foreach ($choices as $choice)
   {
?>

<tr>
   <td width="30px" align="center"><a href="?id=<?php echo $tagId; ?>&tagChoiceId=<?php echo $choice->tagChoiceId; ?>&delete=1"><img src="/admin/images/redx.jpg" width="24px" height="24px"></a></td>
   <td><?php echo $choice->name; ?></td>
</tr>

<?php 
   }
}
?>

</table>

<br/>
<hr/>
<br/>

<form>
<label for="choice">Choice:</label>
<input id="choice" name="choice" size="50" /><br/>
<input type="hidden" name="id" value="<?php echo $tagId; ?>" /><br/>
<br/>
<input type="submit" name="add" value="Add Choice" />
</form>

<br/>
<hr/>
<br/>

<a href="?id=<?php echo $tagId; ?>&delete=1">Delete this Tag</a>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>