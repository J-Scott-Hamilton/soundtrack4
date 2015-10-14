<?php

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
   
   if ($params->name)
   {
      $name = as_db_string($params->name);
      
      $sql = "SELECT * FROM dayslice WHERE name = $name";
      $rows = mysql_query($sql);
         
      if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $json['dayslice'] = daysliceToJson($row);
         $json['result'] = true;
      }
   }
   else
   {
      $sql = "SELECT * FROM dayslice ORDER BY start_hour ASC";
      $rows = mysql_query($sql);
      $slices = array();
        
      while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $slices[] = daysliceToJson($row);
      }
    
      $json['dayslices'] = $slices;  
      $json['result'] = true;
   }   
   
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

function daysliceToJson($row)
{
   $json = array();
   $json['daysliceId'] = intval($row['dayslice_id']);
   $json['name'] = $row['name'];
   $json['startHour'] = intval($row['start_hour']);
   $json['endHour'] = intval($row['end_hour']);
   
   return $json;   
}

?>
