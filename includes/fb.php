<?php

$facebook_scope = 'offline_access,email,publish_stream';

// Live

$facebook_app_id = '190267894396401';
$facebook_app_secret = '971aa6ec3ea19ae228be852899e9b19c';

// Beta

$facebook_app_id = '309365045842756';
$facebook_app_secret = '3411d924132986b5858528589b92df03';

// Common

require_once __DIR__ . '/facebook/facebook.php';

$facebook = new Facebook(array(
  'appId'  => $facebook_app_id,
  'secret' => $facebook_app_secret,
  'cookie' => true
));

$facebookUser = $facebook->getUser();

?>
