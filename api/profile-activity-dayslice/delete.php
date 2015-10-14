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

   $sql = "DELETE FROM profile_activity_dayslice WHERE profile_id = $profileId AND activity_id = $activityId AND dayslice_id = $daysliceId";
   
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
