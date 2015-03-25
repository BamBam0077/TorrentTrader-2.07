<?php
//
//  TorrentTrader v2.x
//	This file was last updated: 4/Sept/2007
//	
//	http://www.torrenttrader.org
//
//
require "backend/functions.php";
dbconn(false);

//check permissions
if ($site_config["MEMBERSONLY"]){
	loggedinonly();

	if($CURUSER["view_torrents"]=="no")
		show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1);
}


stdhead(T_("TORRENT_NEED_SEED"));

begin_frame(T_("TORRENT_NEED_SEED"));
$need_seeds = mysql_query("SELECT * FROM torrents WHERE banned = 'no' AND leechers > 0 AND seeders <= 1 ORDER BY seeders");


if (mysql_num_rows($need_seeds) > 0) {
	print("<font color=\"#FF0000\">".T_("TORRENT_NEED_SEED_MSG")."</font>");
	print("<br><br>");

	print("<table align=center cellpadding=0 cellspacing=0 class=table_table width=95% border=1>");
	print("<TR><td class=table_head align=center>" .T_("TORRENT_NAME"). "</td>");
	print("<td class=table_head align=center>".T_("UPLOADER")."</td>");
	print("<td class=table_head align=center>".T_("LOCAL_EXTERNAL")."</td>");
	print("<td class=table_head align=center>" .T_("SIZE")."</td>");
	print("<td class=table_head align=center>" .T_("SEEDS"). "</td>");
	print("<td class=table_head align=center>" .T_("LEECHERS"). "</td>");
	print("<td class=table_head align=center>".T_("COMPLETE")."</td>");
	print("<td class=table_head align=center>" .T_("ADDED"). "</td><TR>");

	while ($row2 = mysql_fetch_array($need_seeds)) {
		if($row2["external"] == 'yes') {
			$type = "External";
		} else {
			$type = "Local";
		}

		$torrname = htmlspecialchars($row2["name"]);
		if (strlen($torrname) > 40)
			$torrname = substr($torrname, 0, 40) . "...";

		$ttl = (28*24) - floor((utc_to_tz_time() - utc_to_tz_time($row2["added"])) / 3600);

		$username = mysql_query("SELECT id, username FROM users WHERE id = $row2[owner]");
		$row = mysql_fetch_array($username);

		echo "<tr><td class=table_col2 align=left><a href=\"torrents-details.php?id=$row2[id]\">$torrname</a></td>";
	
		echo "<td class=table_col1 align=center><a href=\"account-details.php?id=$row[id]\">$row[username]</a></td>";
		echo "<td class=table_col1 align=center>$type</td>";
		print("<td class=table_col2 align=right><font size=1 face=Verdana>" . mksize($row2[size]) . "</td>\n");
		echo "<td class=table_col1 align=center><font color=green>$row2[seeders]</td>";
		echo "<td class=table_col2 align=center><font color=red>$row2[leechers]</td>";
		echo "<td class=table_col1 align=center><font color=black>$row2[times_completed]</td>";
		echo "<td class=table_col2 align=center><font color=purple>".utc_to_tz($row2['added'])."</td>";
		print("</tr>");
	}
	echo "</table><br></br>";

} else {
	print("<b><CENTER>No ".T_("TORRENT_NEED_SEED")."</CENTER></b><BR><BR>");
}


end_frame();
stdfoot();

?>
