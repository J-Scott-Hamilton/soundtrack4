<?php

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();   
   $params = json_decode(file_get_contents('php://input'));
   $questionId = $params->questionId;

   if (!$questionId)
      throw new Exception('questionId-required');

   $sql = "SELECT * FROM question_profile WHERE question_id = $questionId";
   $rows = mysql_query($sql);
   $profiles = array();
   
   while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
   {
      $profiles[] = intval($row['profile_id']);
   }
   
   $json['profiles'] = $profiles;
   $json['result'] = true;
   
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

?>
