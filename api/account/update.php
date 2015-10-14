<?php

require_once("../session/lookup.php");
require_once("../includes/db.php");

$json = array('result' => false);

try
{
   if (!$accountId)
      throw new Exception('invalid-accountId');

   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
      
   $fields = array();

   if (isset($params->rdioToken))
   {
      $fields['rdio_token'] = as_db_string($params->rdioToken);
      $fields['rdio_secret'] = as_db_string($params->rdioSecret);
   }
   
   $sql = as_db_update('account', $fields, 'account_id', $accountId);
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
