<?php 

set_time_limit(0);

$ROOT = '../..';

require_once("$ROOT/includes/rdio/oauth.php");
require_once("$ROOT/api/includes/common.php");

$url = "http://newmedia.kcrw.com/tracklists/index.php?channel=Live";


/*
$params = array();

$params['keys'] = $playlistId;
$params['extras'] = 'trackKeys';

$search = $rdio->call('get', $params);
$result = $search->result;
$playlist = $result->$playlistId;

echo "rdio.playlist = " . json_encode($playlist) . "<br><hr><br>\n";

$trackKeys = $playlist->trackKeys;

foreach ($trackKeys as $trackKey)
{
   $params = array();
   $params['keys'] = $trackKey;
   
   $search = $rdio->call('get', $params);
   
   $result = $search->result;
   $track = $result->$trackKey;
   $album = $track->album;
   $artist = $track->albumArtist;
   $name = $track->name;

   echo "rdio.track = " . json_encode($search) . "<br><hr><br>\n";

   $params = array();
}
*/

?>

