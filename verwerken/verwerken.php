<?php
/**
 * Created by PhpStorm.
 * User: henk
 * Date: 23-12-16
 * Time: 10:21
 */

$data = file_get_contents("teksttesterp-export.json");
$jsondata = json_decode($data, true)["result"];

function getipfromkey($key) {

}

function getdatefromkey($key) {
  return(substr($key, 0, 10));
}

function gettimefromkey($key) {
  return(substr($key, 11, 8));
}

function geteventtime($name, $content, $date, $starttimestamp, $second = false) {
  $nrfound = 0; // for the stap-lezen-2 bug
  foreach ($content as $time => $type) {
    if (isset($type["name"])) {
      if ($type["name"] == $name) {
        $nrfound++;
        if ($second && $nrfound == 1)
          continue;
        return (strtotime($date." ".substr($time,0,8)) - $starttimestamp);
      }
    }
  }
  return 0;
}

function getenquetedata($content) {
  foreach ($content as $key => $type) {
    if ($key == "Enquete") {
      $retval = $type["vraagdialoog"];
      $retval .= ",".$type["vraagintrekken"];
      $retval .= ",".$type["vraagprettig"];
      $retval .= ",".$type["vraaglezer"];
      $retval .= ","."'".str_replace("'", '"', $type["vraagopmerking"])."'";
      return $retval;
    }
  }
  return "0,0,0,'',''";
}

function noneg($nr) {
  if ($nr > 0) {
    return $nr;
  }
  return 0;
}

function getip($cipher) {
  $cipher = str_replace("-", "/", $cipher); // base64 uses /, will be interpreted by key separator in firebase
  $hidden = openssl_decrypt($cipher, "AES128" , "Polante4321", 0, str_pad("", 16, "a"));
  $ip = substr($hidden, 0, strpos($hidden, "+"));
  $ip = str_replace(":", ".", $ip);

  return $ip;
}

$output = "timestamp,ip,datum,tijd,versie,herkomst,welkom,lezen-een,lezen-twee,enquete,bedankt,dialoog,intrekken,prettig,lezer,dialoog";
echo $output . "\n";


foreach ($jsondata as $logkey => $logdata) {
  $startdate = getdatefromkey($logkey);
  $starttime = gettimefromkey($logkey);
  $starttimestamp = strtotime($startdate." ".$starttime) + 3600;
  $startdate = date("Y-m-d", $starttimestamp);
  $output = $starttimestamp;
  $output .= "," . getip(substr($logkey, 27, 44));
  $output .= "," . $startdate;
  $output .= "," . $starttime;
  $output .= "," . $logdata["version"];
  $output .= "," . $logdata["referrer"];

  $timewelkom = geteventtime("stap-welkom", $logdata, $startdate, $starttimestamp);
  $timelezeneen = geteventtime("stap-lezen-2", $logdata, $startdate, $starttimestamp);
  $timelezentwee = geteventtime("stap-lezen-2", $logdata, $startdate, $starttimestamp, true);
  $timeenquete = geteventtime("stap-enquete", $logdata, $startdate, $starttimestamp);
  $timebedankt = geteventtime("stap-bedankt", $logdata, $startdate, $starttimestamp);

  $output .= "," . noneg($timewelkom);
  $output .= "," . noneg(($timelezeneen - $timewelkom));
  $output .= "," . noneg(($timelezentwee - $timelezeneen));
  $output .= "," . noneg(($timeenquete - $timelezentwee));
  $output .= "," . noneg(($timebedankt - $timeenquete));
  $output .= "," . getenquetedata($logdata);


  echo $output . "\n";

#  echo $logkey . "\n";

}



