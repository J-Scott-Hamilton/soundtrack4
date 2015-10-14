<?php

require_once("../session/lookup.php");

$json = array('result' => false);

try
{
   $params = json_decode(file_get_contents('php://input'));
   
   if (!$accountId)
      throw new Exception('invalid-session');

   $db = db_connect();

   // Calculate the tags
         
   $tags = array();
   
   $sql = "SELECT tag_id, tag_choice_id, SUM(weight) as count FROM account_tag WHERE account_id = $accountId GROUP BY tag_id, tag_choice_id";
   $rows = mysql_query($sql);
   
   if ($rows)
   {
      while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $tagId = intval($row['tag_id']);
         $tagChoiceId = intval($row['tag_choice_id']);
         $count = floatval($row['count']);
         $tags[] = array('tagId' => $tagId, 'tagChoiceId' => $tagChoiceId, 'count' => $count);
      }
   }
   
   $demographics['tags'] = $tags;
   
   // Calculate the profile(s)
         
   $profiles = array();
   
   $sql = "SELECT profile_id, SUM(weight) as count FROM account_profile WHERE account_id = $accountId GROUP BY profile_id";
   $rows = mysql_query($sql);
   
   if ($rows)
   {
      while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $profileId = intval($row['profile_id']);
         $count = floatval($row['count']);
         $profiles[] = array('profileId' => $profileId, 'count' => $count);
      }
   }
   
   $demographics['profiles'] = $profiles;
   
   $json['demographics'] = $demographics;
   $json['result'] = true;
   
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

?>
