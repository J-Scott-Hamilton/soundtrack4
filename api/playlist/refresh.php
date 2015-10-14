<?php

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));

   $playlistId = $params->playlistId;

   if (!$playlistId)
      throw new Exception('playlistId-required');
      
   $sql = "UPDATE playlist SET sync = -1 WHERE playlist_id = $playlistId";
   $rows = mysql_query($sql);

   db_close($db);
   
   // Start the refresh

   $url = 'http://' . $_SERVER['HTTP_HOST'] . "/api/playlist/sync.php?playlistId=$playlistId";
   $parts = parse_url($url);
   
   $fp = fsockopen($parts['host'],
               isset($parts['port']) ? $parts['port'] : 80,
               $errno, $errstr, 30);
   
   $out  = "GET " . $url . " HTTP/1.1\r\n";
   $out .= "Host: " . $parts['host'] . "\r\n";
   $out .= "Connection: Close\r\n\r\n";
   
   fwrite($fp, $out);
   fclose($fp);

   $json['result'] = true;
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

?>
