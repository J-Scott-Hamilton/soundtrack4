<?php
# Based on docs from: https://packagist.org/packages/adam-paterson/oauth2-rdio

session_start();

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . '/keys.php';

# create an instance of the Rdio object with our consumer credentials
$rdio = new \AdamPaterson\OAuth2\Client\Provider\Rdio([
    'clientId'          => RDIO_CONSUMER_KEY,
    'clientSecret'      => RDIO_CONSUMER_SECRET,
    'redirectUri'       => RDIO_REDIRECT_URL,
]);

# work out what our current URL is
$current_url = "http" . ((!empty($_SERVER['HTTPS'])) ? "s" : "") .
  "://" . $_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'];

if (@$_GET['logout']) {
  # to log out, just throw away the session data
  session_destroy();
  # and start again
  header('Location: '.$current_url);
  exit;
}

if (!isset($_GET['code'])) 
{
   // If we don't have an authorization code then get one
    $authUrl = $rdio->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $rdio->getState();
    header('Location: '.$authUrl);
    exit;
    } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    // Try to get an access token (using the authorization code grant)
    $token = $rdio->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // Optional: Now you have a token you can look up a users profile data
    try {

        // We got an access token, let's now get the user's details
        $user = $rdio->getResourceOwner($token);

        // Use these details to create a new profile
        printf('Hello %s!', $user->getFirstName() . '' . $user->getLastName());

    } catch (Exception $e) {

         # auth failure, clear session
   
      session_destroy();

      # and start again
   
      header('Location: '.$current_url);
    }

    // Use this to interact with an API on the users behalf
    echo $token->getToken();
}
  

?>
