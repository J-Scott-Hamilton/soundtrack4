<?php

$ROOT = '../..';
$questionId = $_GET['id'];

include_once("$ROOT/admin/includes/security.php");
include_once("$ROOT/api/includes/common.php");

if (isset($_GET['delete']))
{
   if (!isset($_GET['answerId']))
   {
      api('question', 'delete', array('questionId' => $questionId));
      header("Location: http://" . $_SERVER['HTTP_HOST'] . '/admin/questions');
      exit();
   }
   else
   {
      api('answer', 'delete', array('answerId' => $_GET['answerId']));
   }
}
else if (isset($_GET['add']))
{
   $params = array();
   $params['questionId'] = $questionId;
   $params['text'] = $_GET['answer'];
   
   $ret = api('answer', 'create', $params);
}
else if (isset($_GET['update']))
{
   $params = array();
   $params['questionId'] = $questionId;
   $params['text'] = $_GET['question'];
   $params['comment'] = $_GET['comment'];
   $params['tooltip'] = $_GET['tooltip'];
   $params['starter'] = ($_GET['starter'] == "on") ? 1 : 0;
   
   $ret = api('question', 'update', $params);
}

echo $questionId;

$ret = api('question', 'read', array('questionId' => $questionId));
$question = $ret->question;

$ret = api('answer', 'read', array('questionId' => $questionId));
$answers = $ret->answers;

$ret = api('question-profile', 'read', array('questionId' => $questionId));
$questionProfiles = $ret->profiles;

include("$ROOT/admin/includes/header.php");

?>

<script type="text/javascript" src="/admin/includes/jquery.ajaxfileupload.js"></script>
<script type="text/javascript" src="/admin/includes/si.files.js"></script>

<script type="text/javascript">

$(document).ready(function() 
{ 
   $('.file').ajaxfileupload(
   {
      'action': '/api/answer/upload-image',
      'params': {
         'answerId': $(this).attr("name")
      },
      'onComplete': function(response)
      {
         if (!response.result)
            alert(response.reason);
         
         var answerId = parseInt(response.answerId);
         var imageUrl = response.imageUrl;
         
         $('#cabinet_' + answerId).css("background-image", 'url("' + imageUrl + '?' + (new Date()).getTime() + '")');  
      }
   });
});

function toggleProfile(profileId)
{
   var json = {
            questionId: <?php echo $questionId; ?>,
            profileId: profileId
         };
         
   var isChecked = $('#profile_' + profileId).is(':checked');
   var action = (isChecked) ? 'create' : 'delete';

   $.post('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/api/question-profile/'; ?>' + action, 
   {
      json: JSON.stringify(json)
   });
}

function onTooltipBlur(obj, answerId)
{
   var tooltip = obj.value;
   
   var json = {
            answerId: answerId,
            tooltip: tooltip
         };
         
   $.post('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/api/answer/update'; ?>', 
   {
      json: JSON.stringify(json)
   });
}

</script>

<style type="text/css" title="text/css">
/* <![CDATA[ */

td.separator
{
   background-color: #eee;
}

table.admin td
{
   vertical-align: middle;
}

.SI-FILES-STYLIZED label.cabinet
{
	width: 64px;
	height: 64px;
	background-size: cover;
	display: block;
	overflow: hidden;
	cursor: pointer;
}

.SI-FILES-STYLIZED label.cabinet input.file
{
	position: relative;
	height: 100%;
	width: auto;
	opacity: 0;
	-moz-opacity: 0;
	filter:progid:DXImageTransform.Microsoft.Alpha(opacity=0);
}

/* ]]> */
</style>

<?php include("$ROOT/admin/includes/body.php"); ?>

<div id="main">

<a href="/admin">Admin</a> : 
<a href="/admin/questions">Questions</a>

<h1><?php echo $question->text; ?></h1>

<form>
<table>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="question">Question:</label></td>
   <td width="100%"><input name="question" size="50" value="<?php echo htmlentities($question->text, ENT_QUOTES); ?>" /><br/></td>
</tr>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="comment">Comment:</label></td>
   <td><input name="comment" size="50" value="<?php echo htmlentities($question->comment, ENT_QUOTES); ?>" /><br/></td>
</tr>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="tootip">Tooltip:</label></td>
   <td><input name="tooltip" size="50" value="<?php echo htmlentities($question->tooltip, ENT_QUOTES); ?>" /><br/></td>
</tr>
<tr>
   <td style="text-align:center;vertical-align:middle"><label for="starter">Starter:</label></td>
   <td><input name="starter" type="checkbox" <?php if ($question->starter) { echo "checked=\"checked\""; } ?> /><br/></td>
</tr>
<?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
<tr>
   <td></td>
   <td><input type="submit" name="update" value="Update Question" /></td>
</tr>
<?php } ?>
</table>
<input type="hidden" name="id" value="<?php echo $questionId; ?>" /><br/>
</form>

<hr/>

<h2>Answers</h2>

<table class="admin">
<tr>
   <th></th>
   <th>Answer</th>
   <th>Image</th>
   <th>Tooltip</th>
   <th>Implications</th>
</tr>

<?php 
if ($answers)
{
   foreach ($answers as $answer)
   {
?>

<tr>
   <td width="30px" align="center">
      <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
      <a href="?id=<?php echo $questionId; ?>&answerId=<?php echo $answer->answerId; ?>&delete=1"><img src="/admin/images/redx.jpg" width="24px" height="24px"></a>
      <?php } ?>
   </td>
   <td style="vertical-align:middle">
      <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
      <a href="./answer?id=<?php echo $answer->answerId; ?>&questionId=<?php echo $questionId; ?>">
      <?php } ?>
         <?php echo $answer->text; ?>
      <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
      </a>
      <?php } ?>
   </td>
   <td style="text-align:center;vertical-align:middle;width:64px;">
      <?php $image = ($answer->imageUrl) ? ($answer->imageUrl . '?' . time()) : "$SITEROOT/admin/images/noimage.jpg"; ?>
   	<label class="cabinet" id="cabinet_<?php echo $answer->answerId; ?>" style="background-size:100%;background-repeat:no-repeat;background-position:center;background-image:url('<?php echo $image; ?>');"> 
	  	  <input type="file" class="file" name="image_<?php echo $answer->answerId; ?>" />
   	</label>
   </td>
   <td style="vertical-align:middle">
      <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
      <input id="tooltip_<?php echo $answer->answerId; ?>" 
             value="<?php echo $answer->tooltip; ?>"
             onblur="onTooltipBlur(this, <?php echo $answer->answerId; ?>);" />
      <?php } ?>
   </td>
   <td>
      <?php
      
      $implications = array();
      
      if (count($answer->profiles) > 0)
      {
         $profiles = array();
         
         foreach ($answer->profiles as $profile)
         {
            $profiles[] = $profile->name;
         }
         
         $implications[] = "Profile = " . implode(", ", $profiles);
      }

      if (count($answer->tags) > 0)
      {
         $tags = array();
         
         foreach ($answer->tags as $tag)
         {
            $value = ($tag->tagChoiceId == 0) ? "TRUE" : $tag->tagChoiceName;
            $implications[] = "$tag->tagName = " . $value;
         }
      }

      echo implode("<br>", $implications);
               
      ?>
   </td>
</tr>

<?php 
   }
}
?>

</table>

<br/><br/>
<hr/>

<?php if ($_SESSION['ADMINUSER'] == "admin") { ?>

<form>
<table>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="answer">Answer:</label></td>
   <td><input id="answer" name="answer" size="50" /></td>
</tr>
<tr>
   <td></td>
   <td><input type="submit" name="add" value="Add Answer" /></td>
</tr>
</table>
<input type="hidden" name="id" value="<?php echo $questionId; ?>" /><br/>
</form>

<br/><br/>
<hr/>

<?php 
$ret = api('profile', 'read', null);
$profiles = $ret->profiles;
?>

<h2>Profiles</h2>
<span style="color:red;font-weight:bold">DON'T</span>&nbsp;<span>ask this question for these profiles...</span>
<br/><br/>
<table class="admin">
<tr>
   <th></th>
   <th>Profile</th>
</tr>
<?php 
foreach ($profiles as $profile)
{
?>
<tr>
   <td width="30px" align="center">
      <input type="checkbox" <?php echo (in_array($profile->profileId, $questionProfiles)) ? "checked" : ""; ?> 
         id="profile_<?php echo $profile->profileId; ?>" 
         onchange="toggleProfile(<?php echo $profile->profileId; ?>)" />
   </td>
   <td style="vertical-align:middle"><?php echo $profile->name; ?></td>
</tr>
<?php 
}
?>

</table>

<?php } ?>

<br/><br/>
<hr/>

<script type="text/javascript" language="javascript">
// <![CDATA[
SI.Files.stylizeAll();
// ]]>
</script>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>
