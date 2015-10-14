<?php

require_once("../includes/db.php");
   
function ageCohortRead($params = null)
{
   $json = array('result' => false);
   
   try
   {
      $db = db_connect();
      
      if (!$params)
         $params = json_decode(file_get_contents('php://input'));
   
      $ageCohortId = $params->ageCohortId;
      $age = $params->age;
      
      if ($ageCohortId)
      {
         $sql = "SELECT * FROM age_cohort WHERE age_cohort_id = $ageCohortId";
         $rows = mysql_query($sql);
         
         if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
         {
            $json['ageCohort'] = ageCohortToJson($row);
            $json['result'] = true;
         }
      }
      else if ($age)
      {
         $sql = "SELECT * FROM age_cohort WHERE $age >= min_age AND $age <= max_age";
         $rows = mysql_query($sql);
         
         if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
         {
            $json['ageCohort'] = ageCohortToJson($row);
            $json['result'] = true;
         }
      }
      else
      {
         $sql = "SELECT * FROM age_cohort ORDER BY min_age ASC";
         $rows = mysql_query($sql);
         $cohorts = array();
         
         while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
         {
            $cohorts[] = ageCohortToJson($row);
         }
      
         $json['ageCohorts'] = $cohorts;
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

function ageCohortToJson($row)
{
   return array('ageCohortId' => intval($row['age_cohort_id']),
                'name' => $row['name'],
                'minAge' => $row['min_age'],
                'maxAge' => $row['max_age']);
}

?>
