<?php

require_once("../session/lookup.php");
require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
   
   if(!$accountId) $accountId = $params->accountId;
   $soundtrackId = $params->soundtrackId;
   
   if ($soundtrackId)
   {
      $sql = "SELECT * FROM account_queue,song WHERE account_id = $accountId AND soundtrack_id = $soundtrackId AND " .
                  "account_queue.song_id = song.song_id ORDER BY sequence ASC";
      
      $rows = mysql_query($sql);
      $songs = array();
      
      while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $songs[] = songToJson($row);
      }
   
      $json['songs'] = $songs;   
      $json['result'] = true;
   }
   else
   {
      $sql = "SELECT DISTINCT(account_queue.account_id), account.* FROM account_queue,account WHERE " .
                  "account_queue.account_id = account.account_id";
      
      $rows = mysql_query($sql);
      $queues = array();
      
      while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $queues[] = queueToJson($row);
      }
   
      $json['queues'] = $queues;   
      $json['result'] = true;
   }
      
   db_close($db);
}
catch (Exception $e)
{
   db_close($db);
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

function songToJson($row)
{
   return array('songId' => intval($row['song_id']),
                'name' => $row['name'],
                'artist' => $row['artist'],
                'album' => $row['album'],
                'spotify' => $row['spotify'],
                'rdio' => $row['rdio']);
}

function queueToJson($row)
{
   return array('accountId' => intval($row['account_id']),
                'firstName' => $row['first_name']);
}


?>
