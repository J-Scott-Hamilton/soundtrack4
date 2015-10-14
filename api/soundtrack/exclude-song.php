<?php

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
      
   if (!$params->songRdio)
      throw new Exception('missing songRdio');

   $songRdio = $params->songRdio;
   
   if (!$params->soundtrackId)
      throw new Exception('missing soundtrackId');
      
   $fields = array();
   $fields['soundtrack_id'] = $params->soundtrackId;
   $fields['song_id'] = "(SELECT song_id FROM song WHERE rdio = '$songRdio')";
   $sql = as_db_insert("soundtrack_exclude_song", $fields);
   
   $rows = mysql_query($sql);

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
