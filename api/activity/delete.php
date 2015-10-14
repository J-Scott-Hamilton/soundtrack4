<?php

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));

   $activityId = $params->activityId;

   if ($activityId)
   {
      $sql = "DELETE FROM activity WHERE activity_id = $activityId";
      $rows = mysql_query($sql);

      // TODO: fall-out
      //$sql = "DELETE FROM gender_answer WHERE gender_activity_id = $activityId";
      //$rows = mysql_query($sql);

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
