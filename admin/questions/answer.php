<?php

$ROOT = '../..';
$answerId = $_GET['id'];
$questionId = $_GET['questionId'];

include_once("$ROOT/api/includes/common.php");

$ret = api('answer', 'read', array('answerId' => $answerId));
$answer = $ret->answer;

$ret = api('answer-profile', 'read', array('answerId' => $answerId));
$answerProfiles = $ret->profiles;

$profileWeights = array();

foreach ($answerProfiles as $profile)
{
   $profileWeights[$profile->profileId] = $profile->weight;
}

$ret = api('answer-tag', 'read', array('answerId' => $answerId));
$answerTags = $ret->tags;

$tagChoiceWeights = array();

foreach ($answerTags as $tagChoice)
{
   $tagChoiceWeights[$tagChoice->tagId][$tagChoice->tagChoiceId] = $tagChoice->weight;
}

$ret = api('question', 'read', array('questionId' => $questionId));
$question = $ret->question;

$ret = api('profile', 'read');
$profiles = $ret->profiles;

$ret = api('tag', 'read');
$tags = $ret->tags;

include("$ROOT/admin/includes/header.php");

?>

<style>

table.admin td
{
   vertical-align: middle;
}

</style>

<script>

function numbersOnly(event)
{
   var key = window.event ? event.keyCode : event.which;

   if (event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 46 || event.keyCode == 37 || event.keyCode == 39)
      return true;

   if ( key < 48 || key > 57 )
      return false;

   return true;
}

function onProfileWeightBlur(obj, profileId)
{
   var weight = parseInt(obj.value);
   
   if (!weight)
      weight = 0;
      
   var json = {
            answerId: <?php echo $answerId; ?>,
            profileId: profileId,
            weight: weight
         };
         
   $.post('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/api/answer-profile/update'; ?>', 
   {
      json: JSON.stringify(json)
   });
}

function onTagChoiceWeightBlur(obj, tagId, tagChoiceId)
{
   var weight = parseInt(obj.value);
   
   if (!weight)
      weight = 0;
      
   var json = {
            answerId: <?php echo $answerId; ?>,
            tagId: tagId,
            tagChoiceId: tagChoiceId,
            weight: weight
         };
         
   $.post('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/api/answer-tag/update'; ?>', 
   {
      json: JSON.stringify(json)
   });
}

</script>

<?php include("$ROOT/admin/includes/body.php"); ?>

<div id="main">

<a href="/admin">Admin</a> : 
<a href="/admin/questions">Questions</a> : 
<a href="/admin/questions/detail?id=<?php echo $questionId; ?>&questionId=<?php echo $questionId; ?>"><?php echo $question->text; ?></a>

<h1><?php echo $answer->text; ?></h1>

<h2>Profile(s)</h2>

<table class="admin">
<tr>
   <th>Profile</th>
   <th width="120px">Weight (0-100)</th>
</tr>
<?php   
foreach ($profiles as $profile)
{ 
   $profileId = $profile->profileId;
   $profileName = $profile->name;
?>
<tr>
   <!--
   <td align="center">
      <input type="checkbox" id="profile_<?php echo $profileId; ?>" onchange="toggleProfile(<?php echo "$answerId, $profileId"; ?>)" />
   </td-->
   <td><?php echo $profile->name; ?></td>
   <td>
      <input class="weight" type="text" size="10" 
         value="<?php echo $profileWeights[$profile->profileId]; ?>" 
         onkeypress="return numbersOnly(event)"  
         onblur="onProfileWeightBlur(this, <?php echo $profile->profileId; ?>);" />
   </td>
</tr>
<?php } ?>
</table>

<h2>Tags</h2>

<table class="admin">
<tr>
   <th>Tag</th>
   <th>Choice</th>
   <th width="120px">Weight (0-100)</th>
</tr>
<?php 
$lastTagName = null;
foreach ($tags as $tag)
{
   foreach ($tag->choices as $choice)
   {
      $tagId = $tag->tagId;
      $tagChoiceId = $choice->tagChoiceId; 
?>
<tr>
   <td><?php if ($lastTagName != $tag->name) { echo $tag->name; $lastTagName = $tag->name; } ?></td>
   <td>
      <?php echo $choice->name; ?>
      <!--input type="checkbox" id="<?php echo "tag_" . $tagId . "_" . $tagChoiceId; ?>" onchange="toggleTag(<?php echo "$answerId, $tagId, $tagChoiceId"; ?>)" />&nbsp;<?php echo $choice->name; ?>&nbsp;&nbsp;-->
   </td>
   <td>
      <input class="weight" type="text" size="10" 
         value="<?php echo $tagChoiceWeights[$tagId][$tagChoiceId]; ?>" 
         onkeypress="numbersOnly(event)"  
         onblur="onTagChoiceWeightBlur(this, <?php echo "$tagId, $tagChoiceId"; ?>);" />
   </td>   
</tr>
<?php 
   }
}
?>
</table>

<br/>
<hr/>
<br/>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>