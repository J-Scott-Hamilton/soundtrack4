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
      $sql = "SELECT * FROM activity WHERE activity_id = $activityId";
      $rows = mysql_query($sql);
      
      if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $json['activity'] = activityToJson($row);
         $json['result'] = true;
      }
   }
   else
   {
      $sql = "SELECT * FROM activity ORDER BY name ASC";
      $rows = mysql_query($sql);
      $activities = array();
      
      while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $activities[] = activityToJson($row);
      }
   
      $json['activities'] = $activities;
      $json['result'] = true;
   }
      
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

function activityToJson($row)
{
   return array('activityId' => intval($row['activity_id']),
                'name' => $row['name']);
}

?>
