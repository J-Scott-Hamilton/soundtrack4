<?php

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));

   $tagId = $params->tagId;

   if ($tagId)
   {
      $sql = "DELETE FROM tag_choice WHERE tag_id = $tagId";
      $rows = mysql_query($sql);

      $sql = "DELETE FROM tag WHERE tag_id = $tagId";
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
