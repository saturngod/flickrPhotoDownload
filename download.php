<?php


require_once("rest.php");

$photo_id = array();
$user_id = "";
define("O_TOKEN","ENTER_O_TOKEN");
define("O_SECRECT","ENTER_O_SECRECT");
define("DOWNLOAD_FOLDER",dirname(__FILE__) . "/download/");



if (O_TOKEN == "ENTER_O_TOKEN" || O_SECRECT == "ENTER_O_SECRECT") {
    echo "UPDATE O_TOKEN AND O_SECRECT\n";
    exit;
}

echo "Testing Token...\n";


$login = rest("flickr.test.login","",O_TOKEN,O_SECRECT);




if(isset($login["user"])) {
    $user = $login["user"];
    $user_id = $user["id"];
    

    
    if (!file_exists(DOWNLOAD_FOLDER)) {
        mkdir(DOWNLOAD_FOLDER);
    }
   photos_id();

    
}
    

function download_photo($id) {

    
     $arg = "photo_id=".$id;
    
    
    $photoInfo = rest("flickr.photos.getInfo",$arg,O_TOKEN,O_SECRECT);

    if (isset($photoInfo["photo"])) {
        $photo = $photoInfo["photo"];
        $pid = $photo["id"];
        $server = $photo["server"];
        $originalsecret = $photo["originalsecret"];
        $ext = $photo["originalformat"];

        $obj = [];
        $obj["name"] = $pid."_".$originalsecret."_o_d.".$ext;
        $obj["url"] = "https://farm8.staticflickr.com/".$server."/".$obj["name"];
        return $obj;
    }
    else {
        return "";
    }
}

function download_file($url,$name) {
    set_time_limit(0);
    //This is the file where we save the    information

    $fp = fopen (DOWNLOAD_FOLDER.$name, 'w+');
    //Here is the file we are downloading, replace spaces with %20
    $ch = curl_init(str_replace(" ","%20",$url));
    curl_setopt($ch, CURLOPT_TIMEOUT, 50);
    // write curl response to file
    curl_setopt($ch, CURLOPT_FILE, $fp); 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    // get curl response
    curl_exec($ch); 
    curl_close($ch);
    fclose($fp);
}

function photos_id($page=1) {
    global $user_id;
    global $photo_id;
    echo "PAGE IS : ".$page;
    echo "\n";

    sleep(1);

    $arg = "page=$page&content_type=6&per_page=500&user_id=".$user_id;
    
    
    $photosStream = rest("flickr.people.getPhotos",$arg,O_TOKEN,O_SECRECT);
    
    if(isset($photosStream["photos"])) {
        $photos = $photosStream["photos"];
        $page = intval($photos["page"]);
        $totalpages  = intval($photos["pages"]);

        $photoList = $photos["photo"];
        
        foreach ($photoList as $ph) {
            
            $photoObj = download_photo($ph["id"]);
            
            if($photoObj != "") {
                //array_push($photo_id,$url);
                echo "Downloading... \n".$photoObj["url"]."\n";
                download_file($photoObj["url"],$photoObj["name"]);
                echo "\n";
            }

            sleep(1);
        } 

        if ($page != $totalpages) {
            $page = $page + 1;
            echo "Go to Next page : $page";
            echo "\n";

            sleep(2);
        
            photos_id($page);
        }
    }
}