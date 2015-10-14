<?php

require_once("../session/lookup.php");
require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));

   if (!$accountId)
      throw new Exception('invalid-accountId');
   
   mysql_query("DELETE FROM account_answer WHERE account_id = $accountId");
   mysql_query("DELETE FROM account_profile WHERE account_id = $accountId");
   mysql_query("DELETE FROM account_tag WHERE account_id = $accountId");
   mysql_query("DELETE FROM account_queue WHERE account_id = $accountId");
   mysql_query("DELETE FROM account WHERE account_id = $accountId");
   
   $json['result'] = true;
   
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

?>
