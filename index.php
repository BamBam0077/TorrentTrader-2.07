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
dbconn(true);

stdhead(T_("HOME"));

//check
if (file_exists("check.php") && $CURUSER["class"] == 7){
	show_error_msg("WARNING", "Check.php still exists, please delete or rename the file as it could pose a security risk<BR><BR><a href=check.php>View Check.php</a> - Use to check your config!<BR><BR>",0);
}

//Site Notice
if ($site_config['SITENOTICEON']){
	begin_frame(T_("NOTICE"));
	echo $site_config['SITENOTICE'];
	end_frame();
}

//Site News
if ($site_config['NEWSON']){
	begin_frame(T_("NEWS"));
	$res = mysql_query("SELECT * FROM news ORDER BY added DESC LIMIT 10") or die(mysql_error());
	if (mysql_num_rows($res) > 0){
		print("<table width=100% border=0 cellspacing=0 cellpadding=0><tr><td>\n<ul>");
		$news_flag = 0;

		while($array = mysql_fetch_array($res)){
			$user = mysql_fetch_assoc(mysql_query("SELECT username FROM users WHERE id = $array[userid]"));
            if (!$user)
                 $user["username"] = "Unknown User"; 
            
			$numcomm = number_format(get_row_count("comments", "WHERE news='".$array['id']."'"));

			// Show first 2 items expanded
			if ($news_flag < 2) {
				$disp = "block";
				$pic = "minus";
			} else {
				$disp = "none";
				$pic = "plus";
			}

				print("<BR><a href=\"javascript: klappe_news('a".$array['id']."')\"><img border=\"0\" src=\"".$site_config["SITEURL"]."/images/$pic.gif\" id=\"pica".$array['id']."\" alt=\"Show/Hide\">");
				print("&nbsp;<b>". $array['title'] . "</b></a> - <b>".T_("POSTED").":</B> " . date("d-M-y", utc_to_tz_time($array['added'])) . " <b>".T_("BY").":</B> $user[username]");
				
				print("<div id=\"ka".$array['id']."\" style=\"display: $disp;\"> ".format_comment($array["body"],0)." <BR><BR>".T_("COMMENTS")." (<a href=comments.php?type=news&id=".$array['id'].">".$numcomm."</a>)</div><br> ");

				$news_flag++;
		}
		print("</ul></td></tr></table>\n");
	}else{
		echo "<BR><b>".T_("NO_NEWS")."</b>";
	}
	end_frame();
}



if ($site_config['SHOUTBOX']){
	begin_frame(T_("SHOUTBOX"));
	echo '<IFRAME name="shout_frame" src="'.$site_config["SITEURL"].'/shoutbox.php" frameborder="0" marginheight="0" marginwidth="0" width="99%" height="210" width=350 scrolling="no" align="middle"></IFRAME>';
	printf(T_("SHOUTBOX_REFRESH"), 5)."<BR>";
	end_frame();
}

// latest torrents
begin_frame(T_("LATEST_TORRENTS"));

print("<BR><CENTER><a href=torrents.php>".T_("BROWSE_TORRENTS")."</a> - <a href=torrents-search.php>".T_("SEARCH_TORRENTS")."</a></CENTER><BR>");

if ($site_config["MEMBERSONLY"] && !$CURUSER) {
	echo "<BR><BR><b><CENTER>".T_("BROWSE_MEMBERS_ONLY")."</CENTER><BR><BR>";
} else {
	$query = "SELECT torrents.id, torrents.anon, torrents.announce, torrents.category, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name AS cat_name, categories.image AS cat_pic, categories.parent_cat AS cat_parent, users.username, users.privacy, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id WHERE visible = 'yes' AND banned = 'no' ORDER BY id DESC LIMIT 25";
	$res = mysql_query($query) or die(mysql_error());
	if (mysql_num_rows($res)) {
		torrenttable($res);
	}else {
		show_error_msg(T_("NOTHING_FOUND"), T_("NO_UPLOADS"), 0);
	}
	if ($CURUSER)
		mysql_query("UPDATE users SET last_browse=".gmtime()." WHERE id=$CURUSER[id]");

}
end_frame();


if ($site_config['DISCLAIMERON']){
	begin_frame(T_("DISCLAIMER"));
	echo $site_config['DISCLAIMERTXT'];
	end_frame();
}


stdfoot();
?>