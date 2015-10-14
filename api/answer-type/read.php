<?php

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();   
   $params = json_decode(file_get_contents('php://input'));

   $sql = "SELECT * FROM answer_type";
   $rows = mysql_query($sql);
   $types = array();
   
   while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
   {
      $types[] = answerTypeToJson($row);
   }
   
   $json['answerTypes'] = $types;
   $json['result'] = true;
      
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

function answerTypeToJson($row)
{
   return array(
            'answerTypeId' => intval($row['answer_type_id']),
            'type' => $row['type']);
}

?>
