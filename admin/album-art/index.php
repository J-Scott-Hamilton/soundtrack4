<?php

$ROOT = '../..';

include_once("$ROOT/api/includes/common.php");
include_once("$ROOT/admin/includes/header.php");
include_once("$ROOT/admin/includes/body.php");

?>

<div id="main">

<a href="/admin">Admin</a>

<h1>Backgrounds</h1>

<table class="admin">
<tr>
   <th>Clipped</th>
   <th>Sized</th>
</tr>

<?php 

for ($b = 1; $b <= 10; $b++) 
{
   echo '<tr>';
   echo '<td><a href="./art.php?a=clipped&b=' . $b . '">' . $b . '</td>';
   echo '<td><a href="./art.php?a=sized&b=' . $b . '">' . $b . '</td>';
   echo '</tr>';
}

?>

</table>

<br/>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>