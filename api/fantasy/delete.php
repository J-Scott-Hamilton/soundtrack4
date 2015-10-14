<?php

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));

   $fantasyId = $params->fantasyId;

   if ($fantasyId)
   {
      $sql = "DELETE FROM fantasy WHERE fantasy_id = $fantasyId";
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
