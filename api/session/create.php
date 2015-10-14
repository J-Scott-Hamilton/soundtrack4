<?php

require_once __DIR__ . '/../includes/db.php';

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
   
   if (!isset($params->facebookAccessToken))
      throw new Exception('facebookAccessToken-required');

   // Convert facebook access token into a facebook id
   
   require_once __DIR__ . '/../../includes/fb.php';

   $facebook->setAccessToken($params->facebookAccessToken);
   
   $facebookMe = $facebook->api('/me');
   $facebookId = $facebookMe['id'];
   $facebookPic = $facebook->api('/me/picture');

   // Does an account exist?
   
   $sql = "SELECT * FROM account WHERE facebook_id = $facebookId";
   $rows = mysql_query($sql);

   if ((mysql_num_rows($rows) > 0) &&
       ($row = mysql_fetch_array($rows, MYSQL_ASSOC)))
   {
      $accountId = intval($row['account_id']);
      $sessionId = uniqid();
      $sessionIdStr = as_db_string($sessionId);


      mysql_query("DELETE FROM account_profile WHERE account_id=$accountId");
      mysql_query("DELETE FROM account_answer WHERE account_id=$accountId");
   
      $sql = "REPLACE INTO session (account_id, session_id) VALUES ($accountId, $sessionIdStr)";
      $rows = mysql_query($sql);
   
      if (mysql_affected_rows() > 0)
      {
      	$json['sessionId'] = $sessionId;
         $json['firstName'] = $row['first_name'];
         $json['picture'] = $facebookPic;
         $json['result'] = true;
      }
   }
   
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

?>
