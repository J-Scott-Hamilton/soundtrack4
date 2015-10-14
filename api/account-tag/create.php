<?php

require_once("../session/lookup.php");
require_once("../includes/db.php");

$json = array('result' => false);

try
{
   if (!$accountId)
      throw new Exception('accountId-required');

   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
   
   $fields = array();
   $fields['account_id'] = $accountId;
   $fields['tag_id'] = $params->tagId;
   $fields['tag_choice_id'] = $params->tagChoiceId;
   
   if ($params->weight)
      $fields['weight'] = $params->weight;
   
   $sql = as_db_insert("account_tag", $fields);

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
