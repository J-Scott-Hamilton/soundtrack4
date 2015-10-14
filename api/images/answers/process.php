<?php

$root = "./originals";

if ($handle = opendir($root))
{
   while (false !== ($entry = readdir($handle)))
   {
      if ($entry == "." || $entry == ".." || $entry == ".svn")
         continue;

      echo "processing $entry\n";
      
      $size = getimagesize("$root/$entry");
      
      // Reject anything less than 200x200
      
      if (($size[0] < 200) || ($size[1] < 200))
      {
         echo "\t$entry is too small\n";
         continue;
      }

      $parts = explode(".", $entry);
      $name = $parts[0];
      $ext = $parts[1];
      
      if (($ext == "jpg") || ($ext == "jpeg"))
      {
         $source = imagecreatefromjpeg("$root/$entry");
      }
      else if ($ext == "png")
      {
         $source = imagecreatefrompng("$root/$entry");
      }
      else if ($ext == "gif")
      {
         $source = imagecreatefromgif("$root/$entry");
      }

      // First, make it square

      $squareSize = max($size[0], $size[1]);
      $x = $y = 0;
            
      if ($size[0] > $size[1])
      {
          // Pad top and bottom
          
         $y = ((($size[0] - $size[1]) / 2)); // * (200 / $size[0]));
      }      
      else if ($size[0] < $size[1])
      {
          // Pad left and right

         $x = ((($size[1] - $size[0]) / 2)); // * (200 / $size[1]));
      }      

      $square = imagecreatetruecolor($squareSize, $squareSize);

      imagecopy($square, $source, $x, $y, 0, 0, $size[0], $size[1]);
      
      // Now, resize
      
      resize($name, $square, $squareSize, 75);
      resize($name, $square, $squareSize, 200);

      imagedestroy($source);
      imagedestroy($square);
      
      echo "\tprocessed\n";
      
      //break;
   }

   closedir($handle);
}

function resize($name, $square, $squareSize, $resizeTo)
{
   if (file_exists("./$resizeTo/$name.png"))
      return;
      
   $resized = imagecreatetruecolor($resizeTo, $resizeTo);

   imagecopyresampled($resized, $square, 0, 0, 0, 0, $resizeTo, $resizeTo, $squareSize, $squareSize);
   
   // Fill padding with white
         
   //imagefill($resized, 0, 0, imagecolorallocate($resized, 255, 255, 255));
   //imagefill($resized, ($resizeTo - 1), ($resizeTo - 1), imagecolorallocate($resized, 255, 255, 255));

   imagepng($resized, "./$resizeTo/$name.png");
   imagedestroy($resized);
}

?>
