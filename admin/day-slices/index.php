<?php

$ROOT = '../..';

include_once("$ROOT/api/includes/common.php");

$ret = api('dayslice', 'read');
$slices = $ret->dayslices;

include_once("$ROOT/admin/includes/header.php");
include_once("$ROOT/admin/includes/body.php");

?>

<div id="main">

<a href="/admin">Admin</a>

<h1>Day-Slices</h1>

<table class="admin">
<tr>
   <th>Name</th>
</tr>

<?php foreach ($slices as $slice) { ?>

<tr>
   <td><?php echo $slice->name; ?></td>
</tr>

<?php } ?>

</table>

<br/>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>