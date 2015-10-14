<?php

require_once("../session/lookup.php");
require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
   
   if (!$accountId)
      throw new Exception('invalid-session');
   
   // Calculate profile based on answers
   
   $sql = "SELECT profile_id, SUM(score) as total FROM account_answer, answer_profile WHERE account_id = $accountId AND account_answer.answer_id = answer_profile.answer_id GROUP BY profile_id";
   $rows = mysql_query($sql);
   $maxScore = 0;
   $maxProfileId = 1;
         
   while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
   {
      $profileId = $row['profile_id'];
      $score = intval($row['total']);
   
      if ($score > $maxScore)
      {
         $maxProfileId = $profileId;
         $maxScore = $score;
      }   
   }
   
   $json['profileId'] = intval($maxProfileId);
   $json['result'] = true;
   
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

?>
