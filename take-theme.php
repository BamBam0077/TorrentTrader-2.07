<?php
//
//  TorrentTrader v2.x
//      $LastChangedDate: 2011-06-04 15:19:54 +0100 (Sat, 04 Jun 2011) $
//      $LastChangedBy: torrentialstorm $
//
//      http://www.torrenttrader.org
//
//
require_once("backend/functions.php");
dbconn();
loggedinonly();

$set = array();

$updateset = array();
$stylesheet = $_POST["stylesheet"];
$language = $_POST["language"];

if (is_valid_id($stylesheet))
  $updateset[] = "stylesheet = '$stylesheet'";

mysql_query("UPDATE users SET " . implode(",", $updateset) . " WHERE id = " . $CURUSER["id"]);

if (is_valid_id($language))
  $updateset[] = "language = '$language'";

mysql_query("UPDATE users SET " . implode(",", $updateset) . " WHERE id = " . $CURUSER["id"]);

header("Location: index.php");
?>