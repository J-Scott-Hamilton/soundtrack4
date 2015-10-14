<?php

require_once("../session/lookup.php");

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();
   
   $params = json_decode(file_get_contents('php://input'));

   $questionId = $params->questionId;
   $starterQuestion = $params->starterQuestion;
   
   if ($questionId)
   {
      $sql = "SELECT * FROM question WHERE question_id = $questionId";
      $rows = mysql_query($sql);
      
      if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $question = questionToJson($row);
         $answers = array();
         
         // Add the answers
         
         $sql = "SELECT * FROM answer WHERE question_id = $questionId";
         $rows = mysql_query($sql);
         
         while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
         {
            $answers[] = answerToJson($row);  
         }
         
         $question['answers'] = $answers;     
      
         $json['question'] = $question;
         $json['result'] = true;
      }
   }
   else if ($accountId)
   {
      $sql = "SELECT * FROM question WHERE question_id NOT IN (SELECT question_id FROM account_answer WHERE account_id = $accountId) ";
      
      if ($starterQuestion)
      {
         $sql .= "AND starter = 1 ";
      }
      
      // Don't ask questions that are designed for other profiles
      
      $sql .= "AND question_id NOT IN (";
      $sql .= "SELECT DISTINCT question_id FROM question_profile qp JOIN ";
      $sql .= "(SELECT profile_id FROM account_profile WHERE account_id = $accountId GROUP BY profile_id ORDER BY SUM(weight) DESC LIMIT 3) ";
      $sql .= "as ap ON ap.profile_id = qp.profile_id) ";
      $sql .= "ORDER BY RAND() LIMIT 1";
      
      $rows = mysql_query($sql);
      $question = null;
      
      if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $question = questionToJson($row);
         $questionId = intval($row['question_id']);
         $answers = array();
         
         // Add the answers
         
         $sql = "SELECT * FROM answer WHERE question_id = $questionId";
         $rows = mysql_query($sql);
         
         while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
         {
            $answers[] = answerToJson($row);  
         }
         
         $question['answers'] = $answers;     
      }
   
      $json['question'] = $question;
      $json['result'] = true;
   }
   else
   {
      $sql = "SELECT * FROM question";
      $rows = mysql_query($sql);
      $questions = array();
      
      while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $questions[] = questionToJson($row);
      }
   
      $json['questions'] = $questions;
      $json['result'] = true;
   }
      
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

function questionToJson($row)
{
   $questionId = intval($row['question_id']);
   $question = array('questionId' => $questionId,
                     'questionTypeId' => intval($row['question_type_id']),
                     'text' => $row['text'],
                     'starter' => intval($row['starter']),
                     'tooltip' => $row['tooltip'],
                     'comment' => $row['comment']);

   $imagePath = "/images/questions/originals/". $questionId . '.jpg';
   $imageUrl = "http://soundtrack4.com/api" . $imagePath;
   
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $imageUrl);
   curl_setopt($ch, CURLOPT_NOBODY, 1);
   curl_setopt($ch, CURLOPT_FAILONERROR, 1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   
   if (curl_exec($ch) !== FALSE)
   {
      $question['imageUrl'] = $imageUrl;
   }
   
   curl_close($ch);
   
   return $question;
}

function answerToJson($row)
{
   $answerId = intval($row['answer_id']);
   $answer = array('answerId' => intval($row['answer_id']),
                   'questionId' => intval($row['question_id']),
                   'tooltip' => $row['tooltip'],
                   'text' => $row['text']);

   $imagePath = "/images/answers/originals/". $answerId . '.jpg';
   $imageUrl = "http://soundtrack4.com/api" . $imagePath;  // TODO
   
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_URL, $imageUrl);
   curl_setopt($ch, CURLOPT_NOBODY, 1);
   curl_setopt($ch, CURLOPT_FAILONERROR, 1);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   
   if (curl_exec($ch) !== FALSE)
   {
      $answer['imageUrl'] = $imageUrl;
   }
   
   curl_close($ch);
   
   return $answer;
}

?>
