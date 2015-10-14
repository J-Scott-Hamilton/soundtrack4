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
   
   $soundtrackId = $params->soundtrackId;
   $songRdio = $params->songRdio;
   $action = $params->action;
   $a = 0;
   
   if (strcmp($action, "play") == 0)
   {
      $a = 0;
   }
   else if (strcmp($action, "replay") == 0)
   {
      $a = 1;
   }
   else if (strcmp($action, "skip") == 0)
   {
      $a = (-1);
   }
   else if (strcmp($action, "like") == 0)
   {
      $a = 2;
   }
   else if (strcmp($action, "dislike") == 0)
   {
      $a = (-2);
   }
   
   // Record the action
   
   $sql = "INSERT INTO account_song (account_id, soundtrack_id, song_id, action) VALUES ($accountId, $soundtrackId, (SELECT song_id FROM song WHERE rdio = '$songRdio'), $a)";
   mysql_query($sql);

   // Remove the song from the queue
   
   $sql = "DELETE FROM account_queue " .
            "WHERE song_id = (SELECT song_id FROM song WHERE rdio = '$songRdio') " .
            "AND account_id = $accountId";
   
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
