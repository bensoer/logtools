<?php
/**
 * Created by PhpStorm.
 * User: bensoer
 * Date: 11/03/16
 * Time: 9:35 AM
 */

//base directory
$directory = "/mnt/DATA/8006FP/Documents/network1/data/fw.var.log.tar/snort/eth1/alerts/";

//create a location to place extracted files
/*if(!is_dir($directory . "extr-secure")){
    mkdir($directory . "extr-secure");
}*/

//get all gz files starting with secure-
$files = glob($directory . "*.gz");
//$files = glob($directory . 'secure');

var_dump($files);

//go through each file extracting and writing out
foreach($files as $file){

    //do some name replacing of where the destination file name and location is
    $dstName = substr($file, 0, strrpos($file, "."));
    $dstName = str_replace("/alerts/", "/alerts/unzipped-alerts/", $dstName);

    print($dstName . "\n");


    $sfp = gzopen($file, "rb");
    $fp = fopen($dstName, "w");

    //read the gzip file in 4Mbyte chunks, then write to new location
    while (!gzeof($sfp)) {
        $string = gzread($sfp, 4096);
        fwrite($fp, $string, strlen($string));
    }
    gzclose($sfp);
    fclose($fp);

}