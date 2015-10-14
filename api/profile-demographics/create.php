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
   
   $ageCohortId = $params->ageCohortId;
   
   if (!$ageCohortId)
      throw new Exception('ageCohortId-required');
   
   $genderCohortId = $params->genderCohortId;

   if (!$genderCohortId)
      throw new Exception('genderCohortId-required');
   
   $locationCohortId = $params->locationCohortId;

   if (!$locationCohortId)
      throw new Exception('locationCohortId-required');
   
   mysql_query("INSERT INTO profile_demographics (profile_id, age_cohort_id, gender_cohort_id, location_cohort_id) VALUES ($profileId, $ageCohortId, $genderCohortId, $locationCohortId)");

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
