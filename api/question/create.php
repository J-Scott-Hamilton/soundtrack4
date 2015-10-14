<?php

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
   
   $fields = array();

   $fields['text'] = as_db_string($params->text);
   $fields['question_type_id'] = $params->questionTypeId;
   $fields['comment'] = as_db_string($params->comment);
   $fields['starter'] = $params->starter;
   $fields['tooltip'] = as_db_string($params->tooltip);
   
   $sql = as_db_insert("question", $fields);
   
   $rows = mysql_query($sql);
   $id = mysql_insert_id();

   if ($id > 0)
   {
   	$json['questionId'] = $id;
      $json['result'] = true;
   }
   
   db_close($db);
}
catch (Exception $e)
{
   db_close($db);
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

?>
