<?php

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
   
   $profileId = $params->profileId;

   if (!$profileId)
      throw new Exception('profileId-required');
   
   $activityId = $params->activityId;
   
   if (!$activityId)
      throw new Exception('activityId-required');
   
   $daysliceId = $params->daysliceId;
   
   if (!$daysliceId)
      throw new Exception('daysliceId-required');   
   
   mysql_query("INSERT INTO profile_activity_dayslice (profile_id, activity_id, dayslice_id) VALUES ($profileId, $activityId, $daysliceId)");

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
