<?php

$ROOT = '../..';

include_once("$ROOT/admin/includes/security.php");
include_once("$ROOT/api/includes/common.php");

if (isset($_GET['delete']))
{
   api('song', 'delete', array('songId' => $_GET['songId']));
}

$offset = 0;

$ret = api('song', 'read', array('offset' => $offset, 'pageSize' => 20));
$songs = $ret->songs;

include_once("$ROOT/admin/includes/header.php");
include_once("$ROOT/admin/includes/body.php");

?>

<div id="main">

<a href="/admin">Admin</a>

<h1>Songs</h1>

<table class="admin">
<tr>
   <th>Name</th>
   <th>Artist</th>
   <th>Album</th>
   <th>Spotify</th>
   <th>Rdio</th>
</tr>

<?php foreach ($songs as $song) { ?>

<tr>
   <td style="vertical-align:middle"><a href="./detail?id=<?php echo $song->songId; ?>"><?php echo $song->name; ?></a></td>
   <td style="vertical-align:middle"><?php echo $song->artist; ?></td>
   <td style="vertical-align:middle"><?php echo $song->album; ?></td>
   <td style="vertical-align:middle"><?php if ($song->spotify) { echo '<a target="_blank" href="http://open.spotify.com/track/' . $song->spotify . '">' . $song->spotify . '</a>'; } ?></td>
   <td style="vertical-align:middle"><?php if ($song->rdio) { echo '<a target="_blank" href="http://rd.io/x/' . $song->rdio . '">' . $song->rdio . '</a>'; } ?></td>
</tr>

<?php } ?>

<tr>
   <td colspan="6" style="text-align:right;">
      <a href="">Next ></a>
   </td>
</tr>

</table>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>