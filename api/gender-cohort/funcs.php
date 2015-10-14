<?php

require_once("../includes/db.php");
   
function genderCohortRead($params = null)
{
   $json = array('result' => false);
   
   try
   {
      $db = db_connect();
      
      if (!$params)
         $params = json_decode(file_get_contents('php://input'));
   
      $genderCohortId = $params->genderCohortId;
      $gender = $params->gender;
      
      if ($genderCohortId)
      {
         $sql = "SELECT * FROM gender_cohort WHERE gender_cohort_id = $genderCohortId";
         $rows = mysql_query($sql);
         
         if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
         {
            $json['genderCohort'] = genderCohortToJson($row);
            $json['result'] = true;
         }
      }
      else if ($gender)
      {
         $sql = "SELECT * FROM gender_cohort WHERE name = '$gender'";
         $rows = mysql_query($sql);
         
         if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
         {
            $json['genderCohort'] = genderCohortToJson($row);
            $json['result'] = true;
         }
      }
      else
      {
         $sql = "SELECT * FROM gender_cohort ORDER BY name ASC";
         $rows = mysql_query($sql);
         $cohorts = array();
         
         while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
         {
            $cohorts[] = genderCohortToJson($row);
         }
      
         $json['genderCohorts'] = $cohorts;
         $json['result'] = true;
      }
         
      db_close($db);
   }
   catch (Exception $e)
   {
      $json['reason'] = $e->getMessage();
   }  
   
   return json_encode($json);
}

function genderCohortToJson($row)
{
   return array('genderCohortId' => intval($row['gender_cohort_id']),
                'name' => $row['name']);
}

?>
