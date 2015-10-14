<?php

set_time_limit(0);

// Age questions

$ROOT = '../..';

include_once("$ROOT/api/includes/common.php");

portRefiningQuestions();
portConfirmingQuestions();
portFunQuestions();

function portFunQuestions()
{
   echo "Porting fun questions<br>\n";
   
   $ret = api('fun-question', 'read');
   $questions = $ret->questions;
   
   foreach ($questions as $q)
   {
      echo "Porting: " . json_encode($q) . "<br>\n";

      $profileId = $q->profileId;
      
      $params = array();
      $params['text'] = $q->text;
      $params['questionTypeId'] = 1;   // mutliple-choice
      
      $ret = api('question', 'create', $params);

      echo "question.create = " . json_encode($ret) . "<br>\n";

      $questionId = $ret->questionId;
            
      $ret = api('fun-answer', 'read', array('questionId' => $q->questionId));
      
      echo "run-answer.read = " . json_encode($ret) . "<br>\n";
      
      $funAnswers = $ret->answers;
      
      foreach ($funAnswers as $a)
      {
         $text = $a->text;
         $answerTypeId = $a->answerTypeId;

         if ($answerTypeId == 1)
            $text = "YES";
         else if ($answerTypeId == 2)
            $text = "NO";
         else if ($answerTypeId == 4)
            $text = "YES";
         else if ($answerTypeId == 5)
            $text = "NO";
         
         echo "creating answer: $text<br>\n";
         
         $ret = api('answer', 'create', array('questionId' => $questionId, 'text' => $text));
      
         echo "answer.create = " . json_encode($ret) . "<br>\n";
      }
               
      echo "<br><hr><br>\n";
   }
}

function portConfirmingQuestions()
{
   echo "Porting confirming questions<br>\n";
   
   $ret = api('confirming-question', 'read');
   $questions = $ret->questions;
   
   foreach ($questions as $q)
   {
      echo "Porting: " . json_encode($q) . "<br>\n";

      $profileId = $q->profileId;
      
      $params = array();
      $params['text'] = $q->text;
      $params['questionTypeId'] = 1;   // mutliple-choice
      
      $ret = api('question', 'create', $params);

      echo "question.create = " . json_encode($ret) . "<br>\n";

      $questionId = $ret->questionId;
            
      $ret = api('confirming-answer', 'read', array('questionId' => $q->questionId));
      
      echo "confirming-answer.read = " . json_encode($ret) . "<br>\n";
      
      $confirmingAnswers = $ret->answers;
      
      foreach ($confirmingAnswers as $a)
      {
         $text = $a->text;
         $confirms = $a->confirms;
         
         echo "creating answer: $a->text<br>\n";
         
         $ret = api('answer', 'create', array('questionId' => $questionId, 'text' => $text));
      
         echo "answer.create = " . json_encode($ret) . "<br>\n";
   
         $answerId = $ret->answerId;
         
         // Assign profile to the answer
         
         if ($confirms)
         {
            echo "assigning profile: $profileId<br>\n";
         
            $ret = api('answer-profile', 'create', array('answerId' => $answerId, 'profileId' => $profileId));

            echo "answer-profile.create = " . json_encode($ret) . "<br>\n";
         }
      }      
         
      echo "<br><hr><br>\n";
   }
}

function portRefiningQuestions()
{
   $ret = api('refining-question', 'read');
   $questions = $ret->questions;
   
   foreach ($questions as $q)
   {
      echo "Porting: " . json_encode($q) . "<br>\n";
      
      $params = array();
      $params['text'] = $q->text;
      $params['questionTypeId'] = 1;   // mutliple-choice
      
      $ret = api('question', 'create', $params);
      
      $questionId = $ret->questionId;
      
      echo "question.create = " . json_encode($ret) . "<br>\n";
      
      $ret = api('refining-answer', 'read', array('questionId' => $q->questionId));
      
      echo "refining-answer.read = " . json_encode($ret) . "<br>\n";
      
      $refiningAnswers = $ret->answers;
      
      // Build a list of unique answers
      
      $uniqueAnswers = array();
      
      foreach ($refiningAnswers as $answer)
      {
         $profileId = $answer->profileId;
         $text = $answer->answer;
      
         if (!in_array($text, $uniqueAnswers))
         {
            $uniqueAnswers[] = $text;
         }   
      }
   
      // Create answers
      
      foreach ($uniqueAnswers as $a)
      {
         echo "creating answer: $a<br>\n";
         
         $ret = api('answer', 'create', array('questionId' => $questionId, 'text' => $a));
      
         echo "answer.create = " . json_encode($ret) . "<br>\n";
   
         $answerId = $ret->answerId;
         
         // Assign each profile to the answer
         
         foreach ($refiningAnswers as $answer)
         {
            $profileId = $answer->profileId;
            $text = $answer->answer;
            
            if ($text != $a)
               continue;
   
            echo "assigning profile: $profileId<br>\n";
            
            $ret = api('answer-profile', 'create', array('answerId' => $answerId, 'profileId' => $profileId));
   
            echo "answer-profile.create = " . json_encode($ret) . "<br>\n";
         }
      }
      
      echo "<br><hr><br>\n";
   }
}

?>