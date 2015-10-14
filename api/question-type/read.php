<?php

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();   
   $params = json_decode(file_get_contents('php://input'));

   $sql = "SELECT * FROM question_type";
   $rows = mysql_query($sql);
   $types = array();
   
   while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
   {
      $types[] = questionTypeToJson($row);
   }
   
   $json['questionTypes'] = $types;
   $json['result'] = true;
      
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

function questionTypeToJson($row)
{
   return array(
            'questionTypeId' => intval($row['question_type_id']),
            'name' => $row['name']);
}

?>
