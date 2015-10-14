<?php

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));

   $answerId = $params->answerId;

   if (!$answerId)
      throw new Exception('answerId-required');

   $sql = "DELETE FROM answer WHERE answer_id = $answerId";
   $rows = mysql_query($sql);

   $json['result'] = true;
      
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

?>
