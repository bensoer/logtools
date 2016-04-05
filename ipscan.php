<?php
/**
 * Created by PhpStorm.
 * User: bensoer
 * Date: 11/03/16
 * Time: 10:16 AM
 */

//cuz these files are massive. we can manually override php's memory allocation limit
ini_set('memory_limit', '1024M');

$directory = "/mnt/DATA/8006FP/network3/walrus/log/securelogs";

$files = scandir($directory);

$knownIPs = Array();

//$file = "/secure";
foreach($files as $file) {

    print(" -- Opening File: $file\n");
    $fileContent = file($directory . "/" . $file);

    foreach ($fileContent as $entry) {

        $matches = Array();
        if (preg_match('/\\s(\\d{1}|\\d{2}|\\d{3})\\.(\\d{1}|\\d{2}|\\d{3})\\.(\\d{1}|\\d{2}|\\d{3})\\.(\\d{1}|\\d{2}|\\d{3})\\s/', $entry, $matches)) {


            $mergedMatch = "$matches[1].$matches[2].$matches[3].$matches[4]";
            if(!isKnownIP($knownIPs, $mergedMatch)){
                print("$mergedMatch \n");
                $knownIPs[] = $mergedMatch;
            }
        }
    }
}


function isKnownIP($knownIPs, $ip){
    foreach($knownIPs as $IP){
        if(strcmp($ip, $IP)==0){
            return true;
        }
    }

    return false;
}