<?php

require_once("../session/lookup.php");

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();   
   $params = json_decode(file_get_contents('php://input'));
   $answerId = $params->answerId;
   $questionId = $params->questionId;

   if ($answerId)
   {
      $sql = "SELECT * FROM answer WHERE answer_id = $answerId";
      $rows = mysql_query($sql);
      
      if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $answer = answerToJson($row);
         
         $json['answer'] = $answer;
         $json['result'] = true;
      }
   }
   else if ($questionId)
   {
      $answers = array();
      
      $sql = "SELECT * FROM answer WHERE question_id = $questionId";
      $rows = mysql_query($sql);
      
      while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $answer = answerToJson($row);
         $answerId = intval($row['answer_id']);
         
         $sql = "SELECT * FROM answer_profile, profile WHERE answer_id = $answerId AND answer_profile.profile_id = profile.profile_id";
         $profileRows = mysql_query($sql);
         $profiles = array();
         
         while ($row = mysql_fetch_array($profileRows, MYSQL_ASSOC))
         {
            $profiles[] = array('profileId' => intval($row['profile_id']), 'name' => $row['name']);
         }
         
         $answer['profiles'] = $profiles;

         $sql = "SELECT tag.name as tagName, tag_choice.name as tagChoiceName, tag.tag_id, tag_choice.tag_choice_id FROM " .
                     "answer_tag, tag, tag_choice WHERE answer_id = $answerId AND answer_tag.tag_id = tag.tag_id AND answer_tag.tag_choice_id = tag_choice.tag_choice_id " .
                     "ORDER BY answer_tag.tag_id ASC";
                     
         $tagRows = mysql_query($sql);
         $tags = array();
         
         while ($row = mysql_fetch_array($tagRows, MYSQL_ASSOC))
         {
            $tags[] = array('tagId' => intval($row['tag_id']), 
                            'tagName' => $row['tagName'], 
                            'tagChoiceId' => intval($row['tag_choice_id']), 
                            'tagChoiceName' => $row['tagChoiceName']);
         }
         
         $answer['tags'] = $tags;
      
         $answers[] = $answer;
      }   
   
      $json['answers'] = $answers;
      $json['result'] = true;
   }   
   else if ($accountId)
   {
      $sql = "SELECT COUNT(*) AS answer_count FROM account_answer WHERE account_id = $accountId";

      if (isset($params->starterQuestion) && $params->starterQuestion)
      {
         $sql .= " AND question_id IN (SELECT question_id FROM question WHERE starter = 1)";
      }
      
      $json['sql'] = $sql;
      
      $rows = mysql_query($sql);
      
      if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $json['answerCount'] = intval($row['answer_count']);
         $json['result'] = true;
      }
   }

   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

function answerToJson($row)
{
   $answerId = intval($row['answer_id']);
   $answer = array('answerId' => intval($row['answer_id']),
                   'questionId' => intval($row['question_id']),
                   'tooltip' => $row['tooltip'],
                   'text' => $row['text']);

   $imagePath = "/images/answers/originals/". $answerId . '.jpg';
   $imageRelative = ".." . $imagePath;
   $imageUrl = "http://soundtrack4.com" . "/api" . $imagePath;
   
   if (file_exists($imageRelative))
   {
      $answer['imageUrl'] = $imageUrl;
   }
   
   return $answer;
}

?>
