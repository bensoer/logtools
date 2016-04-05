<?php
/**
 * Created by PhpStorm.
 * User: bensoer
 * Date: 11/03/16
 * Time: 12:11 PM
 */

$directory = "/home/bensoer/Documents/network2/IDS/snort/";

$files = glob($directory . "*.bz2");

foreach($files as $file){

    print("Extracting $file \n");

    $foutName = substr($file, 0, strrpos($file,"."));

    print("Output File: $foutName \n");


    $bzfp = bzopen($file, "r");
    $fp = fopen($foutName, "w");

    while(!feof($bzfp)){
        $decompressedFile = bzread($bzfp);
        if($decompressedFile === FALSE) die('Read problem');
        if(bzerrno($bzfp) !== 0) die('Compression Problem');
        fwrite($fp, $decompressedFile);
    }

    bzclose($bzfp);
    fclose($fp);

}