<?php


if(isset($_GET['oauth_token'])) {
    $oauth_token = $_GET['oauth_token'];
    $oauth_verifier = $_GET['oauth_verifier'];

$mt = microtime();
$rand = mt_rand();
$oauth_nonce = md5($mt . $rand);
$nonce = $oauth_nonce;
$timestamp = gmdate('U'); //It must be UTC time
$request_token_url = "https://www.flickr.com/services/oauth/access_token";

$oauth_token_secret = $_SESSION['oauth_token_secret'];

    
    $sig_method = "HMAC-SHA1";
    $oversion = "1.0";

    $basestring = "oauth_consumer_key=$cc_key&oauth_nonce=$oauth_nonce&oauth_signature_method=HMAC-SHA1&oauth_timestamp=$timestamp&oauth_token=$oauth_token&oauth_verifier=$oauth_verifier&oauth_version=$oversion";
    $basestring = "GET&".urlencode($request_token_url)."&".urlencode($basestring);
    $hashkey = $cc_secret."&".$oauth_token_secret;
    

    $oauth_signature = base64_encode(hash_hmac('sha1', $basestring, $hashkey, true));
    


    $fields = array(
    'oauth_nonce'=>$nonce,
    'oauth_timestamp'=>$timestamp,
    'oauth_verifier'=>$oauth_verifier,
    'oauth_consumer_key'=>$cc_key,
    'oauth_signature_method'=>$sig_method,
    'oauth_version'=>$oversion,
    'oauth_token' =>$oauth_token,
    'oauth_signature'=>$oauth_signature
    );

    $fields_string = "";
    foreach($fields as $key=>$value) $fields_string .= "$key=".urlencode($value)."&";
    $fields_string = rtrim($fields_string,'&');

    $url = $request_token_url."?".$fields_string;
    $ch = curl_init(); 
    $timeout = 5;


    curl_setopt ($ch, CURLOPT_URL, $url); 
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $file_contents = curl_exec($ch); 
    curl_close($ch); 
    

    

    parse_str($file_contents,$rsp_arr);

    if(isset($rsp_arr["oauth_token"])) {
        $_SESSION['oauth_token'] = $rsp_arr["oauth_token"];
        $_SESSION['oauth_token_secret'] = $rsp_arr["oauth_token_secret"];

        
        echo "O_TOKEN: ".$_SESSION['oauth_token']."<br/>";
        echo "O_SECRECT: ".$_SESSION['oauth_token_secret']."<br/>";
    }
    else {
        echo $file_contents;
    }



}