
$(document).ready(function()
{
   // Load a question
reposition_caret();
 loadQuestion(starterQuestion);
});

function reposition_caret(){
   if(starterQuestionsAnswered < 5){
      var positions = [303, 338, 372, 408, 442];
      $("#quiz-caret").css("left", (positions[starterQuestionsAnswered]).toString() + 'px');
      for(var i=0; i < 5; i++) $("#number_" + i).removeClass("selected");
      $("#number_" + (starterQuestionsAnswered + 1)).addClass("selected");
   }
}

function flash_albumart(){
   for(var i=0;i<10;i++){
      a = $('<div class="white-block"></div>');
      x = Math.floor(Math.random() * ($(document).width()) / 100);
      y = Math.floor(Math.random() * $(document).height() / 100);
      a.css('top', 100 * y);
      a.css('left', 100 * x);
      $('#albumart').append(a);
      a.fadeOut(500, function() { $(this).remove(); });
   }

}

function loadQuestion(starterQuestion)
{
   var json = {
            starterQuestion: starterQuestion
         };
         
   api('question', 'read', json, function(data)
   {
      var question = data.question;
      var type = question.questionTypeId;
      var answers = question.answers;
      questionId = question.questionId;
      
      // Some questions are image w/ text answers
      // Some questions are text w/ image answers
      // Some questions are...
      
      if (question.text)
      {
         $('#question-label').text(question.text);
         $('#question-label').show();
      }
      else
      {
         $('#question-label').hide();
      }

      if (question.imageUrl)
      {
         $('#question-image').attr('src', question.imageUrl);
         $('#question-image').css('display', 'inline-block');
      }
      else
      {
         $('#question-image').hide();
      }
       
      $('#question-image').attr('title', question.tooltip);
                     
      if (type == 1)
      {
         // Multiple Choice

         $('#answers').empty();

         $('#answers').removeClass('answer_visual');
         $('#answers').removeClass('answer_text');
         $('#answers').removeClass('answer_image');
         $('#answers').removeClass('answer_both');
         $('#answers').removeClass('answer_smaller');

         $("#answers").removeClass("answer_2col");
         $("#answers").removeClass("answer_3col");
         $("#answers").removeClass("answer_4col");

         if (question.imageUrl) $('#answers').addClass('answer_visual');
         
         // All answers in the same format -- so use the first one
         // to figure out how to lay things out...

         var a = answers[0];
         var style = (a.text && a.imageUrl) ? 'both' : (a.text ? 'text' : 'image');

         $('#answers').addClass('answer_' + style);

         //if(answers.length > 5 && style != 'text') $('#answers').addClass('answer_smaller');
         
         for (var i = 0; i < answers.length; i++)
         {
            a = answers[i];
            var d = $('<div class="answer">');
            var txt;
            var img;
            
            if (a.text)
            {
               txt = $('<span class="text_info">').text(a.text);
            }

            if (a.imageUrl)
            {
               img = $('<img>').attr('src', a.imageUrl);
               
               if (a.tooltip)
               {
                  img.attr('title', a.tooltip);
               }
            }

            if (txt && img || img)
            {
               d.addClass('question-image');
               d.append(img);
               //d.append(txt);
               if(answers.length <= 4){
                  $("#answers").addClass("answer_2col");
               }else if(answers.length <= 7){
                  $("#answers").addClass("answer_3col");
               }else{
                  $("#answers").addClass("answer_4col");
               }
            }
            else if (txt)
            {
               d.append(txt);
            }
            
            d.data('answerId', a.answerId);

            d.bind('click', function()
            {
               flash_albumart();
               var answerId = $(this).data('answerId');
               var json = {
                        questionId: questionId,
                        answerId: answerId
                     };
                   
               api('question', 'answer', json, function(data)
               {
                  // Update the profile chart

                  if($('#graph-content').length > 0){
                  
                     var newSeries = [];
                     var profiles = data.profiles;
                     var primaryId = data.primaryProfileId;

                     for (var i = 0; i < bargraphProfileIds.length; i++)
                     {
                        var profileId = bargraphProfileIds[i];
                        var score = 0.0;
                        
                        for (var j = 0; j < profiles.length; j++)
                        {
                           var p = profiles[j];
                           
                           if (profileId == p.profileId)
                           {
                              score = p.count;
                              break;
                           }
                        }
                        
                        if (profileId == primaryId)
                        {
                           newSeries.push({_y:score, highlighted:1});
                        }
                        else
                        {
                           newSeries.push({_y:score});
                        }
                     }
                         
                     animGraph(document.getElementById("graph-content"), bargraphSeries, newSeries, 20);
                     bargraphSeries = newSeries;
                         
                     // Did the primary profile change?
                     
                     if (primaryId != primaryProfileId)
                     {
                        primaryProfileId = primaryId;
                        
                        // Update the sidebar
                        
                        var json = {
                           profileId: primaryProfileId
                        };
                        
                        api('profile', 'read', json, function(data)
                        {
                           var name = data.profile.name;
                           var desc = data.profile.description;
                           
                           $('#primary-profile-name').text(name);
                           $('#primary-profile-desc').text(desc);
                        });
                     }
                  }
                     
                  // Have they answered enough starter questions?

                  if (starterQuestion)
                  {
                     if (++starterQuestionsAnswered >= 5)
                     {
                        starterQuestion = false;
                        window.location.reload();
                     }
                     reposition_caret();
                  }
                  
                  loadQuestion(starterQuestion);
               });
                                 
            });
            if(txt || img) $('#answers').append(d);
         }
      }
      else if (type == 3)
      {
         // Yes/No
         // TODO
      }
      else
      {
         // Huh?
      }
   });
}
