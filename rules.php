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

stdhead(T_("SITE_RULES"));


$res = mysql_query("SELECT * FROM rules ORDER BY id");
while ($arr=mysql_fetch_assoc($res)){
	if ($arr["public"]=="yes"){
		begin_frame($arr[title]);
		print(format_comment($arr["text"]));
		end_frame();
	}

	elseif($arr["public"]=="no" && $arr["class"]<=$CURUSER["class"]){
		begin_frame($arr[title]);
		print(format_comment($arr["text"]));
		end_frame();
	}
}

echo "<BR><BR>";


stdfoot();
?>