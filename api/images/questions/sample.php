<html>
<body>

<table>
<?php for ($s = 600; $s >= 100; $s -= 100) { ?>
   <tr><td><img src="./originals/carhartt.png" width="<?php echo $s; ?>px" height="<?php echo $s; ?>px" /></td></tr>
   <tr><td><?php echo "$s x $s"; ?></td></tr>
   <tr><td><hr/></td></tr>
<?php } ?>   
</table>

</body>
</html>