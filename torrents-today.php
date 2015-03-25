<?php
//
//  TorrentTrader v2.x
//	This file was last updated: 26/June/2007
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

stdhead(T_("TODAYS_TORRENTS"));

begin_frame(T_("TODAYS_TORRENTS"));

$date_time=get_date_time(gmtime()-(3600*24)); // the 24 is the hours you want listed

	$catresult = mysql_query("SELECT id, name FROM categories ORDER BY sort_index");

		while($cat = mysql_fetch_array($catresult))
		{
			$orderby = "ORDER BY torrents.id DESC"; //Order
			$where = "WHERE banned = 'no' AND category='$cat[id]' AND visible='yes'";
			$limit = "LIMIT 1000"; //Limit

			$query = "SELECT torrents.id, torrents.anon, torrents.category, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name AS cat_name, categories.parent_cat AS cat_parent, categories.image AS cat_pic, users.username, users.privacy FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where AND torrents.added>='$date_time' $orderby $limit";

			$res = mysql_query($query);
			$numtor = mysql_num_rows($res);

			if ($numtor != 0) {
					echo "<b><a href=torrents.php?cat=".$cat[id].">$cat[name]</a></B>";
					list($pagertop, $pagerbottom, $limit) = pager(1000, $count, "torrents.php?" . $addparam); //adjust pager to match LIMIT
					torrenttable($res);
					echo "<BR>";
			}
		

		}
end_frame();
stdfoot();
?>