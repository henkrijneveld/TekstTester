<?php
// add this to vulcanize (and indexed), beacaus vulcanize will not work nicely.....
// prbly should use grunt or whatever build tool, phing??

$ip = '0.0.0.0';

if (isset($_SERVER['REMOTE_ADDR'])) {
    $ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP);
}

// Make random unique key: date-time-ip-randomhash
$sessionkey = DateTime::createFromFormat('U.u', microtime(true))->format("Y-m-d=H:i:s:u");

//@todo change $ip into hash
$hidden = str_replace(".", ":", $ip);
$hidden .= "+".sprintf("%04d", mt_rand(0, 10000));
$hidden = openssl_encrypt($hidden, "AES128" , "Stant1234");
$sessionkey .= "=".$hidden;

//@todo better algorithm or cookie?
$parts = explode(".", $ip);
$type = intval($parts[0]) + intval($parts[1]) + intval($parts[2]) + intval($parts[3]);
$textkey = "versie" . chr(ord("a") + ($type % 3));
?>
