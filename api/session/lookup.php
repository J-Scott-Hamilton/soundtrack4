<?php

$sessionId = null;
$accountId = null;

if (isset($_GET['sessionId']))
   $sessionId = $_GET['sessionId'];
else if (isset($_POST['sessionId']))
   $sessionId = $_POST['sessionId'];

if ($sessionId)
{
   try
   {
      require_once __DIR__ . '/../includes/db.php';
      
      $db = db_connect();   

      $sql  = "SELECT account_id FROM `session` WHERE session_id = '$sessionId'";
      $rows = mysql_query($sql);
      
      if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $accountId = intval($row['account_id']);
      }
      
      db_close($db);
   }
   catch (Exception $e)
   {
   }
}

?>
