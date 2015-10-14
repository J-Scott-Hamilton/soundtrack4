<?php

$ROOT = '../..';

include_once("$ROOT/api/includes/common.php");

$songs = array();

if (isset($_GET['queue']))
{
   $parts = explode(':', $_GET['queue']);
   
   $accountId = $parts[0];
   $soundtrackId = $parts[1];
}

$ret = api('account-queue', 'read');
$queues = $ret->queues;

if ($accountId)
{
   $ret = api('account-queue', 'read', array('accountId' => $accountId, 'soundtrackId' => $soundtrackId));
   $queueSongs = $ret->songs;

   $ret = api('account-song', 'read', array('accountId' => $accountId));
   $heardSongs = $ret->songs;
}

include_once("$ROOT/admin/includes/header.php");

?>

<?php include_once("$ROOT/admin/includes/body.php"); ?>

<div id="main">

<a href="/admin">Admin</a>

<h1>Song Duplication</h1>
<p>The following songs appear in two or more playlists that are part of the same soundtrack.</p>

<br/>
<br/>
<hr/>
<br/>



</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>