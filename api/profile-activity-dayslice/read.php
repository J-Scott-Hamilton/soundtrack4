<?php

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));

   $profileId = $params->profileId;

   if ($profileId)
   {
      $sql = "SELECT * FROM profile_activity_dayslice WHERE profile_id = $profileId";
   }
   else
   {
      $sql = "SELECT * FROM profile_activity_dayslice ORDER BY profile_id ASC";
   }
   
   $rows = mysql_query($sql);
   $mappings = array();
   
   while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
   {
      $mappings[] = resultToJson($row);
   }

   $json['results'] = $mappings;
   $json['result'] = true;
      
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

function resultToJson($row)
{
   return array('profileId' => intval($row['profile_id']),
                'activityId' => intval($row['activity_id']),
                'daysliceId' => intval($row['dayslice_id']));
}

?>
