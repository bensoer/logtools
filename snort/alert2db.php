<?php
/**
 * Created by PhpStorm.
 * User: bensoer
 * Date: 18/03/16
 * Time: 5:16 PM
 */

require_once('./medoo/medoo.php');

//cuz these files are massive. we can manually override php's memory allocation limit. its now unlimited lul
ini_set('memory_limit', -1);


function parseCollection(Array $collection){

    //var_dump($collection);

    $fullEntry = implode(" ", $collection);
    $fullEntryRespaced = str_replace("  ", " ", $fullEntry);
    $fullEntryLowerd = strtolower($fullEntryRespaced);

    $explodedEntry = explode(" ", $fullEntryLowerd);

    //find the time
    //find the error message
    $matches = array();
    $snortstamp = "";
    $errMessage = "";
    if(preg_match("/\\[(\\d*:\\d*:\\d*)\\]\\s((\\w*|\\s*|-|<|%|:|\\+|\\*|\\/|\\(|\\)|\\.)*)\\s\\[\\*\\*\\]/", $fullEntryLowerd, $matches)){

        //var_dump($matches);

        $snortstamp = $matches[1];
        $errMessage = $matches[2];
    }else{

        print("Failed To Parse Out Snortstamp and Error Message From: \n $fullEntryLowerd \n");
        var_dump($matches);
    }

    $matches2 = array();
    $classification = "";
    if(preg_match("/\\[classification: ((\\w*|\\s*|-)*)\\]/", $fullEntryLowerd, $matches2)){

        //print("CLASSIFICATION OUT: \n");
        //var_dump($matches2);
        $classification = $matches2[1];
    }else{
        print("Failed To Parse Out Classification From: \n $fullEntryLowerd \n");
    }

    $matches3 = array();
    $priority = 0;
    if(preg_match("/\\[priority: (\\d{1})\\]/", $fullEntryLowerd, $matches3)){
        //var_dump($matches3);
        $priority = (int)$matches3[1];
    }else{
        print("Failed To Parse Out Priority From: \n $fullEntryLowerd \n");
    }

    $matches4 = array();
    $timestamp = "";
    if(preg_match("/\\s(\\d{2}\\/\\d{2}-\\d{2}:\\d{2}:\\d{2}\\.\\d*)\\s/", $fullEntryLowerd, $matches4)){
        //var_dump($matches4);
        $timestamp = $matches4[1];
    }else{
        print("Failed To Parse Out Timestamp From: \n $fullEntryLowerd \n");
    }

    //match src and dest IPs
    $matches5 = array();
    $sourceIP = "";
    $destinationIP = "";
    if(preg_match("/(\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}(:\\d{1,5})?)\\s->\\s(\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}\\.\\d{1,3}(:\\d{1,5})?)/", $fullEntryLowerd, $matches5)){
        //var_dump($matches5);

        $sourceIP = $matches5[1];
        $destinationIP = $matches5[3];

        if(strlen($sourceIP) == 0 || strlen($destinationIP) == 0 || $sourceIP == " " || $destinationIP == " " ){
            print("ERROR PARSING FIELDS PROPERLY\n");
            var_dump($matches5);
            print("$fullEntryLowerd \n");
        }
    }else{
        $matches5 = array();
        //try to see if this is ipv6 address
        if(preg_match("/((\\w{1,5}:{1,2})+\\w{1,5}|::)\\s->\\s((\\w{1,5}:{1,2})+\\w{1,5}|::)/", $fullEntryLowerd, $matches5)){
            //var_dump($matches5);

            $sourceIP = $matches5[1];
            $destinationIP = $matches5[3];
        }else{
            print("Failed To Parse Out Src and Dest IP Address From: \n $fullEntryLowerd \n");
        }
    }

    //find protocol
    $matches6 = array();
    $protocol = "";
    if(preg_match("/\\w*-?(icmp|tcp|udp|UDP|TCP|nonxt)|proto:\\d*/", $fullEntryLowerd, $matches6)){
        //var_dump($matches6);
        $protocol = $matches6[0];

    }else{
        print("Failed To Parse Out Protocol From: \n $fullEntryLowerd \n");
    }

    //find ttl
    $matches7 = array();
    $ttl = "";
    if(preg_match("/ttl:(\\d*)\\s/", $fullEntryLowerd, $matches7)){
        //var_dump($matches7);
        $ttl = (int)$matches7[1];
    }else{
        print("Failed To Parse Out TTL From: \n $fullEntryLowerd \n");
    }

    //find tos
    $matches8 = array();
    $tos = "";
    if(preg_match("/tos:(\\w*)\\s/", $fullEntryLowerd, $matches8)){
        //var_dump($matches8);
        $tos = $matches8[1];

    }else{
        print("Failed To Parse Out TOS From: \n $fullEntryLowerd \n");
    }

    //find id
    $matches9 = array();
    $id = "";
    if(preg_match("/id:(\\d*)\\s/", $fullEntryLowerd, $matches9)){
        $id = $matches9[1];
    }else{
        print("Failed To Parse Out ID From: \n $fullEntryLowerd \n");
    }

    //find iplength
    $matches10 = array();
    $iplength = 0;
    if(preg_match("/iplen:(\\d*)\\s/", $fullEntryLowerd, $matches10)){
        $iplength = (int)$matches10[1];
    }else{
        print("Failed To Parse Out IP Length From: \n $fullEntryLowerd \n");
    }

    //find datagramlength
    $matches11 = array();
    $datagramlength = 0;
    if(preg_match("/dgmlen:(\\d*)\\s?/", $fullEntryLowerd, $matches11)){
        //var_dump($matches11);
        $datagramlength = (int)$matches11[1];
    }else{
        print("Failed To Parse Out Datagram Length From: \n $fullEntryLowerd \n");
    }


    return Array(
        'snortstamp' => $snortstamp,
        'errmessage' => $errMessage,
        'classification' => $classification,
        'priority' => $priority, //int
        'timestamp' => $timestamp,
        'sourceip' => $sourceIP,
        'destip' => $destinationIP,
        'protocol' => $protocol,
        'ttl' => $ttl,
        'tos' => $tos,
        'packetid' => $id,
        'iplength' => $iplength, //int
        'datagramlength' => $datagramlength //int
    );
}



$directory = "/mnt/DATA/8006FP/Documents/network2/IDS/snort/alerts";

$database=  new medoo([
    'database_type' => 'mysql',
    'database_name' => 'net2snort',
    'server' => 'localhost',
    'username' => 'root',
    'password' => 'password',
    'charset' => 'utf-8'
]);

$files = glob($directory . "/alert*");

print("Fetched Files:\n");
var_dump($files);

print("There Are " . count($files) . " files to be read. This means there will be " . count($files) . " processes made for efficiency \n");

$children = array();

foreach($files as $file){

    //$pid = pcntl_fork();
    //if($pid == -1){
    //    die("Forking Failure");
    //}else if($pid){
        //we are the parent

    //    $children[] = $pid;

    //}else{
        //we are the child

        print(getmypid() . " - Now Reading File: $file\n");

        $contents = file($file, FILE_IGNORE_NEW_LINES);

        $previousSpace = 0;
        $currentSpace = 0;

        //$insertingData = Array();

        $collection = Array();
        foreach($contents as $lineNum => $row){



            if($row == ""){

                $currentSpace = $lineNum;

                //do what you want
                $dbEntry = parseCollection($collection);
                $database->insert('snortentry', $dbEntry);

                //$insertingData[] = $dbEntry;

                //then clear the collection
                $collection = Array();
                $previousSpace = $currentSpace;

            }else{
                $collection[] = $row;
            }

        }

        //print(getmypid() . " - Now Loading Entries Into Database\n");
        //$database->insert('snortentry', $insertingData);

        //child need not continue after sorting its file
        //break;

        unset($contents);
    //}
}
/*
foreach($children as $child){
    print("Waiting on Child $child\n");

    $status = 0;
    pcntl_wait($child, $status);

    print("Child $child has returned\n");
}

*/



