
<?php 

$ROOT = '../..';

require_once("$ROOT/includes/rdio/oauth.php");

require_once("$ROOT/api/includes/common.php");

if (isset($_GET['key']))
{
   $params = array();
   $params['name'] = $_GET['track'];
   $params['artist'] = $_GET['artist'];
   $params['album'] = $_GET['album'];
   $params['rdio'] = $_GET['key'];
   
   $ret = api('song', 'create', $params);   
   $songAdded = ($ret->result) ? $_GET['key'] : null;
}

$q = $_GET['q'];

if ($q)
{
   $params = array();
   $params['query'] = $q;

   $params['types'] = 'track';
   $search = $rdio->call('search', $params);
}

?>

<?php include("$ROOT/admin/includes/header.php"); ?>

<script src="https://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>

<script>
var playback_token = "GA9QorMR_____zl4cHo1YjluZWc0ZnlyZ3EzN3kyZnQ5c3NvdW5kdHJhY2s0LmNvbdnYdGntuW9sPCUwynTB0b4=";
var domain = "soundtrack.com";
</script>

<script src="/includes/rdio/rdio.js"></script>

<script>
$(document).ready(function()
{
});
</script>

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

<h2>Rdio Lookup...</h2>

<!--
<div id="apiswf"></div>
<div id="playback">
  <div id="track"></div>
  <div id="album"></div>
  <div id="artist"></div>
  <div><img src="" id="art"></div>
   <button id="pause">Pause</button>
   <button id="next">Next</button>
</div>
<input type="hidden" id="play_key" value="<?php echo $playlist->result->key; ?>">
</div>
-->

<form>
<input name="q" size="50" />
<input type="submit" name="search" value="Search" />
</form>

<br/>

<?php 

if ($search->result->results)
{
   echo '<div id="results">';
   echo '<table>';
   echo '<tr><th>Key</th><th>Track</th><th>Artist</th><th>Album</th><th></th></tr>';

   foreach ($search->result->results as $r)
   {
      echo '<tr>';
      echo '<td>' . $r->key . '</td><td>' . $r->name . '</td><td>' . $r->artist . '</td><td>' . $r->album . '</td>';
      
      if ($songAdded == $r->key)
      {
         echo '<td><img src="' . $SITEROOT . '/images/checkmark.jpg" /></td>';
      }
      else
      {
         echo '<td><a href="?q=' . $q . '&track=' . urlencode($r->name) . '&artist=' . urlencode($r->artist) . '&album=' . urlencode($r->album) . '&key=' . $r->key . '">Add</a></td>';
      }
   
      echo '</tr>';
   }

   echo '</table>';
   echo '</div>';
}

?>


<?php include("$ROOT/admin/includes/footer.php"); ?>
