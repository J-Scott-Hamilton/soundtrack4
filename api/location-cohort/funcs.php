<?php

require_once("../includes/db.php");
   
function locationCohortRead()
{
   $json = array('result' => false);
   
   try
   {
      $db = db_connect();
      $params = json_decode(file_get_contents('php://input'));
   
      $cohortId = $params->locationCohortId;
   
      if ($cohortId)
      {
         $sql = "SELECT * FROM location_cohort WHERE location_cohort_id = $cohortId";
         $rows = mysql_query($sql);
         
         if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
         {
            $json['locationCohort'] = locationCohortToJson($row);
            $json['result'] = true;
         }
      }
      else
      {
         $sql = "SELECT * FROM location_cohort ORDER BY name ASC";
         $rows = mysql_query($sql);
         $cohorts = array();
         
         while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
         {
            $cohorts[] = locationCohortToJson($row);
         }
      
         $json['locationCohorts'] = $cohorts;
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

function locationCohortToJson($row)
{
   return array('locationCohortId' => intval($row['location_cohort_id']),
                'name' => $row['name']);
}

?>
