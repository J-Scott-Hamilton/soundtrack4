<?php

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode($_REQUEST['json']);
   $sql = null;

   $answerId = $params->answerId;
   $profileId = $params->profileId;
   $weight = $params->weight;
   
   if ($weight > 0)
   {
      $sql = "REPLACE INTO answer_profile (answer_id, profile_id, weight) VALUES ($answerId, $profileId, $weight)";
   }
   else
   {
      $sql = "DELETE FROM answer_profile WHERE answer_id = $answerId AND profile_id = $profileId"; 
   }
   
   mysql_query($sql);

   $json['result'] = true;
   
   db_close($db);
}
catch (Exception $e)
{
   db_close($db);
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

?>
