<?php
//
//  TorrentTrader v2.x
//	This file was last updated: 3/Sept/2007
//	
//	http://www.torrenttrader.org
//
//
require_once("backend/functions.php");
dbconn(false);

//check permissions
if ($site_config["MEMBERSONLY"]){
	loggedinonly();

	if($CURUSER["view_torrents"]=="no")
		show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1);
}


$reseedid = (int)$_GET["id"];

if (!is_valid_id($id))
	show_error_msg(T_("ERROR"), T_("TORRENT_DONT_EXIST"), 1);

stdhead(T_("RESEED_REQUEST"));

begin_frame(T_("RESEED_REQUEST"));

if (isset($_COOKIE["TTrsreq$reseedid"])){ //check cookie for spam prevention
	echo "<div align=left>You have recently made a request for this reseed. Please wait longer for another request.</div>";
	//end cookie check
}else{

	$res = mysql_query("SELECT owner FROM torrents WHERE id=$reseedid");
	if (mysql_num_rows($res) < 1){
		show_error_msg(T_("ERROR"), T_("TORRENT_DONT_EXIST"), 0);
		stdfoot();
		die;
	}

	$owner = mysql_fetch_array($res);
	$ownerid = $owner["owner"];


	echo "<BR><br><div align=left>Your request for a re-seed has been sent to the following members that have completed this torrent:<br><Br>";

	$sres = mysql_query("SELECT * FROM completed WHERE torrentid = " .$reseedid. "");
	while ($srow = mysql_fetch_array($sres)){

		$res = mysql_query("SELECT id, username FROM users WHERE id = ".$srow["userid"]." AND enabled='yes'");
		if ($result=mysql_fetch_array($res)) {
			print("<a href=account-details.php?id=$result[id]>".$result["username"]."</a> ");

			$pn_msg = "" . $CURUSER["username"] . " has requested a re-seed on the torrent below because there are currently no or few seeds:\n\n" . $site_config["SITEURL"] . "/torrents-details.php?id=" . $_GET["id"] . " \nThank You!";

			$rec = $result["id"];
			$send = $CURUSER["id"];

			//SEND MSG
			mysql_query("INSERT INTO messages (subject, sender, receiver, added, msg) VALUES ('Reseed Request',$send,$rec,'".get_date_time()."', " . sqlesc($pn_msg) . ")") or die(mysql_error());

			//request spamming prevention
			@setcookie("TTrsreq".$reseedid, $reseedid);
		}
	}

	if ($ownerid)
		mysql_query("INSERT INTO messages (sender, receiver, added, msg) VALUES (".$CURUSER['id'].",".$ownerid.",'".get_date_time()."', " . sqlesc($pn_msg) . ")") or die(mysql_error());
}

echo "<BR><br>";
end_frame();

stdfoot();
?>