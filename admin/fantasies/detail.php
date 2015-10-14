<?php

$ROOT = '../..';
$fantasyId = $_GET['id'];

include_once("$ROOT/api/includes/common.php");

if (isset($_GET['delete']))
{
   api('fantasy', 'delete', array('fantasyId' => $fantasyId));
   header("Location: http://" . $_SERVER['HTTP_HOST'] . '/admin/fantasies');
   exit();
}

$ret = api('fantasy', 'read', array('fantasyId' => $fantasyId));
$fantasy = $ret->fantasy;

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
<a href="/admin/fantasies">Fantasies</a>

<h1><?php echo $fantasy->name; ?></h1>

<br/>
<hr/>
<br/>

<a href="?id=<?php echo $fantasyId; ?>&delete=1">Delete this fantasy</a>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>