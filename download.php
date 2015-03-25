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

if ($_GET["passkey"]) {
	$CURUSER = mysql_fetch_array(mysql_query("SELECT * FROM users INNER JOIN groups ON users.class=groups.group_id WHERE passkey=".sqlesc($_GET["passkey"])." AND enabled='yes' AND status='confirmed'"));
}

//check permissions
if ($site_config["MEMBERSONLY"]){
	loggedinonly();
	
	if($CURUSER["can_download"]=="no")
		show_error_msg(T_("ERROR"), T_("NO_PERMISSION_TO_DOWNLOAD"), 1);
}

$id = (int)$_GET["id"];

if (!$id)
	show_error_msg(T_("ID_NOT_FOUND"), T_("ID_NOT_FOUND_MSG_DL"), 1);

$res = mysql_query("SELECT filename, banned, external, announce FROM torrents WHERE id =".intval($id));
$row = mysql_fetch_array($res);
$trackerurl = $row['announce'];

$torrent_dir = $site_config["torrent_dir"];

$fn = "$torrent_dir/$id.torrent";

if (!$row)
	show_error_msg("File not found", "No file has been found with that ID!",1);
if ($row["banned"] == "yes")
	show_error_msg(T_("ERROR"), "Torrent is banned.", 1);
if (!is_file($fn))
	show_error_msg("File not found", "The ID has been found on the Database, but the torrent has gone!<BR><BR>Check Server Paths and CHMODs Are Correct!",1);
if (!is_readable($fn))
	show_error_msg("File not found", "The ID and torrent were found, but the torrent is NOT readable!",1);

$name = $row['filename'];
$friendlyurl = str_replace("http://","",$site_config[SITEURL]);
$friendlyname = str_replace(".torrent","",$name);
$friendlyext = ".torrent";
$name = $friendlyname ."[". $friendlyurl ."]". $friendlyext;

mysql_query("UPDATE torrents SET hits = hits + 1 WHERE id = $id");

require_once("backend/BEncode.php");
require_once("backend/BDecode.php");

//if user dont have a passkey generate one, only if tracker is set to members only
if ($site_config["MEMBERSONLY"]){
	if (strlen($CURUSER['passkey']) != 32) {
		$rand = array_sum(explode(" ", microtime()));
		$CURUSER['passkey'] = md5($CURUSER['username'].$rand.$CURUSER['secret'].($rand*mt_rand()));
		mysql_query("UPDATE users SET passkey='$CURUSER[passkey]' WHERE id=$CURUSER[id]");
	}
}

if ($row["external"]!='yes' && $site_config["MEMBERSONLY"]){// local torrent so add passkey
	$dict = BDecode(file_get_contents($fn));
	$dict['announce'] = sprintf($site_config["PASSKEYURL"], $CURUSER["passkey"]);
	unset($dict['announce-list']);


	header('Content-Disposition: attachment; filename="'.$name.'"');

	header("Content-Type: application/x-bittorrent");

	print(BEncode($dict)); 

}else{// external torrent so no passkey needed
	header('Content-Disposition: attachment; filename="'.$name.'"');

	header("Content-Type: application/x-bittorrent");

	readfile($fn); 
}

mysql_close();
?>