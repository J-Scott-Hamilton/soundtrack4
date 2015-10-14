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
   $locationCohortId = $params->locationCohortId;

   $sql = "DELETE FROM profile_demographics WHERE profile_id = $profileId AND age_cohort_id = $ageCohortId";

   if ($genderCohortId)
   {
      $sql .= " AND gender_cohort_id = $genderCohortId";
   
      if ($locationCohortId)
      {
         $sql .= " AND location_cohort_id = $locationCohortId";
      }
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
