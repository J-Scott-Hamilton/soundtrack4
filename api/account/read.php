<?php

require_once("../session/lookup.php");

$json = array('result' => false);

try
{
   if (!$accountId)
      throw new Exception('invalid-session');
      
   require_once("../includes/db.php");

   $db = db_connect();
   
   $sql = "SELECT * FROM account WHERE account_id = $accountId LIMIT 1";
   $rows = mysql_query($sql);
   
   if ($rows && ($row = mysql_fetch_array($rows, MYSQL_ASSOC)))
   {
      $json['account'] = array('accountId' => $row['account_id']);
      $json['email'] = $row['email'];
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
