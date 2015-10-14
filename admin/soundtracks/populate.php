<?php 

set_time_limit(0);

$ROOT = '../..';

require_once("$ROOT/includes/rdio/oauth.php");
require_once("$ROOT/api/includes/common.php");

$earlyLate = array();
$earlyLate[] = array('After Party', 'p491947');
$earlyLate[] = array('Bedroom Ballads', 'p491986');
$earlyLate[] = array('Facebooking', 'p492012');
$earlyLate[] = array('Friends Still Here', 'p492043');
$earlyLate[] = array('Hooking Up', 'p492062');
$earlyLate[] = array('Just Me and A Drink', 'p492082');
$earlyLate[] = array('Post Party Pizza in Dorm', 'p492102');
$earlyLate[] = array('Sing Me to Sleep', 'p492119');
$earlyLate[] = array('Still Studying', 'p492160');
$earlyLate[] = array('Winding Down', 'p492186');
$earlyLate[] = array('Working Night Shift', 'p492232');

$morning = array();
$morning[] = array('A Good Morning Indeed', 'p492290');
$morning[] = array('Checking Email', 'p492307');
$morning[] = array('Dad Brunch', 'p443146');
$morning[] = array('Family Breakfast', 'p492321');
$morning[] = array('Getting Kids Ready for School', 'p510839');
$morning[] = array('Getting Ready for Work', 'p510858');
$morning[] = array('Coffee', 'p510883');
$morning[] = array('Morning Commute', 'p510870');
$morning[] = array('Morning Workout', 'p508422');
$morning[] = array('Reading the Paper', null);
$morning[] = array('Sunday Paper', null);
$morning[] = array('Tweet Facebook', null);
$morning[] = array('Yoga', null);

$midDay = array();
$midDay[] = array('Library', 'p497090');
$midDay[] = array('At Work: Heavy Machinery', 'p497107');
$midDay[] = array('At Work: Long Haul', 'p497147');
$midDay[] = array('At Work: Restaurant', 'p497164');
$midDay[] = array('At Work: Writing, Editing', 'p497189');
$midDay[] = array('Caffeine Jolt', 'p508229');
$midDay[] = array('Cleaning the House', 'p508627');
$midDay[] = array('Coding', 'p508294');
$midDay[] = array('Driving around, errands', 'p508305');
$midDay[] = array('Lunch Break', 'p508338');
$midDay[] = array('Lunchtime Workout', 'p508422');
$midDay[] = array('Relaxing with Tea', 'p508450');
$midDay[] = array('Siesta', 'p492119');
$midDay[] = array('Studying', 'p497090');

$evening = array();
$evening[] = array('Around the Campire', 'p508498');
$evening[] = array('BBQ', 'p508545');
$evening[] = array('Cards with the Boys', 'p508570');
$evening[] = array('Chillaxing', 'p508614');
$evening[] = array('Cocktails', null);
$evening[] = array('Cooking Dinner', null);
$evening[] = array('Dinner Party: Formal', null);
$evening[] = array('Dinner Party: Family', null);
$evening[] = array('Dinner Party: Romantic', null);
$evening[] = array('Doing Email', null);
$evening[] = array('Driving', null);
$evening[] = array('Facebooking', null);
$evening[] = array('Getting Ready to Go Out', null);
$evening[] = array('Homework', null);
$evening[] = array('Nightcap', null);
$evening[] = array('Reading, Relaxing, Chilling', null);
$evening[] = array('Still At Work', null);	
$evening[] = array('Studying', null);
$evening[] = array('With The Kids: Bath', null);
$evening[] = array('With the Kids:Calm', null);
$evening[] = array('With the Kids: Bedtime Stories', null);
$evening[] = array('Workout', null);

$dayslices = array();
//$dayslices[] = array('Morning', $morning);
//$dayslices[] = array('Evening', $evening);
//$dayslices[] = array('Mid-Day', $midDay);
//$dayslices[] = array('Late Night, Early Morning', $earlyLate);

foreach ($dayslices as $dayslice)
{
   $daysliceName = $dayslice[0];
   $sts = $dayslice[1];

   echo "processing $daysliceName...<br>\n";
   
   foreach ($sts as $st)
   {
      $soundtrackName = $st[0];
      $playlistId = $st[1];
      $startSong = true;
      
      echo "processing $soundtrackName...<br><hr><br>\n";
      
      // Find or create soundtrack
      
      $ret = api('soundtrack', 'read', array('name' => $soundtrackName));
   
      echo "soundtrack.read = " . json_encode($ret) . "<br><hr><br>\n";

      if (!$ret->result)
      {
         // Need to create
         
         $ret = api('dayslice', 'read', array('name' => $daysliceName));
         
         echo "dayslice.read = " . json_encode($ret) . "<br><hr><br>\n";
         
         $daysliceId = $ret->dayslice->daysliceId;
         
         $params = array();
         $params['name'] = $soundtrackName;
         $params['profileId'] = 1;
         $params['daysliceId'] = $daysliceId;
         
         $ret = api('soundtrack', 'create', $params);
         
         echo "soundtrack.create = " . json_encode($ret) . "<br><hr><br>\n";
         
         $soundtrackId = $ret->soundtrack->soundtrackId;
      }
      else
      {
         $soundtrackId = $ret->soundtrack->soundtrackId;
      }
      
      // Find songs
      
      if (!$playlistId)
         continue;
         
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
         
         // Add to our database if doesn't exist yet
         
         $ret = api('song', 'read', array('rdioTrackId' => $trackKey));
         
         echo "song.read = " . json_encode($ret) . "<br><hr><br>\n";
   
         if (!$ret->result)
         {
            $params = array();
            $params['name'] = $name;
            $params['artist'] = $artist;
            $params['album'] = $album;
            $params['rdio'] = $trackKey;
            
            $ret = api('song', 'create', $params);
            
            echo "song.create = " . json_encode($ret) . "<br><hr><br>\n";
            
            $songId = $ret->songId;
         }
         else
         {
            $songId = $ret->song->songId;
         }      
               
         // Add song to soundtrack
         
         $params = array();
         $params['songId'] = $songId;
         $params['soundtrackId'] = $soundtrackId;
         $params['startSong'] = $startSong;
         
         $ret = api('soundtrack', 'add-song', $params);
         
         echo "soundtrack.add-song = " . json_encode($ret) . "<br><hr><br>\n";
         
         $startSong = false;
      }
   }
}

?>
