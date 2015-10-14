<?php

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();

   $fields = array();

   //Dig a Little Deeper, Peter Bjorn And John, Gimme Some
   //Fortunate Son, Creedence Clearwater Revival, Chronicle: 20 Greatest Hits (24-Karat Gold Disc)
   //Far Nearer, Jamie xx, Far Nearer / Beat For - EP
   //Holocene, Bon Iver, Bon Iver
   //Kaputt, Destroyer, Kaputt
   //It's Real, Real Estate, Days
   //I Wanna Be Adored, The Stone Roses, The Stone Roses
   //Amor Fati, Washed Out, Within and Without
   //Lorelai, Fleet Foxes, Helplessness Blues
   //There Is a Light That Never Goes Out, Dum Dum Girls, He Gets Me High
   //Your Love Is Calling My Name, The War On Drugs, Slave Ambient
   //Someday Man, Paul Williams, Someday Man
   //Ageless Beauty, Stars, Set Yourself On Fire

   $fields['name'] = as_db_string($params->name);
   $fields['artist'] = as_db_string($params->artist);
   $fields['album'] = as_db_string($params->album);
   $fields['spotify'] = as_db_string($params->spotify);
   $fields['rdio'] = as_db_string($params->rdio);

   $sql = as_db_insert("song", $fields);
   
   $rows = mysql_query($sql);
   $id = mysql_insert_id();

   
   db_close($db);
}
catch (Exception $e)
{
   db_close($db);
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

?>
