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
require_once("backend/BDecode.php") ;
require_once("backend/parse.php") ;//replace with parse later
dbconn();

$torrent_dir = $site_config["torrent_dir"];	
$nfo_dir = $site_config["nfo_dir"];	

//check permissions
if ($site_config["MEMBERSONLY"]){
	loggedinonly();

	if($CURUSER["view_torrents"]=="no")
		show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1);
}

//************ DO SOME "GET" STUFF BEFORE PAGE LAYOUT ***************

$id = (int) $_GET["id"];
$scrape = (int)$_GET["scrape"];
if (!is_valid_id($id))
	show_error_msg("ERROR", T_("THATS_NOT_A_VALID_ID"), 1);

//GET ALL MYSQL VALUES FOR THIS TORRENT
	$res = mysql_query("SELECT torrents.anon, torrents.seeders, torrents.banned, torrents.leechers, torrents.info_hash, torrents.filename, torrents.nfo, torrents.last_action, torrents.numratings, torrents.name, torrents.owner, torrents.save_as, torrents.descr, torrents.visible, torrents.size, torrents.added, torrents.views, torrents.hits, torrents.times_completed, torrents.id, torrents.type, torrents.external, torrents.image1, torrents.image2, torrents.announce, torrents.numfiles, torrents.freeleech, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.numratings, categories.name AS cat_name, torrentlang.name AS lang_name, torrentlang.image AS lang_image, categories.parent_cat as cat_parent, users.username, users.privacy FROM torrents LEFT JOIN categories ON torrents.category = categories.id LEFT JOIN torrentlang ON torrents.torrentlang = torrentlang.id LEFT JOIN users ON torrents.owner = users.id WHERE torrents.id = $id") or die(mysql_error());



$row = mysql_fetch_array($res);

$moderator = $CURUSER["edit_torrents"] == "yes";


//DECIDE IF TORRENT EXISTS
if (!$row || ($row["banned"] == "yes" && !$moderator))
	show_error_msg(T_("ERROR"), T_("TORRENT_NOT_FOUND"), 1);

//torrent is availiable so do some stuff

if ($_GET["hit"]) {
	mysql_query("UPDATE torrents SET views = views + 1 WHERE id = $id");
	header("Location: torrents-details.php?id=$id");
	die;
	}

	stdhead(T_("DETAILS_FOR_TORRENT")." \"" . $row["name"] . "\"");

	if ($CURUSER["id"] == $row["owner"])
		$owned = 1;
	else
		$owned = 0;

	if ($CURUSER["edit_torrents"]=="yes")
		$owned = 1;

//take rating
if ($_GET["takerating"] == 'yes'){
	$rating = (int)$_POST['rating'];

	if ($rating <= 0 || $rating > 5)
		show_error_msg(T_("RATING_ERROR"), T_("INVAILD_RATING"), 1);

	$res = mysql_query("INSERT INTO ratings (torrent, user, rating, added) VALUES ($id, " . $CURUSER["id"] . ", $rating, '".get_date_time()."')");

	if (!$res) {
		if (mysql_errno() == 1062)
			show_error_msg(T_("RATING_ERROR"), T_("YOU_ALREADY_RATED_TORRENT"), 1);
		else
			show_error_msg(T_("RATING_ERROR"), T_("A_UNKNOWN_ERROR_CONTACT_STAFF"), 1);
	}

	mysql_query("UPDATE torrents SET numratings = numratings + 1, ratingsum = ratingsum + $rating WHERE id = $id");
	show_error_msg(T_("RATING_ERROR"), T_("RATING_THANK")."<BR><BR><a href=torrents-details.php?id=$id>" .T_("BACK_TO_TORRENT"). "</a>");
}

//take comment add
if ($_GET["takecomment"] == 'yes'){
	loggedinonly();
	$commentbody = $_POST['body'];
	
	if (!$commentbody)
		show_error_msg(T_("RATING_ERROR"), T_("YOU_DID_NOT_ENTER_ANYTHING"), 1);

	mysql_query("UPDATE torrents SET comments = comments + 1 WHERE id = $id") or die(mysql_error());

	mysql_query("INSERT INTO comments (user, torrent, added, text) VALUES (".$CURUSER["id"].", ".$id.", '" .get_date_time(). "', " . sqlesc($body).")") or die(mysql_error());

	if (mysql_affected_rows() == 1)
			show_error_msg(T_("COMPLETED"), T_("COMMENT_ADDED"), 0);
		else
			show_error_msg(T_("ERROR"), T_("UNABLE_TO_ADD_COMMENT"), 0);
}//end insert comment

//START OF PAGE LAYOUT HERE
$char1 = 50; //cut length
$shortname = CutName(htmlspecialchars($row["name"]), $char1);

begin_frame(T_("TORRENT_DETAILS_FOR"). " \"" . $shortname . "\"");

echo "<div align=right>[<a href=report.php?torrent=$id><B>" .T_("REPORT_TORRENT"). "</B></a>]&nbsp;";
if ($owned)
	echo "[<a href=torrents-edit.php?id=$row[id]><B>".T_("EDIT_TORRENT")."</B></a>]";
echo "</div>";

echo "<center><h1>" . $shortname . "</h1></center>";

// Calculate local torrent speed test
if ($row["leechers"] >= 1 && $row["seeders"] >= 1 && $row["external"]!='yes'){
	$speedQ = mysql_query("SELECT (SUM(p.downloaded)) / (UNIX_TIMESTAMP('".get_date_time()."') - UNIX_TIMESTAMP(added)) AS totalspeed FROM torrents AS t LEFT JOIN peers AS p ON t.id = p.torrent WHERE p.seeder = 'no' AND p.torrent = '$id' GROUP BY t.id ORDER BY added ASC LIMIT 15") or die(mysql_error());
	$a = mysql_fetch_assoc($speedQ);
	$totalspeed = mksize($a["totalspeed"]) . "/s";
}else{
	$totalspeed = "".T_("NO_ACTIVITY").""; 
}

//download box
echo "<CENTER><table border=0 width=98%><TR><TD><div id=downloadbox>";
if ($row["banned"] == "yes"){
	print ("<CENTER><B>" .T_("DOWNLOAD"). ": </B>BANNED!</CENTER>");
}else{
	print ("<table border=0 cellpadding=0 width=95%><tr><td align=center valign=middle width=54><a href=\"download.php?id=$id&name=" . rawurlencode($row["filename"]) . "\"><img src=\"".$site_config["SITEURL"]."/images/download_torrent.gif\" border=\"0\"></a></td>");
	print ("<td valign=top><a href=\"download.php?id=$id&name=" . rawurlencode($row["filename"]) . "\">".T_("DOWNLOAD_TORRENT")."</a><BR>");
	print ("<B>" .T_("HEALTH"). ": </b><img src=".$site_config["SITEURL"]."/images/health_".health($row["leechers"], $row["seeders"]).".gif><BR>");
	print ("<B>" .T_("SEEDS"). ": </b><font color=green>" . $row["seeders"] . "</font><BR>");
	print ("<B>".T_("LEECHERS").": </b><font color=red>" . $row["leechers"] . "</font><BR>");

	if ($row["external"]!='yes'){
		print ("<B>".T_("SPEED").": </b>" . $totalspeed . "<BR>");
	}

	print ("<b>".T_("COMPLETED").": </b>" . $row["times_completed"] . "</B>&nbsp;"); 

	if ($row["external"] != "yes" && $row["times_completed"] > 0) {
		echo("[<a href='torrents-completed.php?id=$id'>" .T_("WHOS_COMPLETED"). "</a>] ");
		if ($row["seeders"] <= 1) {
			echo("[<a href='torrents-reseed.php?id=$id'>" .T_("REQUEST_A_RE_SEED"). "</a>]");
		}
	}
	echo "<br>";

	if ($row["external"]!='yes' && $row["freeleech"]=='1'){
		print ("<B>".T_("FREE_LEECH").": </b><font color=red>".T_("FREE_LEECH_MSG")."</font><BR>");
	}

	print ("<B>".T_("LAST_CHECKED").": </b>" . date("d-m-Y H:i:s", utc_to_tz_time($row["last_action"])) . "<BR></td>");

	if ($row["external"]=='yes'){

		if ($scrape =='1'){
			print("<td valign=top align=right><b>Tracked: </b>EXTERNAL<BR><BR>");
			$seeders1 = $leechers1 = $downloaded1 = null;

			$tres = mysql_query("SELECT url FROM announce WHERE torrent=$id");
			while ($trow = mysql_fetch_array($tres)) {
				$ann = $trow["url"];
				$tracker = explode("/", $ann);
				$path = array_pop($tracker);
				$oldpath = $path;
				$path = preg_replace("/^announce/", "scrape", $path);
				$tracker = implode("/", $tracker)."/".$path;

				if ($oldpath == $path) {
					continue; // Scrape not supported, ignored
				}

				// TPB's tracker is dead. Use openbittorrent instead
				if (preg_match("/thepiratebay.org/i", $tracker) || preg_match("/prq.to/", $tracker)) {
					$tracker = "http://tracker.openbittorrent.com/scrape";
				}

				$stats = torrent_scrape_url($tracker, $row["info_hash"]);
				if ($stats['seeds'] != -1) {
					$seeders1 += $stats['seeds'];
					$leechers1 += $stats['peers'];
					$downloaded1 += $stats['downloaded'];
					mysql_query("UPDATE `announce` SET `online` = 'yes', `seeders` = $stats[seeds], `leechers` = $stats[peers], `times_completed` = $stats[downloaded] WHERE `url` = ".sqlesc($ann)." AND `torrent` = $id");
				} else {
					mysql_query("UPDATE `announce` SET `online` = 'no' WHERE `url` = ".sqlesc($ann)." AND `torrent` = $id");

				}
			}

			if ($seeders1 !== null){ //only update stats if data is received
				print ("<B>".T_("LIVE_STATS").": </b><BR>");
				print ("Seeders: ".$seeders1."<BR>");
				print ("Leechers: ".$leechers1."<BR>");
				print ("".T_("COMPLETED").": ".$downloaded1."<BR>");

				mysql_query("UPDATE torrents SET leechers='".$leechers1."', seeders='".$seeders1."',times_completed='".$downloaded1."',last_action= '".get_date_time()."',visible='yes' WHERE id='".$row['id']."'"); 
			}else{
				print ("<B>".T_("LIVE_STATS").": </b><BR>");
				print ("<font color=red>Tracker Timeout<BR>Please retry later</font><BR>");
			}

			print ("<form action=torrents-details.php?id=$id&scrape=1 method=post><input type=\"submit\" name=\"submit\" value=\"Update Stats\"></td></form>");
		}else{
			print ("<td valign=top align=right><b>Tracked: </b>EXTERNAL<BR><BR><form action=torrents-details.php?id=$id&scrape=1 method=post><input type=\"submit\" name=\"submit\" value=\"Update Stats\"></td></form>");
		}
	}

	echo "</tr></table>";
}
echo "</div></td></tr></table></CENTER><BR><BR>";
//end download box


echo "<FIELDSET class=search><LEGEND></a><b>Details</B></LEGEND>";
echo "<table cellpadding=3 border=0 width=95%>";
print("<tr><td align=left><b>".T_("NAME").":</b></td><td>" . $shortname . "</td></tr>\n");
print("<tr><td align=left colspan=2><b>" .T_("DESCRIPTION"). ":</b><br>" .  format_comment($row['descr']) . "</td></tr>\n");
print("<tr><td align=left><b>" .T_("CATEGORY"). ":</b></td><td>" . $row["cat_parent"] . " > " . $row["cat_name"] . "</td></tr>\n");

if (empty($row["lang_name"])) $row["lang_name"] = "Unknown/NA";
print("<tr><td align=left><b>" .T_("LANG"). ":</b></td><td>" . $row["lang_name"] . "\n");

if (isset($row["lang_image"]) && $row["lang_image"] != "")
			print("&nbsp;<img border=\"0\"src=\"" . $site_config['SITEURL'] . "/images/languages/" . $row["lang_image"] . "\" alt=\"" . $row["lang_name"] . "\" />");

print("</td></tr>");

print("<tr><td align=left><b>" .T_("TOTAL_SIZE"). ":</b></td><td>" . mksize($row["size"]) . " </td></tr>\n");
print("<tr><td align=left><b>" .T_("INFO_HASH"). ":</b></td><td>" . $row["info_hash"] . "</td></tr>\n");
print("");
if ($row["anon"] == "yes" && !$owned)
	print("<tr><td align=left><b>" .T_("ADDED_BY"). ":</b></td><td>Anonymous</td></tr>");
elseif ($row["username"])
	print("<tr><td align=left><b>" .T_("ADDED_BY"). ":</b></td><td><a href=account-details.php?id=" . $row["owner"] . ">" . $row["username"] . "</a></td></tr>");
else
	print("<tr><td align=left><b>" .T_("ADDED_BY"). ":</b></td><td>Unknown</td></tr>");

print("<tr><td align=left><b>" .T_("DATE_ADDED"). ":</b></td><td>" . date("d-m-Y H:i:s", utc_to_tz_time($row["added"])) . "</td></tr>\n");
print("<tr><td align=left><b>" .T_("VIEWS"). ":</b></td><td>" . $row["views"] . "</td></tr>\n");
print("<tr><td align=left><b>".T_("HITS").":</b></td><td>" . $row["hits"] . "</td></tr>\n");
echo "</table></FIELDSET><BR><BR>";


// $srating IS RATING VARIABLE
		$srating = "";
		$srating .= "<table border=\"0\" cellspacing=\"1\" cellpadding=\"4\" width='95%' style=\"border: 2px solid #f8de8f;\"><tr><td style=\"background:#f8edcc;\" width=60><b>".T_("RATINGS").":</b></td><td style=\"background:#f8f1dd;\" valign=middle><NOBR>";
		if (!isset($row["rating"])) {
				$srating .= "Not Yet Rated";
		}else{
			$rpic = ratingpic($row["rating"]);
			if (!isset($rpic))
				$srating .= "invalid?";
			else
				$srating .= "$rpic (" . $row["rating"] . " ".T_("OUT_OF")." 5) " . $row["numratings"] . " ".T_("USERS_HAVE_RATED")."";
		}
		$srating .= "\n";
		if (!isset($CURUSER))
			$srating .= "(<a href=\"account-login.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;nowarn=1\">Log in</a> to rate it)";
		else {
			$ratings = array(
					5 => "".T_("COOL")."",
					4 => "".T_("PRETTY_GOOD")."",
					3 => "".T_("DECENT")."",
					2 => "".T_("PRETTY_BAD")."",
					1 => "".T_("SUCKS")."",
			);
			//if (!$owned || $moderator) {
				$xres = mysql_query("SELECT rating, added FROM ratings WHERE torrent = $id AND user = " . $CURUSER["id"]);
				$xrow = mysql_fetch_array($xres);
				if ($xrow)
					$srating .= "<BR><i>(".T_("YOU_RATED")." \"" . $xrow["rating"] . " - " . $ratings[$xrow["rating"]] . "\")</i>";
				else {
					$srating .= "<form style=display:inline; method=\"post\" action=\"torrents-details.php?id=$id&takerating=yes\"><input type=\"hidden\" name=\"id\" value=\"$id\" />\n";
					$srating .= "<select name=\"rating\">\n";
					$srating .= "<option value=\"0\">(".T_("ADD_RATING").")</option>\n";
					foreach ($ratings as $k => $v) {
						$srating .= "<option value=\"$k\">$k - $v</option>\n";
					}
					$srating .= "</select>\n";
					$srating .= "<input type=\"submit\" value=\"".T_("VOTE")."\" />";
					$srating .= "</form>\n";
				}
			//}
		}
		$srating .= "</NOBR></td></tr></table>";

print("<CENTER>". $srating . "</CENTER>");// rating

//END DEFINE RATING VARIABLE

echo "<BR>";

if ($row["image1"] != "" OR $row["image2"] != "") {
  if ($row["image1"] != "")
    $img1 = "<IMG src=".$site_config["SITEURL"]."/uploads/images/$row[image1] width=150 border=0>";
  if ($row["image2"] != "")
    $img2 = "<IMG src=".$site_config["SITEURL"]."/uploads/images/$row[image2] width=150 border=0>";
  print("<CENTER>". $img1 . "&nbsp&nbsp" . $img2."</CENTER><BR>");
}



if ($row["external"]=='yes'){
	print ("<br><b>Tracker:</B><BR> ".$row['announce']."<br>");
}


$tres = mysql_query("SELECT * FROM `announce` WHERE `torrent` = $id");
if (mysql_num_rows($tres) > 1){
	echo "<br><B>".T_("THIS_TORRENT_HAS_BACKUP_TRACKERS")."</B><br>";
	echo '<table cellpadding="1" cellspacing="2" class="ttable_headinner"><tr>';
	echo '<td class="ttable_head">URL</td><td class="ttable_head">'.T_("SEEDERS").'</td><td class="ttable_head">'.T_("LEECHERS").'</td><td class="ttable_head">'.T_("COMPLETED").'</td></tr>';
	$x = 1;
	while ($trow = mysql_fetch_array($tres)) {
		$colour = $trow["online"] == "yes" ? "green" : "red";
		echo "<tr class=\"ttable_col$x\"><td><font color=\"$colour\"><b>".htmlspecialchars($trow['url'])."</b></font></td><td align=\"center\">".number_format($trow["seeders"])."</td><td align=\"center\">".number_format($trow["leechers"])."</td><td align=\"center\">".number_format($trow["times_completed"])."</td></tr>";
		$x = $x == 1 ? 2 : 1;
	}
	echo '</table>';
}

echo "<BR><BR><b>".T_("FILE_LIST").":</B>&nbsp;<img src='images/plus.gif' id='pic1' onclick='klappe_torrent(1)'><div id='k1' style='display: none;'><table align=center cellpadding=0 cellspacing=0 class=table_table border=1 width=95%><TR><TD class=table_head align=left>&nbsp;".T_("FILE")."</TD><TD width=50 class=table_head>&nbsp;".T_("SIZE")."</td></tr>";
$fres = mysql_query("SELECT * FROM `files` WHERE `torrent` = $id");
if (mysql_num_rows($fres)) {
    while ($frow = mysql_fetch_array($fres)) {
        echo "<TR><td class=table_col1>".htmlspecialchars($frow['path'])."</td><TD class=table_col2>".mksize($frow['filesize'])."</td></tr>";
    }
}else{
    echo "<TR><td class=table_col1>".htmlspecialchars($row["name"])."</td><TD class=table_col2>".mksize($row["size"])."</td></tr>";
}
echo "</table></div>";

if ($row["external"]!='yes'){
	echo "<BR><BR><B>".T_("PEERS_LIST").":</B><BR>";
	$query = mysql_query("SELECT * FROM peers WHERE torrent = $id ORDER BY seeder DESC");

	$result = mysql_num_rows($query);
		if($result == 0) {
			echo T_("NO_ACTIVE_PEERS")."\n";
		}else{
			?>
			<table align=center cellpadding="3" cellspacing="0" class="table_table" width="95%" border="1">
			<tr>
			<td class="table_head"><?php print(T_("PORT")); ?></td>
			<td class="table_head"><?php print(T_("UPLOADED")); ?></td>
			<td class="table_head"><?php print(T_("DOWNLOADED")); ?></td>
			<td class="table_head"><?php print(T_("RATIO")); ?></td>
			<td class="table_head"><?php print(T_("LEFT")); ?></td>
			<td class="table_head"><?php print(T_("FINISHED_SHORT"). "%"); ?></td>
			<td class="table_head"><?php print(T_("SEED")); ?></td>
			<td class="table_head"><?php print(T_("CONNECTED_SHORT")); ?></td>
			<td class="table_head"><?php print(T_("CLIENT")); ?></td>
			<td class="table_head"><?php print(T_("USER_SHORT")); ?></td>
			</tr>

			<?php
			while($row1 = MYSQL_FETCH_ARRAY($query))	{
				
				if ($row1["downloaded"] > 0){
					$ratio = $row1["uploaded"] / $row1["downloaded"];
					$ratio = number_format($ratio, 3);
				}else{
					$ratio = "---";
				}

				$percentcomp = sprintf("%.2f", 100 * (1 - ($row1["to_go"] / $row["size"])));    

				if ($site_config["MEMBERSONLY"]) {
					$res = mysql_query("SELECT id, username, privacy FROM users WHERE id=".$row1["userid"]."");
					$arr = MYSQL_FETCH_ARRAY($res);
				}
				$arr["username"];
				if ($arr["privacy"] != "strong" || ($CURUSER["control_panel"] == "yes")) {
					print("<tr><td class=table_col2>".$row1["port"]."</td><td class=table_col1>".mksize($row1["uploaded"])."</td><td class=table_col2>".mksize($row1["downloaded"])."</td><td class=table_col1>".$ratio."</td><td class=table_col2>".mksize($row1["to_go"])."</td><td class=table_col1>".$percentcomp."%</td><td class=table_col2>$row1[seeder]</td><td class=table_col1>$row1[connectable]</td><td class=table_col2>".htmlspecialchars($row1[client])."</td><td class=table_col1><a href=account-details.php?id=$arr[id]>$arr[username]</a></td></tr>");
				}else{
					print("<tr><td class=table_col2>".$row1["port"]."</td><td class=table_col1>".mksize($row1["uploaded"])."</td><td class=table_col2>".mksize($row1["downloaded"])."</td><td class=table_col1>".$ratio."</td><td class=table_col2>".mksize($row1["to_go"])."</td><td class=table_col1>".$percentcomp."%</td><td class=table_col2>$row1[seeder]</td><td class=table_col1>$row1[connectable]</td><td class=table_col2>".htmlspecialchars($row1[client])."</td><td class=table_col1>Private</td></tr>");
				}

			}
			echo "</table>";
	}
}


echo "<BR><BR>";

//DISPLAY NFO BLOCK
function my_nfo_translate($nfo){
        $trans = array(
        "\x80" => "&#199;", "\x81" => "&#252;", "\x82" => "&#233;", "\x83" => "&#226;", "\x84" => "&#228;", "\x85" => "&#224;", "\x86" => "&#229;", "\x87" => "&#231;", "\x88" => "&#234;", "\x89" => "&#235;", "\x8a" => "&#232;", "\x8b" => "&#239;", "\x8c" => "&#238;", "\x8d" => "&#236;", "\x8e" => "&#196;", "\x8f" => "&#197;", "\x90" => "&#201;",
        "\x91" => "&#230;", "\x92" => "&#198;", "\x93" => "&#244;", "\x94" => "&#246;", "\x95" => "&#242;", "\x96" => "&#251;", "\x97" => "&#249;", "\x98" => "&#255;", "\x99" => "&#214;", "\x9a" => "&#220;", "\x9b" => "&#162;", "\x9c" => "&#163;", "\x9d" => "&#165;", "\x9e" => "&#8359;", "\x9f" => "&#402;", "\xa0" => "&#225;", "\xa1" => "&#237;",
        "\xa2" => "&#243;", "\xa3" => "&#250;", "\xa4" => "&#241;", "\xa5" => "&#209;", "\xa6" => "&#170;", "\xa7" => "&#186;", "\xa8" => "&#191;", "\xa9" => "&#8976;", "\xaa" => "&#172;", "\xab" => "&#189;", "\xac" => "&#188;", "\xad" => "&#161;", "\xae" => "&#171;", "\xaf" => "&#187;", "\xb0" => "&#9617;", "\xb1" => "&#9618;", "\xb2" => "&#9619;",
        "\xb3" => "&#9474;", "\xb4" => "&#9508;", "\xb5" => "&#9569;", "\xb6" => "&#9570;", "\xb7" => "&#9558;", "\xb8" => "&#9557;", "\xb9" => "&#9571;", "\xba" => "&#9553;", "\xbb" => "&#9559;", "\xbc" => "&#9565;", "\xbd" => "&#9564;", "\xbe" => "&#9563;", "\xbf" => "&#9488;", "\xc0" => "&#9492;", "\xc1" => "&#9524;", "\xc2" => "&#9516;", "\xc3" => "&#9500;",
        "\xc4" => "&#9472;", "\xc5" => "&#9532;", "\xc6" => "&#9566;", "\xc7" => "&#9567;", "\xc8" => "&#9562;", "\xc9" => "&#9556;", "\xca" => "&#9577;", "\xcb" => "&#9574;", "\xcc" => "&#9568;", "\xcd" => "&#9552;", "\xce" => "&#9580;", "\xcf" => "&#9575;", "\xd0" => "&#9576;", "\xd1" => "&#9572;", "\xd2" => "&#9573;", "\xd3" => "&#9561;", "\xd4" => "&#9560;",
        "\xd5" => "&#9554;", "\xd6" => "&#9555;", "\xd7" => "&#9579;", "\xd8" => "&#9578;", "\xd9" => "&#9496;", "\xda" => "&#9484;", "\xdb" => "&#9608;", "\xdc" => "&#9604;", "\xdd" => "&#9612;", "\xde" => "&#9616;", "\xdf" => "&#9600;", "\xe0" => "&#945;", "\xe1" => "&#223;", "\xe2" => "&#915;", "\xe3" => "&#960;", "\xe4" => "&#931;", "\xe5" => "&#963;",
        "\xe6" => "&#181;", "\xe7" => "&#964;", "\xe8" => "&#934;", "\xe9" => "&#920;", "\xea" => "&#937;", "\xeb" => "&#948;", "\xec" => "&#8734;", "\xed" => "&#966;", "\xee" => "&#949;", "\xef" => "&#8745;", "\xf0" => "&#8801;", "\xf1" => "&#177;", "\xf2" => "&#8805;", "\xf3" => "&#8804;", "\xf4" => "&#8992;", "\xf5" => "&#8993;", "\xf6" => "&#247;",
        "\xf7" => "&#8776;", "\xf8" => "&#176;", "\xf9" => "&#8729;", "\xfa" => "&#183;", "\xfb" => "&#8730;", "\xfc" => "&#8319;", "\xfd" => "&#178;", "\xfe" => "&#9632;", "\xff" => "&#160;",
        );
        $trans2 = array("\xe4" => "&auml;",        "\xF6" => "&ouml;",        "\xFC" => "&uuml;",        "\xC4" => "&Auml;",        "\xD6" => "&Ouml;",        "\xDC" => "&Uuml;",        "\xDF" => "&szlig;");
        $all_chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $last_was_ascii = False;
        $tmp = "";
        $nfo = $nfo . "\00";
        for ($i = 0; $i < (strlen($nfo) - 1); $i++)
        {
                $char = $nfo[$i];
                if (isset($trans2[$char]) and ($last_was_ascii or strpos($all_chars, ($nfo[$i + 1]))))
                {
                        $tmp = $tmp . $trans2[$char];
                        $last_was_ascii = True;
                }
                else
                {
                        if (isset($trans[$char]))
                        {
                                $tmp = $tmp . $trans[$char];
                        }
                        else
                        {
                            $tmp = $tmp . $char;
                        }
                        $last_was_ascii = strpos($all_chars, $char);
                }
        }
        return $tmp;
}
//-----------------------------------------------

//DISPLAY NFO BLOCK
if($row["nfo"]== "yes"){
	$nfofilelocation = "$nfo_dir/$row[id].nfo";
	$filegetcontents = file_get_contents($nfofilelocation);
	$nfo = htmlspecialchars($filegetcontents);
		if ($nfo) {	
			$nfo = my_nfo_translate($nfo);
			echo "<BR><BR><b>NFO:</B><BR>";
			begin_table();
			print("<tr><td>\n");

			print("<textarea style=\"font-size:8pt;width:100%;height:100%;\" wrap=\"off\" rows=20 and cols=20 READONLY>".stripslashes($nfo)."</textarea>");
			end_table();
        }else{
            print(T_("ERROR")." reading .nfo file!");
        }
}
end_frame();

begin_frame(T_("COMMENTS"));
	//echo "<p align=center><a class=index href=torrents-comment.php?id=$id>" .T_("ADDCOMMENT"). "</a></p>\n";

	$subres = mysql_query("SELECT COUNT(*) FROM comments WHERE torrent = $id") or die(mysql_error());
	$subrow = mysql_fetch_array($subres);
	$commcount = $subrow[0];

	if ($commcount) {
		list($pagertop, $pagerbottom, $limit) = pager(10, $commcount, "torrents-details.php?id=$id&");
		$commquery = "SELECT comments.id, text, user, comments.added, avatar, signature, username, title, class, uploaded, downloaded, privacy, donated FROM comments LEFT JOIN users ON comments.user = users.id WHERE torrent = $id ORDER BY comments.id $limit";
		$commres = mysql_query($commquery) or die(mysql_error());
	}else{
		unset($commres);
	}

	if ($commcount) {
		print($pagertop);
		commenttable($commres);
		print($pagerbottom);
	}else {
		print("<BR><b><CENTER>" .T_("NOCOMMENTS"). "</CENTER></b><BR>\n");
	}

	require_once("backend/bbcode.php");

	if ($CURUSER) {
		echo "<CENTER>";
		echo "<form name=\"comment\" method=\"post\" action=\"torrents-details.php?id=$row[id]&takecomment=yes\">";
		echo "".textbbcode("comment","body")."<br>";
		echo "<input type=\"submit\" class=btn value=\"".T_("ADDCOMMENT")."\" />";
		echo "</form></CENTER>";
	}

	end_frame();

stdfoot();
?>
