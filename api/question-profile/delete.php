<?php

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));

   $questionId = $params->questionId;
   $profileId = $params->profileId;
   $sql = "DELETE FROM question_profile WHERE question_id = $questionId AND profile_id = $profileId"; 
   
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
