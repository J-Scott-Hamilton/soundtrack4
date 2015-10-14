<?php

session_start();

require_once __DIR__ . '/includes/api.php';
require_once __DIR__ . '/includes/fb.php';


if ($facebookUser) 
{
   // Do they have an ST4 account yet?
   
   $accessToken = $facebook->getAccessToken();
   
   $ret = api('session', 'create', array('facebookAccessToken' => $accessToken));
   
   if ($ret->result)
   {
      $_SESSION['ST4'] = $ret;

      // Store access token for future logins

      setcookie('st4lt', 'fb', time()+31536000, '/');
      setcookie('st4fat', $accessToken, time()+31536000, '/');

      // Logged in -- redirect to home page or whatever their destination is

      header('Location:http://' . $_SERVER['HTTP_HOST']);
      exit();
   }
   
   // Create an account
   
   $facebookProfile = $facebook->api('/me');
   $facebookId = $facebookProfile['id'];
      
   $params = array(
               'firstName' => $facebookProfile['first_name'],
               'facebookId' => $facebookId,
               'email' => $facebookProfile['email']);
   
   if (isset($facebookProfile['birthday']))
   {
      $birthday = $facebookProfile['birthday'];
      
      if (strlen($birthday))
      {
         $birthday = date('c', strtotime($birthday));
         $params['birthday'] = $birthday;
      }
   }
               
   if (isset($facebookProfile['gender']))
   {
      $params['gender'] = ($facebookProfile['gender'] == "male") ? "M" : "F";
   }
          
   $ret = api('account', 'create', $params);

   if ($ret->result)
   {
      // Log them in

      $ret = api('session', 'create', array('facebookAccessToken' => $accessToken));
         
      $_SESSION['ST4'] = $st4Session = $ret;
            
      setcookie('st4lt', 'fb', time()+31536000, '/');
      setcookie('st4fat', $accessToken, time()+31536000, '/');
                                          
      // Update their profile guesses based on what we know about them...
      // What age cohort are they in?

      $ageCohortId = null;

      if ($facebookProfile['birthday'])
      {
         $parts = explode("/", $facebookProfile['birthday']);                  
         $age = (date("md", date("U", mktime(0, 0, 0, $parts[0], $parts[1], $parts[2]))) > date("md") ? ((date("Y")-$parts[2])-1):(date("Y")-$parts[2]));
      }   
      
      // Given age and gender, put them in some profile buckets
   
      if (isset($age) || isset($gender))
      {
         $params = array();
         
         if (isset($age))
            $params['age'] = $age;
         
         if (isset($gender))
            $params['gender'] = $gender;
         
         $ret = api('profile', 'read', $params);
         
         foreach ($ret->profiles as $profile)
         {
            $profileId = $profile->profileId;
            
            $ret = api('account-profile', 'create', array(
                                                      'profileId' => $profileId,
                                                      'weight' => 100));
         }
      }            
      
      // And assign appropriate age and/or gender tags
      
      if ($age)
      {
         $tagChoiceId = 0;

         // TODO: Don't hard-code ids
         //	tagChoiceId =  9, tagId = 1, value = < 20
         //	tagChoiceId = 10, tagId = 1, value = 20 - 30
         //	tagChoiceId = 11, tagId = 1, value = 31 - 40
         //	tagChoiceId = 12, tagId = 1, value = 41 - 50
         //	tagChoiceId = 13, tagId = 1, value = 51 - 60
         //	tagChoiceId = 14, tagId = 1, value = > 60
         
         if ($age < 20)
            $tagChoiceId = 9;
         else if ($age < 31)
            $tagChoiceId = 10;
         else if ($age < 41)
            $tagChoiceId = 11;
         else if ($age < 51)
            $tagChoiceId = 12;
         else if ($age < 61)
            $tagChoiceId = 13;
         else
            $tagChoiceId = 14;

         $ret = api('account-tag', 'create', array(
                                                'tagId' => 1,
                                                'tagChoiceId' => $tagChoiceId));
      }

      if ($gender)
      {
         // TODO: Don't hard-code ids
         //	tagChoiceId = 7, tagId = 2, value = Male
         //	tagChoiceId = 8, tagId = 2, value = Female

         $ret = api('account-tag', 'create', array(
                                                'tagId' => 2,
                                                'tagChoiceId' => ($gender == "F") ? 8 : 7));
      }
   }
   
   header('Location: http://' . $_SERVER['HTTP_HOST']);
   exit();
}else{
   $facebook_scope = 'offline_access,email,publish_stream';
   $params = array(
      'scope' => $facebook_scope,
      'redirect_uri' => 'http://soundtrack4.com/login.php'
   );
   $newurl = $facebook->getLoginUrl($params);
   header('Location: ' + $newurl);

}
var_dump($_SESSION);
var_dump($_REQUEST);
?>

<!-- TOOD: If we didn't redirect above, sometihng went wrong -->
