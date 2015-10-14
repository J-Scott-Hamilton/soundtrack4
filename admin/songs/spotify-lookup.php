
<?php 

$ROOT = '../..';

//require_once("$ROOT/includes/rdio/oauth.php");

require_once("$ROOT/api/includes/common.php");

if (isset($_GET['key']))
{
   $params = array();
   $params['name'] = $_GET['track'];
   $params['artist'] = $_GET['artist'];
   $params['album'] = $_GET['album'];
   $params['spotify'] = $_GET['key'];
   
   $ret = api('song', 'create', $params);
   
   echo json_encode($ret);
    
   $songAdded = ($ret->result) ? $_GET['key'] : null;
}

$q = urlencode(utf8_encode($_GET['q']));

if ($q)
{
   $results = json_decode(file_get_contents("http://ws.spotify.com/search/1/track.json?q=$q"));

   //echo "ret = $ret";

   //$params = array();
   //$params['query'] = $q;

   //$params['types'] = 'track';
   //$search = $rdio->call('search', $params);
}

?>

<?php include("$ROOT/admin/includes/header.php"); ?>

<style>

#results table 
{
   border: 1px solid black;
   border-collapse: collapse;
   border-spacing: 0px;
}

#results th {
   background-color: #f8f8f8;
}

#results td, th {
   border: 1px solid black;
   padding: 10px;
}

</style>

<?php include("$ROOT/admin/includes/body.php"); ?>

<div id="main">

<h2>Spotify Lookup...</h2>

<form>
<input name="q" size="50" />
<input type="submit" name="search" value="Search" />
</form>

<br/>

<?php 

if ($results)
{
   echo '<div id="results">';
   echo '<table>';
   echo '<tr><th>Key</th><th>Track</th><th>Artist</th><th>Album</th><th></th></tr>';

   foreach ($results->tracks as $t)
   {
      echo '<tr>';
      echo '<td>' . $t->href . '</td><td>' . $t->name . '</td><td>' . $t->artists[0]->name . '</td><td>' . $t->album->name . '</td>';
      
      if ($songAdded == $t->href)
      {
         echo '<td><img src="' . $SITEROOT . '/images/checkmark.jpg" /></td>';
      }
      else
      {
         echo '<td><a href="?q=' . $q . '&track=' . urlencode($t->name) . '&artist=' . urlencode($t->artists[0]->name) . '&album=' . urlencode($t->album->name) . '&key=' . $t->href . '">Add</a></td>';
      }
   
      echo '</tr>';
   }

   echo '</table>';
   echo '</div>';
}

?>


<?php include("$ROOT/admin/includes/footer.php"); ?>
