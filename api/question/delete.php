<?php

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));

   $questionId = $params->questionId;

   if ($questionId)
   {
      $sql = "DELETE FROM answer WHERE question_id = $questionId";
      $rows = mysql_query($sql);

      $sql = "DELETE FROM question WHERE question_id = $questionId";
      $rows = mysql_query($sql);

      $json['result'] = true;
   }
      
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

?>
