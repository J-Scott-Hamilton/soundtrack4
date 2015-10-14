<?php

set_time_limit(0);

require_once("../includes/db.php");
require_once("../../includes/rdio.php");
require_once("../../includes/rdio/rdio.php");

$db = db_connect();   

$rdio = new Rdio(array($rdio_api_key, $rdio_api_secret));

$sql = "SELECT rdio FROM song ORDER BY RAND() LIMIT 8000";
$rows = mysql_query($sql);

while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
{
   $track = $row['rdio'];
   
   $params = array();
   $params['keys'] = $track;

   $search = $rdio->call('get', $params);
   
   $info = $search->result->$track;
   
   $icon = $info->icon;
   
   $filename = str_replace("http://cdn3.rd.io/", "", $icon);
   $filename = str_replace("/square-200", "", $filename);
   $filename = str_replace("/", "-", $filename);
   $filename = "./art/$filename";
   
   if (file_exists($filename))
      continue;
      
   $i = (count(glob("./art/*")) + 1);
      
   echo "\ticon $i = " . $info->icon . " => $filename\n";
   
   file_put_contents($filename, file_get_contents($info->icon));

   $albums[] = $info->icon;
         
   if ($i > 4000)
      break;
}

db_close($db);   

/*
for ($b = 1; $b <= 1; $b++)
{
   echo "Building bkgnd $b\n";
   
   //echo "albums = " . count($albums) . "\n";
   
   $tileSize = 100;
   $tilesPerSide = 20;
   $imageSize = ($tileSize * $tilesPerSide);
   
   $image = imagecreatetruecolor($imageSize, $imageSize);
   
   $x = 0;
   $y = 0;
   
   $dirs = scandir("./art");
   
   foreach($dirs as $dir)  
   {
      if ($dir === '.' || $dir === '..')
         continue;
   
      $rawImg = imagecreatefromjpeg("./art/$dir");
      $tileImg = imagecreatetruecolor($tileSize, $tileSize);
      
      imagecopyresampled($tileImg, $rawImg, 0, 0, 0, 0, $tileSize, $tileSize, 200, 200);
   
      imagecopy($image, $tileImg, $x, $y, 0, 0, $tileSize, $tileSize);
      imagedestroy($tileImg);
      
      $x += $tileSize;
      
      if ($x >= ($imageSize))
      {
         $x = 0;
         $y += $tileSize;
      }

      unlink("./art/$dir");
   }
   
   imagejpeg($image, "./bkgnds/bkgnd$b.jpg");
   imagedestroy($image);
}
*/

?>
