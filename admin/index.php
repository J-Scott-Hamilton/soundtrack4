<?php
$ADMINUSER = $_SERVER['REMOTE_USER'];

$ROOT = '..';

include_once __DIR__ ."/includes/header.php";
include_once __DIR__ . "/includes/body.php";

?>

<div id="main">

<h1>Admin</h1>

<table>
   <tr>
      <td>
         <ul>
            <li><a href="./day-slices">Day-Slices</a></li>
            <li><a href="./activities">Activities</a></li>
            <li><a href="./fantasies">Fantasies</a></li>
            <li><a href="./tags">Tags</a></li>
         </ul>
         <ul>
            <li><a href="./profiles">Profiles</a></li>
            <li><a href="./profiles/orphaned">Orphaned Profiles</a></li>
         </ul>
         <ul>
            <li><a href="./playlists">Playlists</a></li>
         </ul>
         <ul>
            <li><a href="./songs">Songs</a></li>
            <li><a href="./songs/by-soundtrack">Songs by Soundtrack</a></li>
            <li><a href="./songs/by-user">Songs by User</a></li>
            <li><a href="./songs/in-spotify">Songs in Spotify</a></li>
            <li><a href="./songs/duplication.htm">Song Duplication</a></li>
            <li><a href="./songs/find-song">Find Song...</a></li>
         </ul>
         <ul>
            <li><a href="./soundtracks">Soundtracks</a></li>
            <li><a href="./soundtracks/by-activity">Soundtracks by Activity</a></li>
            <li><a href="./soundtracks/by-dayslice">Soundtracks by Dayslice</a></li>
            <li><a href="./soundtracks/startsongs">Soundtracks w/o Start Songs</a></li>
            <li><a href="./soundtracks/incomplete">Soundtracks Incomplete</a></li>
            <li><a href="./soundtracks/limitations">Soundtrack Limitations</a></li>
         </ul>
         <ul>
            <li><a href="./questions">Questions</a></li>
            <li><a href="./questions/required">Required Questions</a></li>
         </ul>
         <ul>
            <li><a href="../simulation">Simulation</a></li>
         </ul>
         <ul>
            <li><a href="./echonest">EchoNest</a></li>
         </ul>
         <ul>
            <li><a href="./album-art">Album Art</a></li>
         </ul>
      </td>
      <!--
      <td>
         These are just for reference...<br>
         <ul>
            <li><a href="./age-cohorts">Age Cohorts</a></li>
            <li><a href="./gender-cohorts">Gender Cohorts</a></li>
            <li><a href="./location-cohorts">Location Cohorts</a></li>
         </ul>
         <ul>
            <li><a href="./refining-questions">Refining Questions</a></li>
            <li><a href="./confirming-questions">Confirming Questions</a></li>
            <li><a href="./fun-questions">Fun Questions</a></li>
            <li><a href="./refining-questions/required">Required Refining Questions</a></li>
         </ul>
         <ul>
            <li><a href="./age-questions">Age Questions</a></li>
            <li><a href="./gender-questions">Gender Questions</a></li>
            <li><a href="./location-questions">Location Questions</a></li>
         </ul>
      </td-->
   </tr>
</table>

</div>

<?php /* include("$ROOT/admin/includes/footer.php"); */ ?>
