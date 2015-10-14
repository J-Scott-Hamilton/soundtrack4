<?php

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
   
   $questionId = $params->questionId;

   if (!$questionId)
      throw new Exception('questionId-required');

   $fields = array();

   $fields['text'] = as_db_string($params->text);
   $fields['comment'] = as_db_string($params->comment);
   $fields['tooltip'] = as_db_string($params->tooltip);
   $fields['starter'] = $params->starter;

   $sql = as_db_update("question", $fields, 'question_id', $questionId);

   mysql_query($sql);

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
