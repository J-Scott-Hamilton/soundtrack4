<?php

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode($_REQUEST['json']);
   $sql = null;

   $answerId = $params->answerId;
   $tagId = $params->tagId;
   $tagChoiceId = $params->tagChoiceId;
   $weight = $params->weight;
   
   if ($weight > 0)
   {
      $sql = "REPLACE INTO answer_tag (answer_id, tag_id, tag_choice_id, weight) VALUES ($answerId, $tagId, $tagChoiceId, $weight)";
   }
   else
   {
      $sql = "DELETE FROM answer_tag WHERE answer_id = $answerId AND tag_id = $tagId AND tag_choice_id = $tagChoiceId"; 
   }
   
   mysql_query($sql);

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
