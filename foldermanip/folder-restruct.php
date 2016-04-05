<?php
/**
 * Created by PhpStorm.
 * User: bensoer
 * Date: 26/03/16
 * Time: 9:14 PM
 */

$directory = "/mnt/DATA/8006FP/Documents/network1/data/fw.var.log.tar/snort/eth0/pcaps";

$files = glob($directory . "/*");

print("Fetch Files: \n");
var_dump($files);

//now for each file create a folder with the same end tag name and put it into it

foreach($files as $file){

    $folderName = substr($file, strrpos($file, '.')+1, strlen($file));
    print("FolderName: $folderName\n");

    mkdir($directory . "/" . $folderName, 0777, true);

    $lastSlashIndex = strrpos($file, '/');
    $filePreLastSlash = substr($file, 0, $lastSlashIndex);

    //$newFolderPath = str_replace($file, "snort.log.$folderName", "$folderName/snort.log.$folderName");
    $newFolderPath = "$filePreLastSlash/$folderName/snort.log.$folderName";

    print("Old Folder Path: $file \n");
    print("New Folder PAth: $newFolderPath \n");

    rename($file, $newFolderPath);

}