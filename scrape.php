<?php
//
//  TorrentTrader v2.x
//      $LastChangedDate: 2011-06-04 15:19:54 +0100 (Sat, 04 Jun 2011) $
//      $LastChangedBy: torrentialstorm $
//
//      http://www.torrenttrader.org
//
//
error_reporting(0); //disable error reporting

// check if client can handle gzip
if (stristr($_SERVER["HTTP_ACCEPT_ENCODING"],"gzip") && extension_loaded('zlib') && ini_get("zlib.output_compression") == 0) {
    if (ini_get('output_handler')!='ob_gzhandler') {
        ob_start("ob_gzhandler");
    } else {
        ob_start();
    }
}else{
     ob_start();
}
// end gzip controll



require_once("backend/mysql.php");

function dbconn() {
    global $mysql_host, $mysql_user, $mysql_pass, $mysql_db;

    if (!@mysql_connect($mysql_host, $mysql_user, $mysql_pass))
    {
      die('DATABASE: mysql_connect: ' . mysql_error());
    }
     mysql_select_db($mysql_db)
        or die('DATABASE: mysql_select_db: ' + mysql_error());

    unset($mysql_pass); //security
}

function hex2bin($hexdata) {
  $bindata = "";
  for ($i=0;$i<strlen($hexdata);$i+=2) {
    $bindata.=chr(hexdec(substr($hexdata,$i,2)));
  }

  return $bindata;
}


function sqlesc($x) {
    return "'".mysql_real_escape_string($x)."'";
}

dbconn();

$infohash = array();

foreach (explode("&", $_SERVER["QUERY_STRING"]) as $item) {
    if (substr($item, 0, 10) == "info_hash=") {
        $hash = substr($item, 10);
        $hash = urldecode($hash);

        if (get_magic_quotes_gpc())
            $info_hash = stripslashes($hash);
        else
            $info_hash = $hash;
        if (strlen($info_hash) == 20)
            $info_hash = bin2hex($info_hash);
        else if (strlen($info_hash) != 40)
            continue;
        $infohash[] = sqlesc(strtolower($info_hash));
    }
}

if (!count($infohash)) die("Invalid infohash.");
    $query = mysql_query("SELECT info_hash, seeders, leechers, times_completed, filename FROM torrents WHERE info_hash IN (".join(",", $infohash).")");

$result="d5:filesd";

while ($row = mysql_fetch_row($query))
{
    $hash = hex2bin($row[0]);
    $result.="20:".$hash."d";
    $result.="8:completei".$row[1]."e";
    $result.="10:downloadedi".$row[3]."e";
    $result.="10:incompletei".$row[2]."e";
    $result.="4:name".strlen($row[4]).":".$row[4]."e";
    $result.="e";
}

$result.="ee";

echo $result;
ob_end_flush();
mysql_close();
?>