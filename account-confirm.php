<?php
//
//  TorrentTrader v2.x
//      $LastChangedDate: 2011-06-04 15:19:54 +0100 (Sat, 04 Jun 2011) $
//      $LastChangedBy: torrentialstorm $
//
//      http://www.torrenttrader.org
//
//
// Confirm account Via email and send PM
//
require_once("backend/functions.php");


$id = (int)$_GET["id"];
$md5 = $_GET["secret"];

if (!$id)
	show_error_msg(T_("ERROR"), T_("THATS_NOT_A_VALID_ID")."!",1);

dbconn();

$res = mysql_query("SELECT password, secret, status FROM users WHERE id = $id");
$row = mysql_fetch_array($res);

if (!$row)
	show_error_msg(T_("ERROR"), T_("SIGNUP_NOT_FOUND")." ".($site_config['signup_timeout']/86400)." days.",1);

if ($row["status"] != "pending") {
	header("Refresh: 0; url=account-confirm-ok.php?type=confirmed");
	exit();
}

$sec = $row["secret"];

if ($md5 != md5($sec))
	show_error_msg(T_("ERROR"), T_("SIGNUP_ACTIVATE_LINK"), 1);

$newsec = mksecret();

mysql_query("UPDATE users SET secret=" . sqlesc($newsec) . ", status='confirmed' WHERE id=$id AND secret=" . sqlesc($row["secret"]) . " AND status='pending'");

if (!mysql_affected_rows())
	show_error_msg(T_("ERROR"), T_("SIGNUP_UNABLE"), 1);

header("Refresh: 0; url=account-confirm-ok.php?type=confirm");
?>