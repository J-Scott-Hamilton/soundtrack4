<?php

$json = array('result' => false);

$MINSIZE = 100;

try 
{
   $uploaddir = '/images/questions/originals/';

   // Extract questionId from files
   
   $keys = array_keys($_FILES);
   $inputName = $keys[0];
   $fromPath = $_FILES[$inputName]['tmp_name'];

   $parts = explode('_', $inputName);
   $questionId = $parts[1];
   
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
      
   $toPath = '../' . $uploaddir . $questionId . '.' . $ext;
      
   if (move_uploaded_file($fromPath, $toPath))
   {
      // Is it big enough?
      
      $image_info = getimagesize($toPath);
      
      $width = $image_info[0];
      $height = $image_info[1];
      $aspectRatio = ($width > $height) ? ($width / $height) : ($height / $width);
      
      //if (($width < $MINSIZE) || ($height < $MINSIZE))
      //   throw new Exception("The image needs to be at least $MINSIZE x $MINSIZE.");

      // TODO: Scale the image down if needed
      // TODO: Move images to Amazon S3?
      
      // Convert to jpg

      if ($imageType == "png")
      {
         $fromPath = $toPath;
         $toPath = '../' . $uploaddir . $questionId . '.jpg';
         
         $image = imagecreatefrompng($fromPath);
         imagejpeg($image, $toPath, 100);
         imagedestroy($image);
      }
      
      $json['questionId'] = $questionId;
      $json['imageUrl'] = "http://" . $_SERVER['HTTP_HOST'] . "/api" . $uploaddir . $questionId . '.jpg';
      $json['result'] = true;
	}
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}

echo json_encode($json);

?>
