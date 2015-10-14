<?php 
$a = $_GET['a']; 
$b = $_GET['b']; 
$o = number_format($b / 10.0, 1);
?>
<html>
<style>
#art
{
   position: absolute;
   z-index: -1;
   top: 0;
   bottom: 0;
   left: 0;
   right: 0;
   opacity: <?php echo $o; ?>;
   width: 100%;
   height: 100%;
   background-image:url('./<?php echo $a; ?>/bkgnd<?php echo $b; ?>.jpg');
} 
</style>
<body>
<div id="art">
</div>
</body>
</html>