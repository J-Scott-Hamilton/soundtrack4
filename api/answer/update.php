<?php

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
   $params = json_decode($_REQUEST['json']);
   $answerId = $params->answerId;

   $fields = array();
   $fields['tooltip'] = as_db_string($params->tooltip);
   $sql = as_db_update("answer", $fields, 'answer_id', $answerId);
   echo $sql;
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
