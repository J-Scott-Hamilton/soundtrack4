<?php

$json = array('result' => false);

try
{
   require_once("../includes/db.php");

   $db = db_connect();   
   $params = json_decode(file_get_contents('php://input'));
   $tagId = $params->tagId;

   if ($tagId)
   {
      $sql = "SELECT * FROM tag WHERE tag_id = $tagId";
      $rows = mysql_query($sql);
      
      if ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $tag = tagToJson($row);
  
         // List choices
                
         $sql = "SELECT * FROM tag_choice WHERE tag_id = $tagId";
         $choiceRows = mysql_query($sql);
         $choices = array();
         
         if ($choiceRows && (mysql_num_rows($choiceRows) > 0))
         {
            while ($row = mysql_fetch_array($choiceRows, MYSQL_ASSOC))
            {
               $choices[] = tagChoiceToJson($row);
            }
         }
         else
         {
            $choices[] = array('tagChoiceId' => 0);
         }     
         
         $tag['choices'] = $choices;
      
         $json['tag'] = $tag;
         $json['result'] = true;
      }
   }
   else
   {
      $sql = "SELECT * FROM tag ORDER BY name ASC";
      $rows = mysql_query($sql);
      $tags = array();
      
      while ($row = mysql_fetch_array($rows, MYSQL_ASSOC))
      {
         $tag = tagToJson($row);
         $tagId = intval($row['tag_id']);
         
         // List choices
                
         $sql = "SELECT * FROM tag_choice WHERE tag_id = $tagId";
         $choiceRows = mysql_query($sql);
         $choices = array();
         
         if ($choiceRows && (mysql_num_rows($choiceRows) > 0))
         {
            while ($row = mysql_fetch_array($choiceRows, MYSQL_ASSOC))
            {
               $choices[] = tagChoiceToJson($row);
            }
         }
         else
         {
            $choices[] = array('tagChoiceId' => 0);
         }     
               
         $tag['choices'] = $choices;
         $tags[] = $tag;
      }
   
      $json['tags'] = $tags;
      $json['result'] = true;
   }
      
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

function tagToJson($row)
{
   return array('tagId' => intval($row['tag_id']),
                'name' => $row['name']);
}

function tagChoiceToJson($row)
{
   return array('tagChoiceId' => intval($row['tag_choice_id']),
                'name' => $row['name']);
}

?>
