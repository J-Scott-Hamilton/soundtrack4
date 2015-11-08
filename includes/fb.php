<?php

$facebook_scope = 'email,publish_actions';

// Soundtrack4 app ids (Oct 2015)
$facebook_app_id = '181217025548582';
$facebook_app_secret = '242a2311289ac51892c49b1007cbde23';
$facebook_redirect_uri = 'http://soundtrack4.com/login.php';

if(getenv("ST4_ENVIRONMENT") == "dev"){
    $facebook_app_id = '1507569566203089';
    $facebook_app_secret = '60451698bae7254b808231dbe63f3069';
    $facebook_redirect_uri = 'http://soundtrack4.dev/login.php';
}

// Common

require_once __DIR__ . '/facebook/facebook.php';

$facebook = new Facebook(array(
  'appId'  => $facebook_app_id,
  'secret' => $facebook_app_secret,
  'cookie' => true
));

$facebookUser = $facebook->getUser();

?>
