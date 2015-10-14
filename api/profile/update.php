<?php

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));

   $profileId = $params->profileId;
   
   if (!$profileId)
      throw new Exception('profileId-required');

   if (isset($params->description))
   {
      $desc = as_db_string($params->description);
      mysql_query("UPDATE profile SET description = $desc WHERE profile_id = $profileId");
   }

   if (isset($params->shortDescription))
   {
      $desc = as_db_string($params->shortDescription);
      mysql_query("UPDATE profile SET short_desc = $desc WHERE profile_id = $profileId");
   }

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
