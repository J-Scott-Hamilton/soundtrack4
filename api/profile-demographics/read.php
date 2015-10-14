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
      $sql = "SELECT * FROM profile_demographics WHERE profile_id = $profileId";
   }
   else
   {
      $sql = "SELECT * FROM profile_demographics ORDER BY profile_id ASC";
   }
   
   $rows = mysql_query($sql);
   $demographics = array();
   
   while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
   {
      $demographics[] = demographicToJson($row);
   }

   $json['demographics'] = $demographics;
   $json['result'] = true;
      
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

function demographicToJson($row)
{
   return array('profileId' => intval($row['profile_id']),
                'ageCohortId' => intval($row['age_cohort_id']),
                'genderCohortId' => intval($row['gender_cohort_id']),
                'locationCohortId' => intval($row['location_cohort_id']));
}

?>
