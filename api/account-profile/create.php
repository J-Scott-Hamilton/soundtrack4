<?php

require_once("../session/lookup.php");
require_once("../includes/db.php");

$json = array('result' => false);

try
{

   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));

   if(!$accountId) $accountId = $params->accountId;

   if (!$accountId)
      throw new Exception('accountId-required');
   
   if (!isset($params->profileId))
      throw new Exception('profileId-required');

   $fields = array();
   $fields['account_id'] = $accountId;
   $fields['profile_id'] = $params->profileId;
   
   if (isset($params->weight))
      $fields['weight'] = $params->weight;
   
   $sql = as_db_insert("account_profile", $fields);
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
