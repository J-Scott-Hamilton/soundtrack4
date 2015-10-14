<?php

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));

   $songId = $params->songId;

   if ($songId)
   {
      $sql = "DELETE FROM song WHERE song_id = $songId";
      mysql_query($sql);

      $sql = "DELETE FROM song_soundtrack WHERE song_id = $songId";
      mysql_query($sql);

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
