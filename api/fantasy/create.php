<?php

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
   
   $fields = array();
   $fields['name'] = as_db_string($params->name);

   $sql = as_db_insert("fantasy", $fields);
   
   $rows = mysql_query($sql);
   $id = mysql_insert_id();

   if ($id > 0)
   {
   	$json['fantasyId'] = $id;
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
