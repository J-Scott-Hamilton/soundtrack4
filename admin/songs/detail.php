<?php

$ROOT = '../..';
$songId = $_GET['id'];

include_once("$ROOT/api/includes/common.php");

$ret = api('song', 'read', array('songId' => $songId));
$song = $ret->song;

$ret = api('playlist', 'read', array('songId' => $songId));
$playlists = $ret->playlists;

include("$ROOT/admin/includes/header.php");
?>

<script type="text/javascript">

function excludeSong()
{
   var json = {
            songId: <?php echo $songId; ?>
         };
         
   $.post('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/api/song/exclude'; ?>', 
   {
      json: JSON.stringify(json)
   },
   function(data)
   {
      alert('Song excluded');
   });
}

</script>

<?php include("$ROOT/admin/includes/body.php"); ?>

<div id="main">

<a href="/admin">Admin</a> : 
<a href="/admin/songs">Songs</a>

<h1><?php echo $song->name; ?></h1>

<a href="javascript:excludeSong()">Exclude this song from Soundtrack4</a>

<p>This song appears in the following playlists...</p>

<table class="admin">
<tr>
   <th>Name</th>
   <th>Rdio</th>
</tr>

<?php

if (count($playlists) > 0)
{
   foreach ($playlists as $playlist)
   {
?>

<tr>
   <td style="vertical-align:middle">
      <a href="./detail?playlistId=<?php echo $playlist->playlistId; ?>">
      <?php echo $playlist->name; ?>
      </a>
      <?php if ($playlist->comment) { echo "<br/>" . $playlist->comment; } ?>
   </td>
   <td style="vertical-align:middle">
      <?php if ($playlist->rdioUrl) { ?>
      <a href="<?php echo $playlist->rdioUrl; ?>">Open</a>
      <?php } ?>
   </td>
</tr>

<?php } ?>
<?php } ?>

</table>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>