var duration = 1; // track the duration of the currently playing track
var rdioQueue = [];
var rdioQueueIndex = 0;
var startedPlaying = false;
var trackPlaying = null;
var transitioningToNextSong = false;
var rdioUserInfo = null;

var domain = "soundtrack4.com";

$(document).ready(function()
{
   $('#api').bind('ready.rdio', function(e, userInfo)
   {
      // userInfo = ({isFree:false, freeRemaining:0, isUnlimited:false, isSubscriber:true, isTrial:false})

      rdioUserInfo = userInfo;
   
      console.log("playing " + rdioQueue[rdioQueueIndex]);
      $(this).rdio().play(rdioQueue[rdioQueueIndex]);
   });
 
   $('#api').bind('playingTrackChanged.rdio', function(e, playingTrack, sourcePosition)
   {
      //alert("playingTrackChanged: " + playingTrack);
      
      trackPlaying = playingTrack;
      
      if (playingTrack)
      {
         duration = playingTrack.duration;

         $('#player-art').attr('src', playingTrack.icon);
         $('#player-track').text(playingTrack.name);
         $('#player-album').text(playingTrack.album);
         $('#player-artist').text(playingTrack.artist);
      }
   });
   
   $('#api').bind('queueChanged.rdio', function(e, queue)
   {
      //alert("queueChanged: " + queue);
   });
   
   $('#api').bind('positionChanged.rdio', function(e, position)
   {
      $('#player-progress').css('width', Math.floor(100*position/duration)+'%');
   });
      
   $('#api').bind('playStateChanged.rdio', function(e, playState)
   {
      //alert("playState: " + playState);
         
      if (playState == 2)
      {
         //alert("trackPlaying: " + trackPlaying);
         //alert("startedPlaying: " + startedPlaying);
         //alert("transitioningToNextSong: " + transitioningToNextSong);
      }

      if (playState === 0)
      {
         $('#player-play').show();
         $('#player-pause').hide();
      }
      else if ((playState == 2) &&
               (startedPlaying) &&
               (trackPlaying === null) &&
               (!transitioningToNextSong))
      {
         // Track finished
         // If this is the first track and they aren't subscribed to rdio, prompt for rdio authentication
         
         // TODO
         if (!rdioUserInfo.isSubscribed)
         {
//            $("#rdio_sign_in").modal({show:true});
         }

         // What if we are running out of tracks?
         // TODO
         
         // Move to next track
         
         transitioningToNextSong = true;
      
         $('#api').rdio().play(rdioQueue[++rdioQueueIndex]);
      }
      else
      {
         if (playState == 1)
         {
            //alert("playState == 1");
            
            var json = {
                     soundtrackId: soundtrackId,
                     songRdio: rdioQueue[rdioQueueIndex],
                     action: 'play'
                  };
             
            api('song', 'act', json, function(data)
            {
               startedPlaying = true;
            });
         }
                     
         transitioningToNextSong = false;
            
         $('#player-play').hide();
         $('#player-pause').show();
      }
   });
   
   $('#api').rdio(playback_token);

   $('#player-previous').click(function()
   {
      var json = {
               soundtrackId: soundtrackId,
               songRdio: rdioQueue[rdioQueueIndex],
               action: 'replay'
            };
            
      api('song', 'act', json);
   
      $('#api').rdio().play(rdioQueue[--rdioQueueIndex]);
   });
   
   $('#player-play').click(function() { $('#api').rdio().play(); });
   $('#player-pause').click(function() { $('#api').rdio().pause(); });
   
   $('#player-next').click(function()
   {
      console.log("player clicked next");
      var json = {
               soundtrackId: soundtrackId,
               songRdio: rdioQueue[rdioQueueIndex],
               action: 'skip'
            };
            
      api('song', 'act', json);
   
      $('#api').rdio().play(rdioQueue[++rdioQueueIndex]);
   });
});
 
function thumbsUp()
{
   // Record the vote
   
   var json = {
            soundtrackId: soundtrackId,
            songRdio: rdioQueue[rdioQueueIndex],
            action: 'like'
         };
         
   api('song', 'act', json);
}

function thumbsDown()
{
   // Record the vote

   var json = {
            soundtrackId: soundtrackId,
            songRdio: rdioQueue[rdioQueueIndex],
            action: 'dislike'
         };
         
   api('song', 'act', json);

   // Skip to next song
   
   $('#api').rdio().play(rdioQueue[++rdioQueueIndex]);
}
