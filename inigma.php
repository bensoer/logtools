<?php
/**
 * Created by PhpStorm.
 * User: bensoer
 * Date: 11/03/16
 * Time: 9:57 AM
 */

//cuz these files are massive. we can manually override php's memory allocation limit
ini_set('memory_limit', '1024M');

$directory = "/home/bensoer/Documents/network1/data/fw.var.log.tar/log/extr-secure/";

$files = scandir($directory);

foreach($files as $file){

    print(" -- Opening File: $file\n");
    $fileContent = file($directory . $file);

    foreach($fileContent as $entry){

        if(strpos($entry, "24.85.119.149")){

            print("$entry \n");

        }
    }




}

