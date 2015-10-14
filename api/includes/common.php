<?php

function api($resource, $action, $json = null)
{
   global $SESSIONID;

   $APIROOT = 'http://' . $_SERVER['HTTP_HOST'] . '/api';
   $APIROOT = 'http://localhost/api';
   $ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "$APIROOT/$resource/$action");
   curl_setopt($ch, CURLOPT_HEADER, 0);  
   curl_setopt($ch, CURLOPT_USERAGENT, 'ST4');
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
   curl_setopt($ch, CURLOPT_TIMEOUT, 10);

   $fields = '';
   
   if (isset($json) && ($json != null))
   {
      $fields .= 'json=' . urlencode(json_encode($json));
   }

   if (isset($SESSIONID) && ($SESSIONID != null))
   {
      if (strlen($fields) > 0)
      {
         $fields .= "&";
      }
      
      $fields .= "sessionId=$SESSIONID";
   }

   if (strlen($fields) > 0)
   {      
      curl_setopt($ch, CURLOPT_POST, 1);
   	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
   }
   
	$data = curl_exec($ch);
   
//   echo "$APIROOT/$resource/$action = $data\n";
   
	curl_close($ch);
   
   return json_decode($data);
}

function trimToSentence($str, $maxChars)
{
   if (strlen($str) > $maxChars)
   {
      // Trim back to last period.
      
      $str = substr($str, 0, $maxChars);
      $i = strrpos($str, ".");
      $str = substr($str, 0, ($i + 1));
   }
   
   return $str;
}

?>
