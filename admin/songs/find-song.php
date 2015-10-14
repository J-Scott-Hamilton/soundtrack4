
<?php 

$ROOT = '../..';

require_once("$ROOT/api/includes/common.php");

if (isset($_GET['search']))
{
   $params = array();
   $params['search'] = $_GET['q'];
   
   $ret = api('song', 'read', $params);
   $songs = $ret->songs;
}

?>

<?php include("$ROOT/admin/includes/header.php"); ?>

<?php include("$ROOT/admin/includes/body.php"); ?>

<div id="main">

<h2>Song Lookup...</h2>

<form>
<input name="q" size="50" />
<input type="submit" name="search" value="Search" />
</form>

<br/>

<?php 

if ($songs)
{
   echo '<div id="results">';
   echo '<table class="admin">';
   echo '<tr><th>Name</th><th>Artist</th><th>Album</th></tr>';

   foreach ($songs as $s)
   {
      echo '<tr>';
      echo '<td><a href="/admin/songs/detail?id=' . $s->songId . '">' . $s->name . '</a></td><td>' . $s->artist . '</td><td>' . $s->album . '</td>';
      echo '</tr>';
   }

   echo '</table>';
   echo '</div>';
}

?>


<?php include("$ROOT/admin/includes/footer.php"); ?>
