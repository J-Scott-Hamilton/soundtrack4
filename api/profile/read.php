<?php

$json = array('result' => false);

try
{
   require_once("../includes/db.php");
   require_once("../age-cohort/funcs.php");
   require_once("../gender-cohort/funcs.php");

   $db = db_connect();
   
   $params = json_decode(file_get_contents('php://input'));

   $profileId = $params->profileId;
   $age = $params->age;
   $gender = $params->gender;
   
   if ($profileId)
   {
      $sql = "SELECT * FROM profile WHERE profile_id = $profileId";
      $rows = mysql_query($sql);
      
      if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $json['profile'] = profileToJson($row);
         $json['result'] = true;
      }
   }
   else if ($age || $gender)
   {
      $profiles = array();
      
      if ($age)
      {
         $ret = json_decode(ageCohortRead(json_decode(json_encode(array('age' => $age)))));
         $ageCohortId = $ret->ageCohort->ageCohortId;
      }

      if ($gender)
      {
         $ret = json_decode(genderCohortRead(json_decode(json_encode(array('gender' => $gender)))));
         $genderCohortId = $ret->genderCohort->genderCohortId;
      }

      if ($age && $gender)
      {
         $ageAndOrGender = "WHERE age_cohort_id = $ageCohortId AND gender_cohort_id = $genderCohortId ";
      }
      else if ($age)
      {
         $ageAndOrGender = "WHERE age_cohort_id = $ageCohortId ";
      }
      else if ($gender)
      {
         $ageAndOrGender = "WHERE gender_cohort_id = $genderCohortId ";
      }
      
      // Find the profiles in the overlap

      $sql = "SELECT DISTINCT(profile.profile_id), name FROM profile, profile_demographics " .
                  $ageAndOrGender . 
                  "AND profile.profile_id = profile_demographics.profile_id ORDER BY profile.profile_id";

      $rows = mysql_query($sql);
      
      while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $profiles[] = profileToJson($row);
      }
      
      $json['profiles'] = $profiles;
      $json['result'] = true;
   }
   else
   {
      $sql = "SELECT * FROM profile ORDER BY sequence ASC";
      $rows = mysql_query($sql);
      $profiles = array();
      
      while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $profiles[] = profileToJson($row);
      }
   
      $json['profiles'] = $profiles;
      $json['result'] = true;
   }
      
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

function profileToJson($row)
{
   return array('profileId' => intval($row['profile_id']),
                'name' => $row['name'],
                'symbol' => $row['symbol'],
                'age' => $row['age'],
                'income' => $row['income'],
                'education' => $row['education'],
                'gender' => $row['gender'],
                'description' => (isset($row['description'])) ? $row['description'] : '',
                'shortDescription' => (isset($row['short_desc'])) ? $row['short_desc'] : '');
}

?>
