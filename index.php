<?php
require_once __DIR__ . '/includes/session.php';

$rdio = null;

function stringToHtml($s)
{
   $s = str_replace("\n", "<br>", $s);
   $s = str_replace("'", "&apos;", $s);
   
   return $s;
}

require_once __DIR__ . '/includes/keys.php';
require_once __DIR__ . '/includes/fb.php';
require_once __DIR__ . '/includes/api.php';
require_once __DIR__ . '/includes/rdio/rdio.php';
$rdio = new Rdio(array(RDIO_CONSUMER_KEY, RDIO_CONSUMER_SECRET));

if ($st4Session) 
{
     
// Load dayslices
   
   $ret = api('dayslice', 'read');
   $dayslices = $ret->dayslices;
   
   // Load the profiles
   
   $ret = api('profile', 'read');
   $profiles = $ret->profiles;
   
   if ($st4Session)
   {
      $username = $st4Session->firstName;
      
      // Load the user's demographics
   
      $ret = api('account', 'demographics');
      $demographics = $ret->demographics;
      
      $eml = api('account', 'read');
      $email = $eml->email;   
   
      // How many starter questions have they seen?
      
      $ret = api('answer', 'read', array('starterQuestion' => true));
      $starterAnswerCount = $ret->answerCount;
            
      // How many regular questions have they seen?
      
      $ret = api('answer', 'read');
      $answerCount = ($ret->answerCount - $starterAnswerCount);
            
      // What's their primary profile?
      
      $primaryProfileScore = 0;
      $primaryProfileId = 0;
      $primaryProfile = null;
      
      foreach ($demographics->profiles as $p)
      {
         if ($p->count > $primaryProfileScore)
         {
            $primaryProfileScore = $p->count;
            $primaryProfileId = $p->profileId;
         }
      }
      
      if ($primaryProfileId)
      {
         $ret = api('profile', 'read', array('profileId' => $primaryProfileId));
         $primaryProfile = $ret->profile;

         // Load possible soundtracks
         // TODO: Do this on-the-fly when they go to change soundtracks?
      
         $ret = api('soundtrack', 'read', array('profileId' => $primaryProfileId));
         $soundtracks = $ret->soundtracks;
      }
   }
} 
else 
{
   $params = array(
      'scope' => $facebook_scope,
      'redirect_uri' => 'http://soundtrack4.com/login.php'
   );

   $loginUrl = $facebook->getLoginUrl($params);
}

// Do we need to create a new ST4 account?

?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Soundtrack4 Demo</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Soundtrack4 Interface Version 1">
<meta name="author" content="Stephen Young @stephenyoungdev">

<!-- Le styles -->
<link href="css/bootstrap.css" rel="stylesheet">
<style type="text/css">
body {
   padding-top: 60px;
   padding-bottom: 40px}
</style>
<link href="css/bootstrap-responsive.css" rel="stylesheet">
<link href="css/fonts.css" rel="stylesheet">
<link href="css/layout.css" rel="stylesheet">
<link href="css/landing.css" rel="stylesheet">
<?php if($starterAnswerCount < 5): ?>
   <link href="css/quiz.css" rel="stylesheet">
<?php endif; ?>

<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<!-- Le fav and touch icons -->
<link rel="shortcut icon" href="img/favicon.ico">
<script type="text/javascript" language="javascript">
var apiHost = 'http://<?php echo $_SERVER['HTTP_HOST']; ?>/api';
if (window.location.hash == "#_=_") {
   window.location.hash = "";
}
var soundtrackId;
</script>   
</head>
<body>
   <div id="albumart" style="background:url(/img/album-art/sized/bkgnd5.jpg) !important">
      <div class="darken"></div>
   </div>
   
   <?php if ($st4Session) { ?>
   
   <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-translucent2 navbar-inner">
         <div class="container">
            <div class="row">
               <div class="span10 offset1">
                  <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                     <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                     <span class="icon-bar"></span>
                  </a>
                  <a class="brand" href=""><img src="img/logo.png"></a>
                  <div id="user-account" class="pull-right">
                     <span class="user-name"><a href="/logout.php"><?php echo $username; ?></a></span>
                     <img class="user-avatar" src="http://www.gravatar.com/avatar/<?php echo @md5( strtolower( trim( $email ) ) ); ?>.png">
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   
   <?php } ?>
   
   <div class="container">
   
      <?php if (!$st4Session) { ?>
   
      <div class="row">
         <div class="span8 offset2 logobig">
            <img src="img/logo-big.png">
         </div>
         <div class="span8 offset2 slogan">
            Lean back to a soundtrack for your life.
         </div>
         <div class="span8 offset2 facebook">
            <a href="<?php echo $loginUrl; ?>">
               <img src="img/fb-large.png">
            </a>
         </div>
      </div>
      
      <?php } else if($starterAnswerCount < 5){ ?>

         <div class="row">
           <div id="welcome" class="span8 offset2">
             <h1>Welcome, <?php echo $username; ?>!</h1>
             <h3>Answer 5 questions to discover your personal soundtrack</h3>
           </div>
           <div id="quiz" class="span8 offset2">
             <div class="quiz-header">
               <h2 id="question-label"></h2>
             </div>
             <div id="answers" class="image-quiz-4">
               <div class="choice"></div>
               <div class="choice"></div>
               <div class="choice"></div>
               <div class="choice"></div>
             </div>
             <!--a class="skip">Skip</a-->
             <div id="quiz-caret" class="caret"></div>
           </div>
           <div id="numbers" class="span8 offset2">
             <span id="number_1" class="selected">1</span>
             <span id="number_2" >2</span>
             <span id="number_3" >3</span>
             <span id="number_4" >4</span>
             <span id="number_5" >5</span>
           </div>
         </div>

      <?php } else { ?>
  <?php require_once __DIR__ . '/includes/rdio.php'; ?>    
      <!--div class="row">
         <div id="page_title" class="span12">
            <h1></h1>
         </div>
      </div-->

      <div class="row">
         <div id="profile-view" class="span7 offset1">
            <div class="content">
               <h6>Your Psycho-Acoustic Profile</h6>
               <div id="graph">
                  <div id="graph-content"></div>
               </div>
               <h5 id="question-label" style="display:block"></h5>
               <img id="question-image" style="display:block" />
               <div id="answers">
                  <!-- div class="answer"><img src=""></div-->
               </div>
               <!--h5><a class="skip" href="#">Skip</a></h5-->
            </div>
         </div>
         
         <?php if ($primaryProfile) { ?>
         <div class="dominant-trait span3">
            <h2><em>Your Dominant Trait</em></h2>
            <h3 id="primary-profile-name" class="six-pack"><?php echo $primaryProfile->name; ?></h3>
            <p id="primary-profile-desc"><?php echo stringToHtml($primaryProfile->description); ?></p>
            <!-- TODO: p><a class="btn" style="display:block;" href="#">Share</a></p-->
            <!-- a href="#sign_in" role="button" class="btn btn-primary" data-toggle="modal" class="btn btn-primary">trigger signin</a-->
         </div>
         <?php } ?>
         
      </div>
      
      <?php } ?>
      
   </div>
   
   <?php if ($st4Session && $starterAnswerCount >= 5) { ?>
   
   <div id="api"></div>
   
   <div id="audio-controls">
      <div id="above-controls">
         <strong>Now Playing:</strong>&nbsp;&nbsp;&nbsp;&nbsp;A soundtrack for 
         <span id="soundtrack-name"><?php echo $soundtrack->activityName; ?></span>
         <!-- span class="upcoming">
            <strong>Coming up:</strong>
            <img src="albums/greatesthits.jpg">
            <img src="albums/iyh.jpg">
            <Img src="albums/obo.jpg">
         </span-->
      </div>
      <div id="changebtn">
         <a id="change_soundtrack_button" class="btn btn-primary" class="btn btn-primary">
            <i class="icon-white icon-music"></i> Change Soundtrack
         </a>
      </div>
      <div class="album">
         <img id="player-art" src="" />
      </div>
      <div class="song">
         <span id="player-track" class="title"></span>
         <span id="player-artist" class="artist"></span>
         <div class="controls">
            <i id="player-previous" class="icon-white icon-fast-backward"></i>
            <i id="player-play" class="icon-white icon-play"></i>
            <i id="player-next" class="icon-white icon-fast-forward"></i>
         </div>
      </div>
   
      <div class="playback-bar">
         <div id="player-progress" class="playback"></div>
      </div>
   
      
      <div class="sound-controls pull-right">
         <i class="icon-white icon-volume-up"></i>
         <div class="sound-bar">
            <div class="sound-level">
               <div class="knob"></div>
            </div>
         </div>
      </div>
      
      
   </div>

   <?php } ?>
   <?php if ($st4Session && $starterAnswerCount >= 5) { ?>
   
   <!-- Change Soundtrack Modal -->
   <div id="change_soundtrack" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <!--div class="modal-header">
         <h3 id="myModalLabel">Change Soundtrack</h3>
      </div-->
      <div class="modal-body">
         <!--p>Specify what time of day it is, and what you're doing.</p-->
         <form class="form-horizontal">
            <div class="control-group">
               <label class="control-label" for="timeofday" style="font-weight:bold;">What time of day is it?</label>
               <div class="controls">
                  <select name="timeofday" id="timeofday" onchange="onTimeOfDayChange()">
                     <?php foreach ($dayslices as $dayslice) { ?>
                        <option value="<?php echo $dayslice->daysliceId; ?>"><?php echo $dayslice->name; ?></option>
                     <?php } ?>
                  </select>
               </div>
            </div>
            <div class="control-group bigger-modal-data" id="timeofdaycontrols" style="display:none">
               <label class="control-label" for="activity">What you are doing?</label>
               <div class="controls">
                  <select name="activity" id="activity">
                  </select>
               </div>
            </div>
         </form>
      </div>
      <div class="modal-footer">
         <!--button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button-->
         <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true" onclick="onSoundtrackChange()">Play!</button>
      </div>
   </div>
   
   <!-- Sign In Modal -->
   <div id="rdio_sign_in" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-header">
         <h3 id="myModalLabel">Like Your Soundtrack?</h3>
      </div>
      <div class="modal-body">
         <p>To keep listening, you need to sign in with Rdio.</p>
      </div>
      <div class="modal-footer">
         <div class="modal-footer-left pull-left">
            <p>Already have an Rdio account?</p>
            <a href="" class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Sign in with Rdio</a>
         </div>
         <div class="modal-divider pull-left"></div>
         <div class="modal-footer-right pull-right">
            <p>Don't have an Rdio account?</p>
            <a class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Get an Rdio Account</a>
         </div>
      </div>
   </div>
   
   <?php } ?>
            
<!-- jQuery and friends -->
<script src="js/vendor/jquery.min.js"></script>
<script src="js/vendor/jquery.transit.min.js"></script>
<script src="js/vendor/jquery.ui.min.js"></script>

<!-- D3 + Graphic libs -->
<script src="js/vendor/flotr.js"></script>
<!--[if lt IE 9]>
<script src="js/vendor/flotr.ie.min.js"></script>
<![endif]-->

<!-- Usability / Bootstrap -->
<script src="js/vendor/bootstrap.min.js"></script>

<script type="text/javascript" language="javascript">
function api(resource, action, json, onSuccess)
{
   var url = apiHost + '/' + resource + '/' + action + '?sessionId=<?php echo $st4Session->sessionId; ?>';
   
   $.ajax( 
   {
      url: url,
      type: 'post',
      data: JSON.stringify(json),
      dataType: 'json',
      success: onSuccess
   });
}
</script>

<?php if($st4Session): ?>
<script type="text/javascript">
   var starterQuestionsAnswered = <?php echo $starterAnswerCount; ?>;
   var starterQuestion = <?php echo ($starterAnswerCount < 5) ? 'true' : 'false'; ?>;
   var questionId = 0;
</script>
<?php endif; ?>

<?php if ($st4Session && $starterAnswerCount >= 5) { ?>
<script src="https://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>

<script src="js/psychograph.js"></script>
<script src="js/vendor/jquery.rdio.js"></script>

<script type="text/javascript">
   <?php
      echo 'var playback_token = "' . $rdio->call('getPlaybackToken', array('domain'=>'soundtrack4.com'))->result . '";';
   ?>
</script>
<script src="js/player.js"></script>

<script type="text/javascript" language="javascript">

   var questionsAnswered = <?php echo $answerCount; ?>;
   var hourToDayslice = new Array();
   var primaryProfileId = <?php echo $primaryProfileId; ?>;
   
   $(document).ready(function()
   {
      // Default time-of-day to the current time of day
      
      var d = new Date();
      var h = d.getHours();
      var daysliceId = hourToDayslice[h];

      $("#timeofday").val(daysliceId);
      
      // If no soundtrack yet, display the pick-a-soundtrack dialog

      <?php if (!$soundtrackId && $st4Session) { ?>
      onTimeOfDayChange();
      $("#change_soundtrack").modal({show:true});
      <?php } ?>
      
      $('#change_soundtrack_button').click(function() 
      {
         onTimeOfDayChange();
         $("#change_soundtrack").modal({show:true});
      });

   });

   <?php
   
   // Daysclice lookup by hour
   
   if ($st4Session)
   {
      foreach ($dayslices as $slice)
      {
         if ($slice->endHour < $slice->startHour)
            $slice->endHour += 24;
            
         for ($i = $slice->startHour; $i <= $slice->endHour; $i++)
         {
            $h = ($i % 24);
            echo "hourToDayslice[" . $h . "] = " . $slice->daysliceId . ";\n";
         }
      }
   }
      
   // Profile bars
   
   if ($st4Session && $starterAnswerCount >= 5)
   {
      for ($i = 0; $i < count($profiles); $i++) 
      {
         $p = $profiles[$i];
         $profileId = $p->profileId;
         $title = $p->name;
         $tooltip = addslashes($p->shortDescription);
         $score = 0.0;
         
         foreach ($demographics->profiles as $p)
         {
            if ($profileId == $p->profileId)
            {
               $score = $p->count;
               break;
            }
         }
         
         if ($profileId == $primaryProfileId)
         {
            echo "bargraphSeries.push({_y:$score, highlighted:1});\n";
         }
         else
         {
            echo "bargraphSeries.push({_y:$score});\n";
         }
         
         echo "bargraphTitles.push('$title');\n";
         echo "bargraphTooltips.push('$tooltip');\n";
         echo "bargraphProfileIds.push($profileId);\n";
      }
   }
      
   // Break the soundtracks into dayslices

   if ($soundtracks) 
   {
      $soundtracksByDayslice = array();
      
      foreach ($soundtracks as $s) 
      {
         $soundtrackId = $s->soundtrackId;
         $activityId = $s->activityId;
         $activityName = $s->activityName;
         $daysliceId = $s->daysliceId;
         
         if (!isset($soundtracksByDayslice[$daysliceId]))
         {
            $soundtracksByDayslice[$daysliceId] = array();
         }
         
         $soundtracksByDayslice[$daysliceId][] = array($soundtrackId, $activityName);
      }
   
      // Dump out as javascript
      
      echo "var soundtracksByDayslice = new Array();\n";
      
      foreach ($soundtracksByDayslice as $daysliceId => $sts)
      {
         $pairs = array();
         
         foreach ($sts as $st)
         {
            $pairs[] = '"' . $st[0] . '": "' . $st[1] . '"';
         }
   
         echo "soundtracksByDayslice[$daysliceId] = { " . implode(",", $pairs) . " };\n";
      }
   } 
   
   ?>
    
   function onTimeOfDayChange()
   {
      // What dayslice did they pick?
      
      var daysliceId = $("#timeofday").val();
      var soundtracks = soundtracksByDayslice[daysliceId];
   
      $('#activity').empty();
   
      $.each(soundtracks, function(key, value) 
      {
         $('#activity')
            .append($('<option>', { value : key })
            .text(value)); 
      });         
      
      $("#timeofdaycontrols").css("display", "block");
   }
   
   function onSoundtrackChange()
   {
      // They picked a new soundtracks -- grab some music and start playing it
      
      soundtrackId = $("#activity").val();
      var json = {
         soundtrackId: soundtrackId,
      };
      
      api('soundtrack', 'read', json, function(data)
      {
         $('#soundtrack-name').text(data.soundtrack.activityName);
      });
      
      api('account-queue', 'refresh', json, function(data)
      {
         api('account-queue', 'read', json, function(data)
         {
            var songs = data.songs;
               
            rdioQueue = Array();
            
            for (var i = 0; i < songs.length; i++)
            {
               rdioQueue.push(songs[i].rdio);
            }      

            rdioQueueIndex = 0;
      
            $('#api').rdio().play(rdioQueue[rdioQueueIndex]);
         });
      });
   }
   
</script>
<?php } ?>

<!-- App specific libs -->
<script src="js/albumart.js"></script>
<?php if ($st4Session) { ?>
<script src="js/questions.js"></script>
<?php } ?>

</body>
</html>
