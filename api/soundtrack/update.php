<?php

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
   
   $soundtrackId = $params->soundtrackId;

   if (!$soundtrackId)
      throw new Exception('soundtrackId-required');
      
   $fields = array();

   if (isset($params->shuffle))
      $fields['shuffle'] = ($params->shuffle) ? 1 : 0;

   $sql = as_db_update("soundtrack", $fields, 'soundtrack_id', $soundtrackId);
   
   mysql_query($sql);
   
   $json['result'] = true;
   
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

?>
