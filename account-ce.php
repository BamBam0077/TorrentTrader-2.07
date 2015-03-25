<?php
//
//  TorrentTrader v2.x
//      $LastChangedDate: 2011-06-04 15:19:54 +0100 (Sat, 04 Jun 2011) $
//      $LastChangedBy: torrentialstorm $
//
//      http://www.torrenttrader.org
//
require_once("backend/functions.php");
dbconn(false);

$id = 0 + $_GET["id"];
$md5 = $_GET["secret"];
$email = $_GET["email"];

stdhead();

if (!$id || !$md5 || !$email)
	show_error_msg("Couldn't change the email", T_("ERROR")." retrieving ID, KEY or ".T_("EMAIL").".",1);


$res = mysql_query("SELECT editsecret FROM users WHERE id = $id");
$row = mysql_fetch_array($res);

if (!$row)
	show_error_msg("Couldn't change the email", "No user found wanting to change the email.",1);

$sec = hash_pad($row["editsecret"]);
if (preg_match('/^ *$/s', $sec))
	show_error_msg("Couldn't change the email", "No match found.",1);
if ($md5 != md5($sec . $email . $sec))
	show_error_msg("Couldn't change the email", "No md5.",1);

mysql_query("UPDATE users SET editsecret='', email=" . sqlesc($email) . " WHERE id=$id AND editsecret=" . sqlesc($row["editsecret"]));

if (!mysql_affected_rows())
	show_error_msg("Couldn't change the email", "No affected rows.",1);

header("Refresh: 0; url=" . $site_config["SITEURL"] . "/account.php");

stdfoot();
?>