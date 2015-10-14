<?php

$json = array('result' => false);

$MINSIZE = 100;

try 
{
   $uploaddir = '/images/answers/originals/';

   // Extract answerId from files
   
   $keys = array_keys($_FILES);
   $inputName = $keys[0];
   $fromPath = $_FILES[$inputName]['tmp_name'];

   $parts = explode('_', $inputName);
   $answerId = $parts[1];
   
   $type = $_FILES[$inputName]['type'];
   $imageTypeParts = explode('/', $type);
   $imageType = $imageTypeParts[1];
   $ext = $imageType;
   
   //$json['files'] = json_encode($_FILES);
   //$json['type'] = $type;
   
   if (($imageType == "jpeg") || ($imageType == "jpg"))
      $ext = "jpg";
   else if ($imageType == "png")
      $ext = "png";
   else
      throw new Exception("The image needs to be png or jpg.");
      
   $toPath = '../' . $uploaddir . $answerId . '.' . $ext;
      
   if (move_uploaded_file($fromPath, $toPath))
   {
      // Is it big enough?
      
      $image_info = getimagesize($toPath);
      
      $width = $image_info[0];
      $height = $image_info[1];
      $aspectRatio = ($width > $height) ? ($width / $height) : ($height / $width);
      
      //if (($width < $MINSIZE) && ($height < $MINSIZE))
      //   throw new Exception("At least one dimension needs to be at least $MINSIZE pixels.");

      // Convert to jpg

      if ($imageType == "png")
      {
         $fromPath = $toPath;
         $toPath = '../' . $uploaddir . $answerId . '.jpg';
         
         $image = imagecreatefrompng($fromPath);
         imagejpeg($image, $toPath, 100);
         imagedestroy($image);
      }
      
      // Pad to square if needed
      /*
      if ($width != $height)
      {
         $image = imagecreatefromjpeg($toPath);
         $squareSize = max($width, $height);
         $x = $y = 0;
            
         if ($width > $height)
         {
             // Pad top and bottom
             
            $y = ((($width - $height) / 2));
         }      
         else
         {
             // Pad left and right
   
            $x = ((($height - $width) / 2));
         }      
   
         $square = imagecreatetruecolor($squareSize, $squareSize);
         $fillColor = imagecolorallocate($square, 255, 255, 255);
         //$fillColor = imagecolorat($image, 0, 0);
         
         imagefill($square, 0, 0, $fillColor);
         imagefill($square, ($squareSize - 1), ($squareSize - 1), $fillColor);
   
         imagecopy($square, $image, $x, $y, 0, 0, $width, $height);
         imagejpeg($square, $toPath, 100);

         imagedestroy($square);
         imagedestroy($image);
      }
      */
      
      // TODO: Scale the image down if needed
      
      $json['answerId'] = $answerId;
      $json['imageUrl'] = "http://" . $_SERVER['HTTP_HOST'] . "/api" . $uploaddir . $answerId . '.jpg';
      $json['result'] = true;
	}
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}

echo json_encode($json);

?>
