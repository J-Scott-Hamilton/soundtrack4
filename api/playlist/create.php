<?php

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
   
   $fields = array();

   $fields['name'] = as_db_string($params->name);
   $fields['rdio'] = as_db_string($params->rdioUrl);
   $fields['rdio_id'] = as_db_string($params->rdioId);
   $fields['sync'] = as_db_string($params->sync);
   $fields['comment'] = as_db_string($params->comment);

   $sql = as_db_insert("playlist", $fields);
   
   $rows = mysql_query($sql);
   $playlistId = mysql_insert_id();

   if ($playlistId > 0)
   {
   	$json['playlistId'] = $playlistId;
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
