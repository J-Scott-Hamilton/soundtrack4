<?php

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
   
   $fields = array();

   $fields['dayslice_id'] = $params->daysliceId;
   $fields['profile_id'] = $params->profileId;
   $fields['activity_id'] = $params->activityId;
   
   if (isset($params->shuffle))
      $fields['shuffle'] = ($params->shuffle) ? 1 : 0;

   $sql = as_db_insert("soundtrack", $fields);
      
   $rows = mysql_query($sql);
   $soundtrackId = mysql_insert_id();

   if ($soundtrackId > 0)
   {
   	$json['soundtrackId'] = $soundtrackId;
      $json['result'] = true;
   }
   
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

?>
