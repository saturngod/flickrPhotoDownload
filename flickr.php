<?php

$mt = microtime();
$rand = mt_rand();
$oauth_nonce = md5($mt . $rand);
$request_token_url = "https://www.flickr.com/services/oauth/request_token";

$nonce = $oauth_nonce;
$timestamp = gmdate('U'); //It must be UTC time

$cc_key = "d62ac97b3cad4fed3e069c9229c408b1";
$cc_secret = "e18e6aad977f1d58";
$sig_method = "HMAC-SHA1";
$oversion = "1.0";


$basestring = "oauth_callback=".urlencode($callbackURL)."&oauth_consumer_key=".$cc_key."&oauth_nonce=".$nonce."&oauth_signature_method=".$sig_method."&oauth_timestamp=".$timestamp."&oauth_version=".$oversion."&perms=write";
$basestring = "GET&".urlencode($request_token_url)."&".urlencode($basestring);
$hashkey = $cc_secret."&";


$oauth_signature = base64_encode(hash_hmac('sha1', $basestring, $hashkey, true));

$fields = array(
'oauth_nonce'=>$nonce,
'oauth_timestamp'=>$timestamp,
'oauth_consumer_key'=>$cc_key,
'oauth_signature_method'=>$sig_method,
'oauth_version'=>$oversion,
'oauth_signature'=>$oauth_signature,
'oauth_callback' => $callbackURL
);

$fields_string = "";
foreach($fields as $key=>$value) $fields_string .= "$key=".urlencode($value)."&";
$fields_string = rtrim($fields_string,'&');

$url = $request_token_url."?".$fields_string."&perms=write";

$ch = curl_init(); 
$timeout = 60;
curl_setopt ($ch, CURLOPT_URL, $url); 
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$file_contents = curl_exec($ch); 

if (FALSE === $file_contents){
        echo curl_error($ch);
        exit;
}

curl_close($ch); 
parse_str($file_contents,$rsp_arr);

if(isset($rsp_arr["oauth_token"])) {
    
    if (isset($rsp_arr["oauth_token_secret"])) {
        $_SESSION['oauth_token_secret'] = $rsp_arr['oauth_token_secret'];
    }
    echo "<script>window.location.replace(\"https://www.flickr.com/services/oauth/authorize?perms=read&oauth_token=".$rsp_arr["oauth_token"]."\")</script>";


}