<?php

$ROOT = '../..';
$questionId = $_GET['question'];

include_once("$ROOT/api/includes/common.php");

$ret = api('question', 'read', array('questionId' => $questionId));
$question = $ret->question;

include("$ROOT/admin/includes/header.php");
include("$ROOT/admin/includes/body.php");

$h1 = $question->text;

if ($question->comment)
{
   $h1 .= ' (' . $question->comment . ')';
}

?>

<div id="main">

<h1><a href="/admin/questions">Questions</a> : <?php echo $h1; ?></h1>


</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>