<?php

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();   
   $params = json_decode(file_get_contents('php://input'));
   $fantasyId = $params->fantasyId;

   if ($fantasyId)
   {
      $sql = "SELECT * FROM fantasy WHERE fantasy_id = $fantasyId";
      $rows = mysql_query($sql);
      
      if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $json['fantasy'] = fantasyToJson($row);
         $json['result'] = true;
      }
   }
   else
   {
      $sql = "SELECT * FROM fantasy ORDER BY name ASC";
      $rows = mysql_query($sql);
      $fantasies = array();
      
      while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $fantasies[] = fantasyToJson($row);
      }
   
      $json['fantasies'] = $fantasies;
      $json['result'] = true;
   }
      
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

function fantasyToJson($row)
{
   return array('fantasyId' => intval($row['fantasy_id']),
                'name' => $row['name']);
}

?>
