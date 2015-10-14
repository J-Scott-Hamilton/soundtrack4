<?php

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));

   $questionId = $params->questionId;
   $profileId = $params->profileId;
   $sql = "INSERT INTO question_profile (question_id, profile_id) VALUES ($questionId, $profileId)";

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
