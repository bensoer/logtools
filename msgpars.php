<?php
/**
 * Created by PhpStorm.
 * User: bensoer
 * Date: 11/03/16
 * Time: 9:35 AM
 */

$directory = "/home/bensoer/Documents/network1/data/fw.var.log.tar/log/";

if(!is_dir($directory . "extr-message")){
    mkdir($directory . "extr-message");
}

$files = glob($directory . "messages-*.gz");
//$files = glob($directory . 'secure');

var_dump($files);

foreach($files as $file){

    $dstName = substr($file, 0, strrpos($file, "."));
    $dstName = str_replace("/log/", "/log/extr-message/", $dstName);

    print($dstName . "\n");

    $sfp = gzopen($file, "rb");
    $fp = fopen($dstName, "w");

    while (!gzeof($sfp)) {
        $string = gzread($sfp, 4096);
        fwrite($fp, $string, strlen($string));
    }
    gzclose($sfp);
    fclose($fp);

}