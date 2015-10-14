<?php

require_once("../session/lookup.php");
require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
   
   $questionId = $params->questionId;
   $answerId = $params->answerId;

   if (!$accountId)
      throw new Exception('accountId-required');

   if (!$questionId)
      throw new Exception('questionId-required');

   if (!$answerId)
      throw new Exception('answerId-required');

   // Record the answer
   
   $sql = "INSERT INTO account_answer (account_id, question_id, answer_id) VALUES ($accountId, $questionId, $answerId)";

   mysql_query($sql);

   // Record the profile(s) that this answer implies

   $sql = "SELECT profile_id, weight FROM answer_profile WHERE answer_id = $answerId";
   $rows = mysql_query($sql);
   
   while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
   {
      $profileId = $row['profile_id'];
      $weight = $row['weight'];
      
      $sql = "INSERT INTO account_profile (account_id, profile_id, weight) VALUES ($accountId, $profileId, $weight)";
      mysql_query($sql);
      
      // TODO: And record the tags that this profile implies?
   }
   
   // Record the tag(s) that this answer implies

   $sql = "SELECT tag_id, tag_choice_id, weight FROM answer_tag WHERE answer_id = $answerId";
   $rows = mysql_query($sql);
   
   while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
   {
      $tagId = $row['tag_id'];
      $tagChoiceId = $row['tag_choice_id'];
      $weight = $row['weight'];
      
      $sql = "INSERT INTO account_tag (account_id, tag_id, tag_choice_id) VALUES ($accountId, $tagId, $tagChoiceId, $weight)";
      mysql_query($sql);
   }
   
   // Return their updated profile
   
   $profiles = array();
   
   $sql = "SELECT profile_id, SUM(weight) as count FROM account_profile WHERE account_id = $accountId GROUP BY profile_id";
   $rows = mysql_query($sql);
   
   $maxProfileId = 0;
   $maxCount = 0;
   
   if ($rows)
   {
      while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $profileId = intval($row['profile_id']);
         $count = floatval($row['count']);
         $profiles[] = array('profileId' => $profileId, 'count' => $count);
         
         if ($count > $maxCount)
         {
            $maxProfileId = $profileId;
            $maxCount = $count;
         }
      }
   }

   $json['primaryProfileId'] = $maxProfileId;
   $json['profiles'] = $profiles;
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
