<?php

$ROOT = '../..';

include_once("$ROOT/admin/includes/security.php");
include_once("$ROOT/includes/api.php");

if (isset($_GET['delete']))
{
   api('question', 'delete', array('questionId' => $_GET['questionId']));
}

if (isset($_GET['question']))
{
   if (strlen($_GET['question']) > 0)
   {
      $params = array();
      $params['text'] = $_GET['question'];
      $params['questionTypeId'] = $_GET['questionType'];
      $params['comment'] = $_GET['comment'];
      $params['starter'] = ($_GET['starter'] == "on") ? 1 : 0;

      $ret = api('question', 'create', $params);
   }
}

$ret = api('question', 'read');
$questions = $ret->questions;

$ret = api('question-type', 'read');
$questionTypes = $ret->questionTypes;

$questionTypeNames = array();

foreach ($questionTypes as $questionType)
{
   $questionTypeNames[$questionType->questionTypeId] = $questionType->name;
}

include_once("$ROOT/admin/includes/header.php");

?>

<script type="text/javascript" src="/admin/includes/jquery.ajaxfileupload.js"></script>
<script type="text/javascript" src="/admin/includes/si.files.js"></script>

<script type="text/javascript">

$(document).ready(function() 
{ 
   $('.file').ajaxfileupload(
   {
      'action': '/api/question/upload-image',
      'params': {
         'questionId': $(this).attr("name")
      },
      'onComplete': function(response)
      {
         if (!response.result)
            alert(response.reason);
         
         var questionId = parseInt(response.questionId);
         var imageUrl = response.imageUrl;
         
         $('#cabinet_' + questionId).css("background-image", 'url("' + imageUrl + '?' + (new Date()).getTime() + '")');  
      }
   });
});

</script>

<style type="text/css" title="text/css">
/* <![CDATA[ */

td.separator
{
   background-color: #eee;
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

<?php include_once("$ROOT/admin/includes/body.php"); ?>

<div id="main">

<a href="/admin">Admin</a>

<h1>Questions</h1>

<form>

<table class="admin">
<tr>
   <th width="30px"></th>
   <th>Question</th>
   <th>Type</th>
   <th>Image</th>
   <th>Starter</th>
   <th>Comment</th>
</tr>

<?php 

if ($questions)
{
   foreach ($questions as $question)
   {
?>

<tr>
   <td width="30px" align="center">
      <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
      <a href="?questionId=<?php echo $question->questionId; ?>&delete=1"><img src="/admin/images/redx.jpg" width="24px" height="24px"></a>
      <?php } ?>
   </td>
   <td style="vertical-align:middle"><a href="./detail?id=<?php echo $question->questionId; ?>"><?php echo $question->text; ?></a></td>
   <td style="vertical-align:middle"><?php echo $questionTypeNames[$question->questionTypeId]; ?></td>
   <td style="text-align:center;vertical-align:middle;width:64px;">
      <?php $image = ($question->imageUrl) ? ($question->imageUrl . '?' . time()) : "/admin/images/noimage.jpg"; ?>
   	<label class="cabinet" id="cabinet_<?php echo $question->questionId; ?>" style="background-size:100%;background-repeat:no-repeat;background-position:center;background-image:url('<?php echo $image; ?>');"> 
      <?php if ($_SESSION['ADMINUSER'] == "admin") { ?>
         <input type="file" class="file" name="image_<?php echo $question->questionId; ?>" />
      <?php } ?>
   	</label>
   </td>
   <td style="text-align:center;vertical-align:middle"><?php if ($question->starter) { echo "<img src=\"/admin/images/checkmark.jpg\" />"; } ?></td>
   <td style="vertical-align:middle"><?php echo $question->comment; ?></td>
</tr>

<?php 
   }
}
?>

</table>

<br/>
<hr/>
<br/>

<?php if ($_SESSION['ADMINUSER'] == "admin") { ?>

<table>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="question">Question:</label></td>
   <td width="100%"><input name="question" size="50" /></td>
</tr>
</tr>
   <td style="text-align:right;vertical-align:middle;"><label style="white-space:nowrap;" for="type">Question Type:</label></td>
   <td style="width:100%">
      <select name="questionType">
      <?php 
      foreach ($questionTypes as $questionType) 
      {
         echo '<option value="' . $questionType->questionTypeId . '">' . $questionType->name . '</option>';
      }
      ?>
      </select>
   </td>
</tr>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="comment">Comment:</label></td>
   <td width="100%"><input name="comment" size="50" /></td>
</tr>
<tr>
   <td style="text-align:center;vertical-align:middle"><label for="starter">Starter:</label></td>
   <td><input name="starter" type="checkbox" /><br/></td>
</tr>
<tr>
   <td></td>
   <td><input type="submit" name="add" value="Add Question" /></td>
</tr>
</table>
</form>

<?php } ?>

<script type="text/javascript" language="javascript">
// <![CDATA[
SI.Files.stylizeAll();
// ]]>
</script>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>