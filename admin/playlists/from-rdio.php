<?php 

set_time_limit(0);

$ROOT = '../..';

require_once("$ROOT/includes/rdio/oauth.php");
require_once("$ROOT/api/includes/common.php");

function get_web_page( $url ) 
{ 
   $options = array( 
      CURLOPT_RETURNTRANSFER => true,     // return web page 
      CURLOPT_HEADER         => true,    // return headers 
      CURLOPT_FOLLOWLOCATION => true,     // follow redirects 
      CURLOPT_ENCODING       => "",       // handle all encodings 
      CURLOPT_USERAGENT      => "spider", // who am i 
      CURLOPT_AUTOREFERER    => true,     // set referer on redirect 
      CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect 
      CURLOPT_TIMEOUT        => 120,      // timeout on response 
      CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects 
   ); 
   
   $ch = curl_init( $url ); 
   
   curl_setopt_array( $ch, $options ); 
   
   $content = curl_exec( $ch ); 
   $err     = curl_errno( $ch ); 
   $errmsg  = curl_error( $ch ); 
   $header  = curl_getinfo( $ch ); 
   
   curl_close( $ch ); 
   
   //$header['errno']   = $err; 
   // $header['errmsg']  = $errmsg; 
   //$header['content'] = $content; 
    
   print($header[0]); 
    
   return $header; 
}  

$rdioUrl = "http://rd.io/x/QVFb6TNUyh8";

$myUrlInfo = get_web_page($rdioUrl); 

echo $myUrlInfo["url"];

// http://www.rdio.com/people/jshamil/playlists/759902/TD_Core__Mellow_Classic/

$playlistId = "p747552";

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

?>
