<?php

set_time_limit(0);

// Getting low on songs, add more...

require_once("../session/lookup.php");
require_once("../includes/db.php");
require_once("./funcs.php");

$json = array('result' => false);

try
{
   if (!$accountId)
      throw new Exception('invalid-accountId');

   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
   
   $soundtrackId = $params->soundtrackId;

   // Find sequence to start at
   
   $sql = "SELECT sequence FROM account_queue WHERE account_id = $accountId AND soundtrack_id = $soundtrackId ORDER BY sequence DESC LIMIT 1";
   
   $rows = mysql_query($sql);
   $startingSequence = 1;
   
   if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
   {
      $startingSequence = (intval($row['sequence']) + 1);
   }
   
   addSongsToQueue($accountId, $soundtrackId, null, $startingSequence);

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
