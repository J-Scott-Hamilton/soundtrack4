<?php

$dbhost = 'localhost';
$dbuser = 'root';                                 // jalbano_mysql';
$dbpass = 's0undtr@ck4';                               // jFaD1967';
$dbname = 'st4mysql';                                 // soundtrack_jalbano_net';

$dbConnection = null;
$dbConnectionCount = 0;

function db_connect()
{
   global $dbhost;
   global $dbuser;
   global $dbpass;
   global $dbname;
   
   global $dbConnection;
   global $dbConnectionCount;

   // If we already have one, reuse it

   if (!$dbConnection)
   {
      if (!$dbConnection = mysql_connect($dbhost, $dbuser, $dbpass))
           throw new Exception(mysql_error());
   
      if (!mysql_select_db($dbname))
           throw new Exception(mysql_error());
   }
   
   $dbConnectionCount++;
   
   return $dbConnection;
}

function db_close($dbconn)
{
   global $dbConnectionCount;
   global $dbConnection;
   
   if (--$dbConnectionCount == 0)
   {
      mysql_close($dbconn);
      $dbConnection = null;
   }
}

function as_db_string($s)
{
   $v = mysql_real_escape_string(stripslashes($s));
   
   return (strlen($v) > 0) ? ("'" . mysql_real_escape_string(stripslashes($s)) . "'") : "NULL";
}

function as_db_boolean($b)
{
   return ($b) ? 1 : 0;
}

function as_db_insert($table, $fields)
{
   foreach ($fields as $key => $value)
   {
      $fieldNames[] = $key;
      $fieldValues[] = $value;
   }
   
   return 'INSERT INTO ' . $table . ' (' . implode(',', $fieldNames) . ') VALUES (' . implode(',', $fieldValues) . ')';
}

function as_db_update($table, $fields, $primaryField, $primaryId)
{
   $updates = array();
   
   foreach ($fields as $key => $value)
   {
      $updates[] = "$key = $value";
   }

   return "UPDATE $table SET " . implode(',', $updates) . " WHERE $primaryField = $primaryId";
}

?>
