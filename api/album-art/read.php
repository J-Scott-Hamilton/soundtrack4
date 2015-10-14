<?php

$json = array('result' => false);

try
{
   require_once("../includes/db.php");
   require_once("../../includes/rdio.php");
   require_once("../../includes/rdio/rdio.php");

   $db = db_connect();   
   $params = json_decode(file_get_contents('php://input'));
   $limit = (isset($params->limit)) ? $params->limit : 1;
   
   $rdio = new Rdio(array($rdio_api_key, $rdio_api_secret));
   $tracks = array();
      
   $sql = "SELECT rdio FROM song ORDER BY RAND() LIMIT $limit";
   $rows = mysql_query($sql);

   while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
   {
      $tracks[] = $row['rdio'];
   }
   
   $params = array();
   $params['keys'] = implode(",", $tracks);

   $albums = array();
   $search = $rdio->call('get', $params);
   
   foreach ($search->result as $track => $info)
   {
      $albums[] = $info->icon;
   }
   
   $json['albums'] = $albums;
   $json['result'] = true;

   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

?>
