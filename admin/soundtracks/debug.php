<?php

set_time_limit(0);

include_once("../../api/includes/common.php");

$soundtrackId = 494;
$accountId = 224;

$params = array('accountId' => $accountId, 'soundtrackId' => $soundtrackId);

$totalDjangos = 0;
$totalSongs = 0;
$soundtrackSongs = 0;
$djangoPositions = array();

for ($i = 0; $i < 1; $i++)
{
   $ret = api('account-queue', 'refresh', $params);
   
   $ret = api('account-queue', 'read', $params);
   $songs = $ret->songs;
   $djangos = 0;
   $totalSongs += count($songs);
   $pos = 0;
   
   foreach ($songs as $song)
   {
      if (strpos($song->artist, "Django") !== FALSE)
      {
         $djangos++;
         $djangoPositions[$pos]++;
      }
      
      $pos++;
   }
   
   $ret = api('soundtrack', 'read', array('soundtrackId' => $soundtrackId));
   $playlists = $ret->soundtrack->playlists;
   $possibleDjangos = 0;
   
   foreach ($playlists as $playlist)
   {
      $playlistDjangos = 0;
      
      //echo $playlist->name . " : " . $playlist->weight . " : " . $playlist->startSongs . "<br>\n";
      
      // How many Djanos in this playlist?
      
      $ret = api('song', 'read', array('playlistId' => $playlist->playlistId));
      $songs = $ret->songs;
   
      foreach ($songs as $song)
      {
         $soundtrackSongs++;
      
         if (strpos($song->artist, "Django") !== FALSE)
         {
            $possibleDjangos++;
            $playlistDjangos++;
         }
      }
      
      //echo "This playlist contains $playlistDjangos Djangos out of " . count($songs) . " total songs<br><br>\n\n";
   }
   
   //echo "This queue contains $djangos Djangos<br>\n";
   //echo "$possibleDjangos out of $soundtrackSongs = " . $possibleDjangos / $soundtrackSongs * 100.0 . "<br>\n";
   //echo $possibleDjangos / $soundtrackSongs * 100.0 . " x 5% = " . $possibleDjangos / $soundtrackSongs * 100.0 * 0.05 . "<br>\n";
   
   $totalDjangos += $djangos;
}

//echo "Overall Django % = $totalDjangos / $totalSongs = " . $totalDjangos  /$totalSongs * 100 . "%<br><br>\n\n";

//echo json_encode($djangoPositions);

/*

100
266 / 1010 = 2.63%

1000
2600 / 101000 = 2.57%
{"46":24,"71":22,"39":23,"64":28,"36":26,"95":23,"48":32,"62":31,"88":28,"16":31,"57":25,"76":24,"5":27,"33":25,"55":24,"58":22,"92":29,"37":36,"40":30,"43":28,"22":37,"52":32,"7":23,"59":28,"65":34,"3":39,"21":27,"72":31,"84":30,"87":21,"2":31,"79":17,"83":21,"10":25,"94":33,"35":29,"13":27,"41":23,"45":29,"50":34,"29":28,"54":26,"66":28,"20":33,"24":24,"28":25,"51":32,"74":27,"15":26,"32":25,"27":34,"61":29,"89":24,"82":23,"53":29,"75":25,"4":22,"14":35,"8":22,"80":27,"60":25,"19":20,"31":30,"44":24,"34":37,"38":29,"98":31,"69":30,"49":30,"73":29,"17":25,"67":25,"78":23,"23":32,"77":32,"86":24,"91":20,"1":26,"81":26,"6":28,"85":28,"93":23,"12":28,"90":24,"56":29,"25":26,"99":31,"42":23,"68":22,"96":22,"11":22,"9":26,"100":24,"18":27,"97":24,"63":21,"47":23,"26":29,"70":11,"30":22}

10000
26052 / 1010000 = 2.58%
{"27":274,"80":250,"55":262,"99":273,"83":244,"14":252,"77":249,"92":238,"7":290,"30":251,"88":224,"61":277,"82":280,"91":262,"29":252,"87":253,"11":250,"21":231,"23":241,"41":272,"67":270,"31":245,"57":266,"74":248,"96":251,"36":275,"45":257,"59":298,"40":268,"78":226,"1":263,"89":309,"97":248,"79":261,"24":232,"28":256,"94":294,"15":271,"66":273,"68":254,"90":281,"4":262,"70":263,"86":267,"10":265,"93":261,"44":287,"76":229,"84":275,"39":278,"100":284,"60":263,"64":246,"98":271,"47":240,"42":294,"81":248,"20":266,"25":268,"58":243,"5":273,"8":256,"85":244,"51":249,"62":241,"56":271,"65":265,"3":259,"50":253,"72":285,"38":252,"52":280,"71":239,"32":283,"95":275,"16":262,"48":261,"13":267,"26":266,"49":268,"19":260,"37":254,"33":267,"12":250,"69":251,"75":287,"54":236,"6":255,"18":273,"53":238,"9":263,"73":236,"22":252,"63":261,"35":221,"46":258,"43":281,"2":252,"34":278,"17":249}[fitzpatrick]$ 
*/

$data = json_decode('{"27":274,"80":250,"55":262,"99":273,"83":244,"14":252,"77":249,"92":238,"7":290,"30":251,"88":224,"61":277,"82":280,"91":262,"29":252,"87":253,"11":250,"21":231,"23":241,"41":272,"67":270,"31":245,"57":266,"74":248,"96":251,"36":275,"45":257,"59":298,"40":268,"78":226,"1":263,"89":309,"97":248,"79":261,"24":232,"28":256,"94":294,"15":271,"66":273,"68":254,"90":281,"4":262,"70":263,"86":267,"10":265,"93":261,"44":287,"76":229,"84":275,"39":278,"100":284,"60":263,"64":246,"98":271,"47":240,"42":294,"81":248,"20":266,"25":268,"58":243,"5":273,"8":256,"85":244,"51":249,"62":241,"56":271,"65":265,"3":259,"50":253,"72":285,"38":252,"52":280,"71":239,"32":283,"95":275,"16":262,"48":261,"13":267,"26":266,"49":268,"19":260,"37":254,"33":267,"12":250,"69":251,"75":287,"54":236,"6":255,"18":273,"53":238,"9":263,"73":236,"22":252,"63":261,"35":221,"46":258,"43":281,"2":252,"34":278,"17":249}');

include "../includes/libchart/classes/libchart.php";

$chart = new VerticalBarChart();
$dataSet = new XYDataSet();

for ($position = 0; $position <= 100; $position++)
{
   if ($data->$position == 0)
   {
      echo "position $position has 0 count<br>\n";
   }

   $dataSet->addPoint(new Point($position, $data->$position));
}

$chart->setDataSet($dataSet);
$chart->render("debug.png");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15" />
</head>
<body>
	<img alt="Vertical bars chart" src="debug.png" style="border: 1px solid gray;"/>
</body>
</html>
