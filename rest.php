<?php

function rest($method,$arg="",$oToken="",$oauth_token_secret="") {

    $rest_url = "https://api.flickr.com/services/rest";

    $mt = microtime();
    $rand = mt_rand();
    $oauth_nonce = md5($mt . $rand);
    $timestamp = gmdate('U'); //It must be UTC time

    $cc_key = "d62ac97b3cad4fed3e069c9229c408b1";
    $cc_secret = "e18e6aad977f1d58";
    $sig_method = "HMAC-SHA1";
    $oversion = "1.0";
    if($oToken == "") {
        $oToken = $_SESSION['oauth_token'];
    }

    if($oauth_token_secret == "") {
        $oauth_token_secret = $_SESSION['oauth_token_secret'];
    }


    $basestring = "format=json&method=$method&nojsoncallback=1&oauth_consumer_key=$cc_key&oauth_nonce=$oauth_nonce&oauth_signature_method=HMAC-SHA1&oauth_timestamp=$timestamp&oauth_token=$oToken&oauth_version=$oversion";

    
    $basestring = "GET&".urlencode($rest_url)."&".urlencode($basestring);
    $hashkey = $cc_secret."&".$oauth_token_secret;

    $oauth_signature = base64_encode(hash_hmac('sha1', $basestring, $hashkey, true));
    
    // echo $basestring;
    // echo "\n";
    // echo "========";
    // echo "\n";
    
    $fields = array(
    "nojsoncallback" => 1,
    "oauth_nonce" => $oauth_nonce,
    "format" => "json",
    "oauth_consumer_key" => $cc_key,
    "oauth_timestamp" => $timestamp,
    "oauth_signature_method" => "HMAC-SHA1",
    "oauth_version" => $oversion,
    "oauth_token" => $oToken,
    "oauth_signature" => $oauth_signature,
    "method" => $method
    );

    $fields_string = "";
    foreach($fields as $key=>$value) $fields_string .= "$key=".urlencode($value)."&";
    $fields_string = rtrim($fields_string,'&');

    $url = $rest_url."?".$fields_string."&".$arg;

    $ch = curl_init(); 
    $timeout = 5;


    curl_setopt ($ch, CURLOPT_URL, $url); 
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $file_contents = curl_exec($ch); 
    curl_close($ch); 
    

    // echo $file_contents;
    return json_decode($file_contents,true);

}