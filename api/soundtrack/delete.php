<?php

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));

   $soundtrackId = $params->soundtrackId;

   if ($soundtrackId)
   {
      $sql = "DELETE FROM soundtrack WHERE soundtrack_id = $soundtrackId";
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
