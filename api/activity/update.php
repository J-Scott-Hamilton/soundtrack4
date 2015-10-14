<?php

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
   
   $activityId = $params->activityId;

   if ($activityId)
   {
      $fields = array();
      $fields['name'] = as_db_string($params->name);

      $sql = as_db_update("activity", $fields, 'activity_id', $activityId);
      mysql_query($sql);

      $json['result'] = true;
   }
   
   db_close($db);
}
catch (Exception $e)
{
   db_close($db);
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

?>
