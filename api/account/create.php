<?php

require_once("../includes/db.php");

$json = array('result' => false);

try
{
   $db = db_connect();
   $params = json_decode(file_get_contents('php://input'));
   
   $fields = array();

   $fields['email'] = as_db_string($params->email);
   $fields['first_name'] = as_db_string($params->firstName); 
   $fields['birthday'] = as_db_string($params->birthday);
   $fields['gender'] = as_db_string($params->gender);
   $fields['facebook_id'] = $params->facebookId;

   if (isset($params->password)) {
      $fields['password'] = as_db_string($params->password);
   }
   
   $sql = as_db_insert("account", $fields);
   
   $rows = mysql_query($sql);
   $accountId = mysql_insert_id();

   if ($accountId > 0)
   {
      // Send email verification email
      /*
      $emailConfirmationLink = 'http://' . $_SERVER['HTTP_HOST'] . '/account/confirm_email/';
      $emailConfirmationCode = '1234567890'; // TODO
      
      $email = $params->email;
      $firstName = $params->firstName;
      
      $to = $email;

      $headers  = 'From: noreply@voodoovox.com' . "\r\n";
      $headers .= 'Reply-To: noreply@voodoovox.com' . "\r\n";
      $headers .= 'MIME-Version: 1.0' . "\r\n";
      $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
      
      $subject = 'Confirm your SoundTrack4 Account';

      $message = "
         <html>
         <body>
            <p>Hi $firstName,</p>
            <p>
               Please confirm your SoundTrack4 account by clicking this link:<br>
               $emailConfirmationLink/$emailConfirmationCode
            </p>
            <p>Once you confirm, you will have full access to SoundTrack4 and all future notifications will be sent to this email address.</p>
            <p>Please do not reply to this message -- it was sent from an unmonitored email address.</p>
            <p>The SoundTrack4 Team</p>
         </body>
         </html>
      ";

      $json['confirmEmailSent'] = mail($to, $subject, $message, $headers);
      */
      
   	//$json['accountId'] = $accountId;
      $json['result'] = true;
   }
   else
   {
      // Duplicate email?
    
      $email = $params->email;
      
      $sql = "SELECT * FROM account WHERE email = '$email'";
      $rows = mysql_query($sql);

      if (mysql_num_rows($rows) > 0)
      {
         $json['reason'] = 'duplicate-email';
      }
   }
   
   db_close($db);
}
catch (Exception $e)
{
   $json['reason'] = $e->getMessage();
}  

echo json_encode($json);

?>
