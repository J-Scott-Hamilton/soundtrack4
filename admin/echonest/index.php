<?php

// This is just for experimenting with the echonest API...

$ROOT = '../..';

// Your API Key: UNBPQXFNYSVTNXRLB
// Your Consumer Key: 801f9273b8d2eff2b268f5b0594d0dfc
// Your Shared Secret: 5Qwaco2jQSi9S+MgeJDqgw 

// "http://developer.echonest.com/api/v4/artist/similar?api_key=N6E4NIOVYMTHNDM8J&id=ARH6W4X1187B99274F&format=json&results=1&start=0"
// "http://developer.echonest.com/api/v4/artist/search?api_key=N6E4NIOVYMTHNDM8J&format=json&mood=happy&results=1"
// "http://developer.echonest.com/api/v4/artist/search?api_key=N6E4NIOVYMTHNDM8J&format=json&style=jazz&results=1"
// "http://developer.echonest.com/api/v4/artist/top_hottt?api_key=N6E4NIOVYMTHNDM8J&format=json&results=1&start=0&bucket=hotttnesss"

require_once(dirname(__FILE__) . "/../includes/curl.php");

$ret = json_decode(curl("http://developer.echonest.com/api/v4/song/profile?api_key=UNBPQXFNYSVTNXRLB&format=json&id=SOCZMFK12AC468668F&bucket=audio_summary"));
print_r($ret);

$ret = json_decode(curl("http://developer.echonest.com/api/v4/song/identify?api_key=UNBPQXFNYSVTNXRLB&format=json&id=SOCZMFK12AC468668F&bucket=audio_summary"));
print_r($ret);

exit();

$ret = json_decode(curl("http://developer.echonest.com/api/v4/artist/list_terms?api_key=UNBPQXFNYSVTNXRLB&format=json&type=mood"));
$moods = $ret->response->terms;

$ret = json_decode(curl("http://developer.echonest.com/api/v4/artist/list_terms?api_key=UNBPQXFNYSVTNXRLB&format=json&type=style"));
$styles = $ret->response->terms;
$results = null;

if (isset($_GET['search']))
{
   $params = array(
               'api_key' => 'UNBPQXFNYSVTNXRLB',
               'bucket' => 'tracks',
               'format' => 'json');

   if ($_GET['artist'])
   {
      $params['artist'] = $_GET['artist'];
   }
    
   if (strlen($_GET['mood']) > 0)
   {
      $params['mood'] = $_GET['mood'];
   }
    
   if (strlen($_GET['style']) > 0)
   {
      $params['style'] = $_GET['style'];
   }
    
   $url = "http://developer.echonest.com/api/v4/song/search?" . http_build_query($params) . "&bucket=id:rdio-us-streaming";
       
   //echo "url = $url";
    
   $results = json_decode(curl($url));
   
   //echo "results = " . print_r($results, true);
}

include_once("$ROOT/api/includes/common.php");
include_once("$ROOT/admin/includes/header.php");
include_once("$ROOT/admin/includes/body.php");

?>

<div id="main">

<a href="/admin">Admin</a>

<h1>EchoNest</h1>

<form>
<table>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="artist">Artist:</label></td>
   <td width="100%"><input name="artist" size="50" /></td>
</tr>
<tr>
   <td style="text-align:right;vertical-align:middle"><label for="mood">Mood:</label></td>
   <td>
      <select name="mood">
         <option value="">any</option>
         <?php foreach($moods as $mood) { ?>
         <option value="<?php echo $mood->name; ?>"><?php echo $mood->name; ?></option>
         <?php } ?>
      </select>
   </td>
</tr>
<tr>
   <td style="text-align:center;vertical-align:middle"><label for="style">Style:</label></td>
   <td>
      <select name="style">
         <option value="">any</option>
         <?php foreach($styles as $style) { ?>
         <option value="<?php echo $style->name; ?>"><?php echo $style->name; ?></option>
         <?php } ?>
      </select>
   </td>
</tr>
<tr>
   <td></td>
   <td><input type="submit" name="search" value="Search" /></td>
</tr>
</table>
</form>

<hr/>

<?php 

if ($results) 
{
   //echo print_r($results, true);
   
   echo '<table class="admin">';
   echo '<tr><th>Song</th><th>Artist</th></tr>';
   
   foreach ($results->response->songs as $song)
   {
      echo '<tr><td>' . $song->title . '</td><td>' . $song->artist_name . '</td></tr>';
   }
   
   echo '</table>';
}

?>

</div>

<?php include("$ROOT/admin/includes/footer.php"); ?>