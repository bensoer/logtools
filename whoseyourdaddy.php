<?php
/**
 * Created by PhpStorm.
 * User: bensoer
 * Date: 11/03/16
 * Time: 10:46 AM
 */

//cuz these files are massive. we can manually override php's memory allocation limit
ini_set('memory_limit', '1024M');

$directory = "/mnt/DATA/8006FP/network3/walrus/log/securelogs";

$uniqueips = file($directory . "/uniqueips", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$fixedips = Array();
foreach($uniqueips as $ip){
    $fixedip = str_replace(" ", "", $ip);
    $fixedips[] = $fixedip;
}
$files = glob($directory . "/secure*");

var_dump($files);

$fp = fopen($directory . "/violations", "w");
$tfp = fopen($directory . "/vstattotals", "w");

$ipsWhoViolated = 0;
$ipsWhoSSHViolated = 0;
$ipsWhoFTPViolated = 0;
$ipsWhoAcceptedPublicKeys = 0;
$ipsWhoClosedAConnection = 0;

foreach($fixedips as $ip){

    $hasViolated = false;
    $hasSSHViolated = false;
    $hasFTPViolated = false;
    $hasAcceptedPublicKey = false;
    $hasClosedAConnection = false;

    print("Checking Violations of IP >$ip< \n");
    fwrite($fp, "Checking violations of IP >$ip< \n");
    fwrite($tfp, "Checking violations of IP >$ip< \n");
    $totalViolations = 0;
    $totalFTP = 0;
    $totalSSH = 0;
    $totalAcceptedPublicKeys = 0;
    $totalConnectionsClosed = 0;

    foreach($files as $file){

        print("Checking Violations in File $file \n");
        fwrite($fp, "Checking Violations in File $file \n");

        $content = file($file);

        foreach($content as $entry){

            $lowercaseEntry = strtolower($entry);

            if (preg_match('/\\s(\\d{1}|\\d{2}|\\d{3})\\.(\\d{1}|\\d{2}|\\d{3})\\.(\\d{1}|\\d{2}|\\d{3})\\.(\\d{1}|\\d{2}|\\d{3})\\s/', $entry, $matches)) {
                $mergedMatch = "$matches[1].$matches[2].$matches[3].$matches[4]";

                if((strcmp($mergedMatch,$ip)==0) && (strpos($lowercaseEntry,"failed password") || strpos($lowercaseEntry, "authentication failure"))){

                    $totalViolations++;
                    $hasViolated = true;
                    fwrite($fp, $entry);
                    if(strpos($lowercaseEntry,"ssh")){
                        //print("SSH Violation \n");
                        $totalSSH++;
                        $hasSSHViolated = true;

                    }

                    if(strpos($lowercaseEntry,"ftp")){
                        //print("FTP Violation \n");
                        $totalFTP++;
                        $hasFTPViolated = true;
                    }


                }

                if((strcmp($mergedMatch,$ip)==0) && (strpos($lowercaseEntry, "accepted publickey"))){

                        fwrite($fp, $entry);

                        $hasAcceptedPublicKey = true;
                        $totalAcceptedPublicKeys++;

                }

                if((strcmp($mergedMatch,$ip)==0) && (strpos($lowercaseEntry, "connection closed"))){

                    fwrite($fp, $entry);

                    //$hasAcceptedPublicKey = true;
                    $hasClosedAConnection = true;
                    $totalConnectionsClosed++;

                }

            }




        }

    }

    if($hasViolated){
        $ipsWhoViolated++;
    }
    if($hasFTPViolated){
        $ipsWhoFTPViolated++;
    }
    if($hasSSHViolated){
        $ipsWhoSSHViolated++;
    }
    if($hasAcceptedPublicKey){
        $ipsWhoAcceptedPublicKeys++;
    }
    if($hasClosedAConnection){
        $ipsWhoClosedAConnection++;
    }

    fwrite($fp, "TOTAL SSH VIOLATIONS: $totalSSH \n");
    fwrite($fp, "TOTAL FTP VIOLATIONS: $totalFTP \n");
    fwrite($fp, "TOTAL VIOLATIONS: $totalViolations \n");
    fwrite($fp, "TOTAL ACCEPTING PUBLIC KEYS: $totalAcceptedPublicKeys \n");
    fwrite($fp, "TOTAL CLOSED CONNECTIONS: $totalConnectionsClosed \n");

    fwrite($tfp, "TOTAL SSH VIOLATIONS: $totalSSH \n");
    fwrite($tfp, "TOTAL FTP VIOLATIONS: $totalFTP \n");
    fwrite($tfp, "TOTAL VIOLATIONS: $totalViolations \n");
    fwrite($tfp, "TOTAL ACCEPTING PUBLIC KEYS: $totalAcceptedPublicKeys \n");
    fwrite($tfp, "TOTAL CLOSED CONNECTIONS: $totalConnectionsClosed \n");

    //print("TOTAL SSH VIOLATIONS: $totalSSH \n");
    //print("TOTAL FTP VIOLATIONS: $totalFTP \n");
    //print("TOTAL VIOLATIONS: $totalViolations \n");
    //print("TOTAL ACCEPTING PUBLIC KEYS: $totalAcceptedPublicKeys \n");
}

//print("TOTAL IPS VIOLATING WITH SSH : $ipsWhoSSHViolated \n");
//print("TOTAL IPS VIOLATING WITH FTP : $ipsWhoFTPViolated \n");
//print("TOTAL IPS WITH VIOLATIONS: $ipsWhoViolated \n");
//print("TOTAL IPS WITH ACCEPTING PUBLIC KEYS: $ipsWhoAcceptedPublicKeys \n");

fwrite($fp, "TOTAL IPS VIOLATING WITH SSH : $ipsWhoSSHViolated \n");
fwrite($fp, "TOTAL IPS VIOLATING WITH FTP : $ipsWhoFTPViolated \n");
fwrite($fp, "TOTAL IPS WITH VIOLATIONS: $ipsWhoViolated \n");
fwrite($fp, "TOTAL IPS WITH ACCEPTING PUBLIC KEYS: $ipsWhoAcceptedPublicKeys \n");
fwrite($fp, "TOTAL IPS CLOSING CONNECTIONS: $ipsWhoClosedAConnection \n");

fwrite($tfp, "TOTAL IPS VIOLATING WITH SSH : $ipsWhoSSHViolated \n");
fwrite($tfp, "TOTAL IPS VIOLATING WITH FTP : $ipsWhoFTPViolated \n");
fwrite($tfp, "TOTAL IPS WITH VIOLATIONS: $ipsWhoViolated \n");
fwrite($tfp, "TOTAL IPS WITH ACCEPTING PUBLIC KEYS: $ipsWhoAcceptedPublicKeys \n");
fwrite($tfp, "TOTAL IPS CLOSING CONNECTIONS: $ipsWhoClosedAConnection \n");

fclose($fp);
fclose($tfp);