<?php

$ROOT = '../..';
include_once("$ROOT/api/includes/common.php");

$profileId = $_GET['profileId'];

if (isset($_GET['delete']))
{
   if (isset($_GET['soundtrackId']))
   {
      api('soundtrack', 'delete', array('soundtrackId' => $soundtrackId));
   }
   else
   {
      api('profile', 'delete', array('profileId' => $profileId));
      header("Location: http://" . $_SERVER['HTTP_HOST'] . '/admin/profiles');
      exit();
   }
}

/*
if (isset($_GET['soundtrackActivity']))
{
   $activityId = $_GET['soundtrackActivity'];
   $playlistId = $_GET['soundtrackPlaylist'];
   $daysliceId = $_GET['soundtrackDayslice'];

   if ($profileId && $activityId && $playlistId && $daysliceId)
   {
      $params = array();
      $params['profileId'] = $profileId;
      $params['activityId'] = $activityId;
      $params['daysliceId'] = $daysliceId;
      $params['playlistId'] = $playlistId;
   
      api('soundtrack', 'create', $params);
   }   
}
*/

$ret = api('profile', 'read', array('profileId' => $profileId));
$profile = $ret->profile;

$ret = api('age-cohort', 'read');
$ageCohorts = $ret->ageCohorts;

$ret = api('location-cohort', 'read');
$locationCohorts = $ret->locationCohorts;

$ret = api('gender-cohort', 'read');
$genderCohorts = $ret->genderCohorts;

$ret = api('profile-demographics', 'read', array('profileId' => $profileId));
$demographics = $ret->demographics;

//$ret = api('dayslice', 'read');
//$dayslices = $ret->dayslices;

//$ret = api('activity', 'read');
//$activities = $ret->activities;

//$ret = api('playlist', 'read');
//$playlists = $ret->playlists;

//$ret = api('profile-activity-dayslice', 'read', array('profileId' => $profileId));
//$profileActivityDayslices = $ret->results;

$ret = api('soundtrack', 'read', array('profileId' => $profileId));
$soundtracks = $ret->soundtracks;

include_once("$ROOT/admin/includes/header.php");

?>

<style>

table.profile-map td
{
   vertical-align: middle;
   padding: 0px;
   margin: 0px;
}

table.activity-map td
{
   border: 1px solid black;
   vertical-align: middle;
   padding: 0px;
   margin: 0px;
}

textarea
{
   padding: 4px;
   border: 1px solid white;
   width: 100%;
   font-family: inherit;
   font-size: 18px;
   resize: none;
}

textarea:focus
{
   padding: 4px;
   border: 1px solid #3D77AD;
   resize: none;
}

</style>

<script>

function toggleAge(profileId, ageCohortId)
{
   // Update tables
   
   var json = {
            profileId: profileId,
            ageCohortId: ageCohortId
         };
         
   var isChecked = $('#age_' + ageCohortId).is(':checked');
   var action = (isChecked) ? 'create' : 'delete';

   $.post('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/api/profile-demographics/'; ?>' + action, 
      {
         json: JSON.stringify(json)
      },
      function(data)
      {
         // Check/Uncheck gender boxes
         // Check/Uncheck location boxes
      }
   );
}

function toggleGender(profileId, ageCohortId, genderCohortId)
{
   // Update tables
   
   var json = {
            profileId: profileId,
            ageCohortId: ageCohortId,
            genderCohortId: genderCohortId
         };
         
   var isChecked = $('#gender_' + ageCohortId + '_' + genderCohortId).is(':checked');
   var action = (isChecked) ? 'create' : 'delete';

   $.post('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/api/profile-demographics/'; ?>' + action, 
      {
         json: JSON.stringify(json)
      },
      function(data)
      {
         // Check/Uncheck location boxes
         
         
      }
   );
}

function toggleLocation(profileId, ageCohortId, genderCohortId, locationCohortId)
{
   // Update tables
   
   var json = {
            profileId: profileId,
            ageCohortId: ageCohortId,
            genderCohortId: genderCohortId,
            locationCohortId: locationCohortId
         };
      
   var isChecked = $('#location_' + ageCohortId + '_' + genderCohortId + '_' + locationCohortId).is(':checked');
   var action = (isChecked) ? 'create' : 'delete';

   $.post('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/api/profile-demographics/'; ?>' + action, 
      {
         json: JSON.stringify(json)
      },
      function(data)
      {
         // Nothing to do
      }
   );
}

/*
function toggleActivity(profileId, activityId, daysliceId)
{
   // Update tables
   
   var json = {
            profileId: profileId,
            activityId: activityId,
            daysliceId: daysliceId
         };
      
   var isChecked = $('#activity_' + activityId + '_' + daysliceId).is(':checked');
   var action = (isChecked) ? 'create' : 'delete';

   $.post('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/api/profile-activity-dayslice/'; ?>' + action, 
      {
         json: JSON.stringify(json)
      },
      function(data)
      {
         // Nothing to do
      }
   );
}
*/

function updateDescription()
{
   var desc = $('#desc').val();
   var json = {
            profileId: <?php echo $profileId; ?>,
            description: desc
         };
         
   $.post('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/api/profile/update'; ?>', 
      {
         json: JSON.stringify(json)
      },
      function(data)
      {
         $('#desc-update-button').hide();
      }
   );
}

function onDescriptionChanged()
{
   $('#desc-update-button').show();
}

function updateShortDescription()
{
   var desc = $('#short-desc').val();
   var json = {
            profileId: <?php echo $profileId; ?>,
            shortDescription: desc
         };
         
   $.post('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/api/profile/update'; ?>', 
      {
         json: JSON.stringify(json)
      },
      function(data)
      {
         $('#short-desc-update-button').hide();
      }
   );
}

function onShortDescriptionChanged()
{
   $('#short-desc-update-button').show();
}

$(document).ready(function()
{
   $('#desc-update-button').hide();
   $('#short-desc-update-button').hide();
      
   // If description empty, start with focus there

   var desc = $('#desc').val();

   if (desc.length == 0)
   {
      $('#desc').focus();
      $('#desc-update-button').show();
   }
   
   // Check the boxes that should be checked 

   var age;
   var gender;
   var location;
   
   <?php foreach ($demographics as $demographic) { ?>
   
      age = <?php echo $demographic->ageCohortId; ?>;
      gender = <?php echo $demographic->genderCohortId; ?>;
      location = <?php echo $demographic->locationCohortId; ?>;

      $('#location_' + age + '_' + gender + '_' + location).prop("checked", true);
      
   <?php } ?>

   /*
   var activity;
   var dayslice;
   
   <?php foreach ($profileActivityDayslices as $pad) { ?>
   
      activity = <?php echo $pad->activityId; ?>;
      dayslice = <?php echo $pad->daysliceId; ?>;

      $('#activity_' + activity + '_' + dayslice).prop("checked", true);
      
   <?php } ?>
   */
   
});

</script>

<?php include_once("$ROOT/admin/includes/body.php"); ?>

<div id="main">

<a href="/admin">Admin</a> : 
<a href="/admin/profiles">Profiles</a>

<h1><?php echo $profile->name; ?></h1>

<h3>Description</h3>
<textarea rows="4" name="desc" id="desc" onkeyup="onDescriptionChanged();"><?php echo $profile->description; ?></textarea><br/>
<br/>
<div width="100%" align="right">
   <input type="button" id="desc-update-button" value="Update Description" onclick="updateDescription();" />
</div>

<h3>Short Description</h3>
<textarea rows="4" name="short-desc" id="short-desc" onkeyup="onShortDescriptionChanged();"><?php echo $profile->shortDescription; ?></textarea><br/>
<br/>
<div width="100%" align="right">
   <input type="button" id="short-desc-update-button" value="Update Short Description" onclick="updateShortDescription();" />
</div>


<br/>
<hr/>

<h3>Demographics</h3>

<div id="demographics" style="display:block">
<table class="profile-map">
<?php for ($i = 0; $i < count($ageCohorts); $i++) { $ageCohort = $ageCohorts[$i]; ?>
   <tr>
      <td>
         <!--input type="checkbox" id="age_<?php echo $ageCohort->ageCohortId; ?>" onchange="toggleAge(<?php echo "$profileId, $ageCohort->ageCohortId" ?>)" />&nbsp;-->
         <?php echo $ageCohort->name; ?>
      </td>
      <td>
         <table class="profile-map">
         <?php for ($j = 0; $j < count($genderCohorts); $j++) { $genderCohort = $genderCohorts[$j]; ?>
            <tr>
               <td>
                  <!--input type="checkbox" 
                     id="gender_<?php echo $ageCohort->ageCohortId . '_' . $genderCohort->genderCohortId; ?>"
                     onchange="toggleGender(<?php echo "$profileId, $ageCohort->ageCohortId, $genderCohort->genderCohortId" ?>)" />&nbsp;-->
                  <?php echo $genderCohort->name; ?>
               </td>
               <td>
                  <table class="profile-map">
                  <?php for ($k = 0; $k < count($locationCohorts); $k++) { $locationCohort = $locationCohorts[$k]; ?>
                     <tr>
                        <td>
                           <input type="checkbox" 
                              id="location_<?php echo $ageCohort->ageCohortId . '_' . $genderCohort->genderCohortId . '_' . $locationCohort->locationCohortId; ?>"
                              onchange="toggleLocation(<?php echo "$profileId, $ageCohort->ageCohortId, $genderCohort->genderCohortId, $locationCohort->locationCohortId" ?>)" />&nbsp;
                           <?php echo $locationCohort->name; ?>
                        </td>
                     </tr>
                     <?php if ($k < (count($locationCohorts) - 1)) { ?>
                     <tr><td><hr/></td></tr>
                     <?php } ?>
                  <?php } ?>
                  </table>
               </td>
            </tr>
            <?php if ($j < (count($genderCohorts) - 1)) { ?>
            <tr><td colspan="2"><hr/></td></tr>
            <?php } ?>
         <?php } ?>
         </table>
      </td>
   </tr>
   <?php if ($i < (count($ageCohorts) - 1)) { ?>
   <tr><td colspan="2"><hr/></td></tr>
   <?php } ?>
<?php } ?>
</table>
</div>

<hr/>

<!--
<h3>Activities</h3>

<table class="admin">
   <tr>
      <th width="100%">Activity</th>
      <?php foreach ($dayslices as $dayslice) { ?>
      <th nowrap="nowrap"><?php echo $dayslice->name; ?></th>
      <?php } ?>      
   </tr>
   
<?php for ($i = 0; $i < count($activities); $i++) { $activity = $activities[$i]; ?>
   <tr>
      <td style="vertical-align:middle"><?php echo $activity->name; ?></td>
      <?php foreach ($dayslices as $dayslice) { ?>
      <td width="30px" align="center">
      
         <input type="checkbox" 
            id="activity_<?php echo $activity->activityId . '_' . $dayslice->daysliceId; ?>"
            onchange="toggleActivity(<?php echo "$profileId, $activity->activityId, $dayslice->daysliceId"; ?>)" />
      
      </td>
      <?php } ?>      
   </tr>
<?php } ?>

</table>
-->

<h3>Soundtracks</h3>

<table class="admin">
<tr>
   <th></th>
   <th>Dayslice</th>
   <th>Activity</th>
   <th></th>
</tr>
<?php foreach ($soundtracks as $soundtrack) { ?>
<tr>
   <td width="30px" align="center"><a href="?profileId=<?php echo $profileId; ?>&soundtrackId=<?php echo $soundtrack->soundtrackId; ?>&delete=1"><img src="/admin/images/redx.jpg" width="24px" height="24px"></a></td>
   <td><?php echo $soundtrack->daysliceName; ?></td>
   <td><?php echo $soundtrack->activityName; ?></td>
   <td><a href="/admin/soundtracks/detail?id=<?php echo $soundtrack->soundtrackId; ?>">Edit</a></td>
</tr>
<?php } ?>
</table>

<br/>

<!--
<form>
<table>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="soundtrackDayslice">Dayslice:</label></td>
   <td width="100%">
      <select name="soundtrackDayslice">
         <option value="">Select</option>
      <?php foreach ($dayslices as $dayslice) { ?>
         <option value="<?php echo $dayslice->daysliceId; ?>"><?php echo $dayslice->name; ?></option>
      <?php } ?>
      </select>
   </td>
</tr>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="soundtrackActivity">Activity:</label></td>
   <td>
      <select name="soundtrackActivity">
         <option value="">Select</option>
      <?php foreach ($activities as $activity) { ?>
         <option value="<?php echo $activity->activityId; ?>"><?php echo $activity->name; ?></option>
      <?php } ?>
      </select>
   </td>
</tr>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="soundtrackPlaylist">Playlist:</label></td>
   <td>
      <select name="soundtrackPlaylist">
         <option value="">Select</option>
      <?php foreach ($playlists as $playlist) { ?>
         <option value="<?php echo $playlist->playlistId; ?>"><?php echo $playlist->name; ?></option>
      <?php } ?>
      </select>
   </td>
</tr>
<tr>
   <td></td>
   <td><input type="submit" name="add" value="Add Soundtrack" /></td>
</tr>
</table>
<input type="hidden" name="profileId" value="<?php echo $profileId; ?>" />
</form>
-->

<br/>
<hr/>
<br/>

<a href="?profileId=<?php echo $profileId; ?>&delete=1">Delete this Profile</a>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>