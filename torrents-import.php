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

$dir = "import";

//ini_set("upload_max_filesize",$max_torrent_size);

$files = array();
$dh = opendir("$dir/");
while (false !== ($file=readdir($dh))) {
	if (preg_match("/\.torrent$/i", $file))
		$files[] = $file;
}
closedir($dh);


// check access and rights
if ($CURUSER["edit_torrents"] != "yes")
	show_error_msg(T_("ERROR"), T_("ACCESS_DENIED"), 1);

$announce_urls = explode(",", strtolower($site_config["announce_list"]));  //generate announce_urls[] from config.php

if ($_POST["takeupload"] == "yes") {
	set_time_limit(0);
	require_once("backend/parse.php");
	stdhead(T_("UPLOAD_COMPLETE"));
	begin_frame(T_("UPLOAD_COMPLETE"));
	echo "<center>";

	//check form data
	$catid = (int)$_POST["type"];

	if (!is_valid_id($catid))
		$message = T_("UPLOAD_NO_CAT");
	
	if (empty($message)) {
		$r = mysql_query("SELECT name, parent_cat FROM categories WHERE id=$catid");
		$r = mysql_fetch_row($r);

		echo "<b>Category:</B> ".htmlspecialchars($r[1])." -> ".htmlspecialchars($r[0])."<BR>";
		for ($i=0;$i<count($files);$i++) {
			$fname = $files[$i];

			$descr = T_("UPLOAD_NO_DESC");

			$langid = (int)$_POST["lang"];
	
			preg_match('/^(.+)\.torrent$/si', $fname, $matches);
			$shortfname = $torrent = $matches[1];

			//parse torrent file
			$torrent_dir = $site_config["torrent_dir"];	

			$TorrentInfo = array();
			$TorrentInfo = ParseTorrent("$dir/$fname");


			$announce = strtolower($TorrentInfo[0]);
			$infohash = $TorrentInfo[1];
			$creationdate = $TorrentInfo[2];
			$internalname = $TorrentInfo[3];
			$torrentsize = $TorrentInfo[4];
			$filecount = $TorrentInfo[5];
			$annlist = $TorrentInfo[6];
			$comment = $TorrentInfo[7];
			
			$message = "<BR><BR><HR><BR><b>$internalname</B><BR><BR>fname: ".htmlspecialchars($fname)."<BR>message: ";

			//check announce url is local or external
			if (!in_array($announce, $announce_urls, 1))
				$external='yes';
			else
				$external='no';

			if (!$site_config["ALLOWEXTERNAL"] && $external == 'yes') {
				$message .= T_("UPLOAD_NO_TRACKER_ANNOUNCE");
				echo $message;
				continue;
			}

			$name = $internalname;
			$name = str_replace(".torrent","",$name);
			$name = str_replace("_", " ", $name);

			//anonymous upload
			$anonyupload = $_POST["anonycheck"]; 
			if ($anonyupload == "yes")
				$anon = "yes";
			else
				$anon = "no";

			$ret = mysql_query("INSERT INTO torrents (filename, owner, name, descr, image1, image2, category, added, info_hash, size, numfiles, save_as, announce, external, nfo, torrentlang, anon) VALUES (".sqlesc($fname).", '".$CURUSER['id']."', ".sqlesc($name).", ".sqlesc($descr).", '".$inames[0]."', '".$inames[1]."', '".catid."', '" . get_date_time() . "', '".$infohash."', '".$torrentsize."', '".$filecount."', ".sqlesc($fname).", '".$announce."', '".$external."', '".$nfo."', '".$langid."','$anon')");

			$id = mysql_insert_id();
	
			if (mysql_errno() == 1062) {
				$message .= T_("UPLOAD_ALREADY_UPLOADED");
				echo $message;
				continue;
			}

			if($id == 0){
				$message .= T_("UPLOAD_NO_ID");
				echo $message;
				continue;
			}
    
			copy("$dir/$files[$i]", "uploads/$id.torrent");

			//EXTERNAL SCRAPE
			if ($external == "yes") {
				$tracker=str_replace("/announce","/scrape",$announce);	
				$stats 			= torrent_scrape_url($tracker, $infohash);
				$seeders 		= strip_tags($stats['seeds']);
				$leechers 		= strip_tags($stats['peers']);
				$downloaded 	= strip_tags($stats['downloaded']);

				mysql_query("UPDATE torrents SET leechers='".$leechers."', seeders='".$seeders."',times_completed='".$downloaded."',last_action= '".get_date_time()."',visible='yes' WHERE id='".$id."'"); 
			}
			//END SCRAPE

			write_log("Torrent $id ($name) was Uploaded by $CURUSER[username]");

			$message .= "<BR><b>".T_("UPLOAD_OK")."</B><BR><a href=torrents-details.php?id=".$id.">".T_("UPLOAD_VIEW_DL")."</a><BR><BR>";
			echo $message;
			@unlink("$dir/$fname");
		}
	echo "</center>";
	end_frame();
	stdfoot();
	die;
	}else
		show_error_msg(T_("UPLOAD_FAILED"), $message, 1);

}//takeupload


///////////////////// FORMAT PAGE ////////////////////////

stdhead(T_("UPLOAD"));

begin_frame(T_("UPLOAD"));
?>
<form name="upload" enctype="multipart/form-data" action="torrents-import.php" method="post">
<input type="hidden" name="takeupload" value="yes" />
<table border="0" cellspacing="0" cellpadding="6" align="center">
<TR><TD align=right valign=top><b>File List:</B></TD><TD align=left><?php
if (!count($files))
	echo T_("NOTHING_TO_SHOW_FILES")." $dir/.";
else{
	foreach ($files as $f)
		echo htmlspecialchars($f)."<BR>";
	echo "<BR>Total files: ".count($files);
}?></TD></TR>
<?php
$category = "<select name=\"type\">\n<option value=\"0\">" .T_("CHOOSE_ONE"). "</option>\n";

$cats = genrelist();
foreach ($cats as $row)
	$category .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["parent_cat"]) . ": " . htmlspecialchars($row["name"]) . "</option>\n";

$category .= "</select>\n";
print ("<TR><TD align=right>" .T_("CATEGORY"). ": </td><td align=left>".$category."</td></tr>");


$language = "<select name=\"lang\">\n<option value=\"0\">Unknown/NA</option>\n";

$langs = langlist();
foreach ($langs as $row)
	$language .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";

$language .= "</select>\n";
print ("<TR><TD align=right>Language: </td><td align=left>".$language."</td></tr>");

if ($site_config['ANONYMOUSUPLOAD']){ ?>
	<TR><TD align=right><?php echo T_("UPLOAD_ANONY");?>: </td><td><?php printf("<input name=anonycheck value=yes type=radio" . ($anonycheck ? " checked" : "") . ">Yes <input name=anonycheck value=no type=radio" . (!$anonycheck ? " checked" : "") . ">No"); ?> &nbsp;<I><?php echo T_("UPLOAD_ANONY_MSG");?></font>
	</td></tr>

<?php } ?>
<TR><TD align=center colspan=2><input type="submit" value="<?php echo T_("UPLOAD")?>"><BR>
<I><?php echo T_("CLICK_ONCE_IMAGE");?></I></form></TD></TR></TABLE>
<?php
end_frame();
stdfoot();
?>