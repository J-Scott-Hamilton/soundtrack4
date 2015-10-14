<?php

function api($resource, $action, $json = null)
{
   global $st4Session;

   $url = "http://localhost/api/$resource/$action";
   
   if ($st4Session && $st4Session->sessionId)
   {
      $url .= '?sessionId=' . $st4Session->sessionId;
   }

   //echo $url;

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);

   $data_string = json_encode($json); 

   curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
   curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
   curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
       'Content-Type: application/json',                                                                                
       'Content-Length: ' . strlen($data_string))                                                                       
   );                                                	

	$data = curl_exec($ch);
   
	curl_close($ch);
   
   return json_decode($data);
}

/*
function api($resource, $action, $json = null)
{
   global $st4Session;

   $APIROOT = 'http://soundtrack4.com/api';

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "$APIROOT/$resource/$action");
   curl_setopt($ch, CURLOPT_HEADER, 0);  
   curl_setopt($ch, CURLOPT_USERAGENT, 'ST4');
   curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
   curl_setopt($ch, CURLOPT_TIMEOUT, 10);
   curl_setopt($ch, CURLOPT_COOKIESESSION, true);

   $fields = '';
   
   if (isset($json) && ($json != null))
   {
      $fields .= 'json=' . urlencode(json_encode($json));
   }

   if (isset($st4Session) && ($st4Session != null))
   {
      if (strlen($fields) > 0)
      {
         $fields .= "&";
      }
      
      $fields .= "sessionId=" . $st4Session->sessionId;
   }

echo "fields = $fields\n";

   if (strlen($fields) > 0)
   {      
      curl_setopt($ch, CURLOPT_POST, 1);
   	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
   }
   
	$data = curl_exec($ch);
   
   //echo "data = $data\n";
   
	curl_close($ch);
   
   return json_decode($data);
}
*/

?>
