<?php

if (!isset($ROOT)) {
   $ROOT = '.';
}

$SITEROOT = 'http://' . $_SERVER['HTTP_HOST'];
$APIROOT = 'http://' . $_SERVER['HTTP_HOST'] . '/api';

$companyName = "SoundTrack 4";
$companyNameLLC = "$companyName, LLC";
$domainName = "soundtrack4.com";
$productName = "SoundTrack 4";

//require_once("$ROOT/includes/session.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
<head>
<title><?php echo $PAGETITLE?></title>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="title" content="<?php echo $PAGETITLE?>" />
<meta name="language" content="en" />

<meta name="robots" content="noindex">
<meta name="googlebot" content="noindex">

<link rel="stylesheet" type="text/css" href="/admin/includes/admin.css" />
<link rel="stylesheet" type="text/css" href="/admin/includes/main.css" />

<script src="/js/vendor/jquery.min.js"></script>
<!--script src="/includes/jquery-cookies.js"></script-->
