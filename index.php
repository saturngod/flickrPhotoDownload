<?php
session_start();

$callbackURL = "http://localhost/flickr_download/index.php";

//get key and secrect at https://www.flickr.com/services/apps/create/
$cc_key = "API KEY";
$cc_secret = "API SECRECT";

if(isset($_GET['oauth_token'])) {
    require_once('oauthtoken.php');
}
else {
    require_once('flickr.php');
}