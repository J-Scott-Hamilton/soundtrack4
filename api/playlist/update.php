<?php

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
   
   $playlistId = $params->playlistId;

   if (!$playlistId)
      throw new Exception('playlistId-required');
      
   $fields = array();

   if (isset($params->name))
      $fields['name'] = as_db_string($params->name);
      
   if (isset($params->rdioUrl))
      $fields['rdio'] = as_db_string($params->rdioUrl);
   
   if (isset($params->rdioId))
      $fields['rdio_id'] = as_db_string($params->rdioId);
   
   if (isset($params->sync))
      $fields['sync'] = $params->sync;
   
   if (isset($params->comment))
      $fields['comment'] = as_db_string($params->comment);

   $sql = as_db_update("playlist", $fields, 'playlist_id', $playlistId);
   
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
