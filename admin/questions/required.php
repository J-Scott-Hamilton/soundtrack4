<?php

$ROOT = '../..';

include_once("$ROOT/api/includes/common.php");

$ret = api('question', 'read');
$questions = $ret->questions;

$ret = api('profile-demographics', 'overlap');
$overlaps = $ret->overlaps;

include_once("$ROOT/admin/includes/header.php");
include_once("$ROOT/admin/includes/body.php");

?>

<div id="main">

<a href="/admin">Admin</a>

<h1>Required Questions</h1>

<p>Below are the combinations of profiles that are possible given our current set of age/gender/location questions. We need at least one question for each combination that can "break the tie".</p>

<table class="admin">
<tr>
   <th>Profiles</th>
   <th>Question</th>
</tr>

<?php 

if ($overlaps)
{
   foreach ($overlaps as $overlap)
   {
      echo "<tr><td>";

      foreach ($overlap->profiles as $profile) 
      {
         echo "<a href=\"$SITEROOT/admin/profiles/detail?profileId=$profile->profileId\">$profile->name</a><br/>";  
      }
         
      echo "</td>";
      echo "<td>";
      
      foreach ($overlap->questions as $question) 
      {
         echo "<a href=\"$SITEROOT/admin/rquestions/detail?id=$question->questionId\">$question->text</a><br/>";
      }
      
      echo "</td>";
      echo "</tr>";
   }
}

?>

</table>

<br/>
<hr/>
<br/>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>