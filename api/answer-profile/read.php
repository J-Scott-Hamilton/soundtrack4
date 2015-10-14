<?php

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();   
   $params = json_decode(file_get_contents('php://input'));
   $answerId = $params->answerId;

   if (!$answerId)
      throw new Exception('answerId-required');

   $sql = "SELECT * FROM answer_profile WHERE answer_id = $answerId";
   $rows = mysql_query($sql);
   $profiles = array();
   
   while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
   {
      $profiles[] = array(
                        'profileId' => intval($row['profile_id']), 
                        'weight' => intval($row['weight']));
   }
   
   $json['profiles'] = $profiles;
   $json['result'] = true;
      
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

?>
