<?php

$ROOT = '../..';

include_once("$ROOT/api/includes/common.php");

$ret = api('age-cohort', 'read');
$ageCohorts = $ret->ageCohorts;

$ret = api('location-cohort', 'read');
$locationCohorts = $ret->locationCohorts;

$ret = api('gender-cohort', 'read');
$genderCohorts = $ret->genderCohorts;

$ret = api('profile-demographics', 'read');
$demographics = $ret->demographics;

$ret = api('profile', 'read');
$profiles = $ret->profiles;

$profileNames = array();

foreach ($profiles as $profile)
{
   $profileNames[$profile->profileId] = $profile->name;
}

include_once("$ROOT/admin/includes/header.php");
include_once("$ROOT/admin/includes/body.php");

?>

<div id="main">

<a href="/admin">Admin</a>

<h1>Orphaned Demographics</h1>

<p>The highlighted rows below are the combinations of demographics that aren't covered by any of the existing profiles.</p>

<table class="admin">
<tr>
   <th>Age</th>
   <th>Gender</th>
   <th>Location</th>
   <th></th>
</tr>

<?php 

foreach ($ageCohorts as $age)
{
   $ageCohortId = $age->ageCohortId;
   
   foreach ($genderCohorts as $gender) 
   {
      $genderCohortId = $gender->genderCohortId;
      
      foreach ($locationCohorts as $location)
      {
         $locationCohortId = $location->locationCohortId;
         $found = false;
         $profiles = "";
         
         foreach ($demographics as $demographic) 
         {
            if (($demographic->ageCohortId == $ageCohortId) &&
                ($demographic->genderCohortId == $genderCohortId) &&
                ($demographic->locationCohortId == $locationCohortId))
            {
               $profiles .= "<a href=\"$SITEROOT/admin/profiles/detail?profileId=$demographic->profileId\">" . $profileNames[$demographic->profileId] . "</a><br>";
               $found = true;
            }
         }
         
         $bkgndColor = $found ? "#ffffff" : "#ffffbb";
         
         echo "<tr style=\"background-color:$bkgndColor;\"><td>$age->name</td><td>$gender->name</td><td>$location->name</td><td>$profiles</td></tr>";
      }
   }
}

?>

</table>

<br/>
<hr/>
<br/>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>