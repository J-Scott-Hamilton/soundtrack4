<?php

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));

   $sql = "SELECT COUNT(*) as count, age_cohort_id, gender_cohort_id, location_cohort_id FROM profile_demographics GROUP BY age_cohort_id, gender_cohort_id, location_cohort_id HAVING count > 1";
   $rows = mysql_query($sql);
   $overlaps = array();
   $profileHashes = array();
     
   while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
   {
      $ageCohortId = intval($row['age_cohort_id']);
      $genderCohortId = intval($row['gender_cohort_id']);
      $locationCohortId = intval($row['location_cohort_id']);
   
      $overlap = overlapToJson($row);

      // Find the profiles in the overlap
      
      $sql = "SELECT * FROM profile, profile_demographics WHERE age_cohort_id = $ageCohortId AND gender_cohort_id = $genderCohortId AND " .
                  "location_cohort_id  = $locationCohortId AND profile.profile_id = profile_demographics.profile_id ORDER BY profile.profile_id";
      
      //echo $sql;
      //echo "<br>\n<br>\n<br>\n";
      
      $profileRows = mysql_query($sql);
      $profiles = array();
      $profileIds = array();
      $hash = '';
      
      while ($row = mysql_fetch_array($profileRows, MYSQL_ASSOC))
      {
         $profileId = intval($row['profile_id']);
         $profileIds[] = $profileId;
         $hash .= $profileId;
         
         $profiles[] = profileToJson($row);
      }

      if (in_array($hash, $profileHashes))
         continue;

      $profileHashes[] = $hash;
      
      // Is there a question for this overlap?
      
      $sql = "SELECT * FROM question WHERE question_id IN " .
               "(SELECT question_id FROM (SELECT question_id, COUNT(*) as answer_count FROM answer WHERE answer_id IN " .
               "(SELECT answer_id FROM answer_profile WHERE answer_profile.profile_id IN (" . implode(',', $profileIds) . ")) GROUP BY question_id) as a " .
               "WHERE answer_count >= " . count($profileIds) . ")";

      //echo $sql;
      //echo "<br>\n<br>\n<br>\n";
      
      $answerRows = mysql_query($sql);
      $questions = array();

      while ($row = mysql_fetch_array($answerRows, MYSQL_ASSOC))
      {
         $questions[] = questionToJson($row);
      }
      
      $overlap['profiles'] = $profiles;
      $overlap['questions'] = $questions;
      $overlaps[] = $overlap;
   }

   $json['overlaps'] = $overlaps;
   $json['result'] = true;
      
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

function overlapToJson($row)
{
   return array('count' => intval($row['count']),
                'ageCohortId' => intval($row['age_cohort_id']),
                'genderCohortId' => intval($row['gender_cohort_id']),
                'locationCohortId' => intval($row['location_cohort_id']));
}

function profileToJson($row)
{
   return array('profileId' => intval($row['profile_id']),
                'name' => $row['name']);
}

function questionToJson($row)
{
   return array('questionId' => intval($row['question_id']),
                'text' => $row['text'],
                'imageId' => $row['image_id']);
}

?>




