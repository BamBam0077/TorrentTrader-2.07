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

// check access and rights
if ($site_config["MEMBERSONLY"]){
	loggedinonly();

	if($CURUSER["can_upload"]=="no")
		show_error_msg(T_("ERROR"), T_("UPLOAD_NO_PERMISSION"), 1);
	if ($site_config["UPLOADERSONLY"] && $CURUSER["class"] < 4)
		show_error_msg(T_("ERROR"), T_("UPLOAD_ONLY_FOR_UPLOADERS"), 1);
}

$announce_urls = explode(",", strtolower($site_config["announce_list"]));  //generate announce_urls[] from config.php

if($_POST["takeupload"] == "yes") {
	require_once("backend/parse.php");

	//check form data
	foreach(explode(":","type:name") as $v) {
		if (!isset($_POST[$v]))
			$message = T_("MISSING_FORM_DATA");
	}

	if (!isset($_FILES["torrent"]))
	$message = T_("MISSING_FORM_DATA");

	$f = $_FILES["torrent"];
	$fname = $f["name"];

	if (empty($fname))
		$message = "Empty filename!";

	if ($_FILES['nfo']['size'] != 0) {
		$nfofile = $_FILES['nfo'];

		if ($nfofile['name'] == '')
			$message = "No NFO!";
			
		if (!preg_match('/^(.+)\.nfo$/si', $nfofile['name'], $fmatches))
			$message = T_("UPLOAD_NOT_NFO");

		if ($nfofile['size'] == 0)
			$message = "0-byte NFO";

		if ($nfofile['size'] > 65535)
			$message = "NFO is too big! Max 65,535 bytes.";

		$nfofilename = $nfofile['tmp_name'];

		if (@!is_uploaded_file($nfofilename))
			$message = T_("UPLOAD_NFO_FAILED");
			$nfo = 'yes';
	}

	$descr = $_POST["descr"];

	if (!$descr)
		$descr = T_("UPLOAD_NO_DESC");

	$langid = (0 + $_POST["lang"]);
	
	/*if (!is_valid_id($langid))
		$message = "Please be sure to select a torrent language";*/

	$catid = (0 + $_POST["type"]);

	if (!is_valid_id($catid))
		$message = T_("UPLOAD_NO_CAT");

	if (!validfilename($fname))
		$message = T_("UPLOAD_INVALID_FILENAME");

	if (!preg_match('/^(.+)\.torrent$/si', $fname, $matches))
		$message = T_("UPLOAD_INVALID_FILENAME_NOT_TORRENT");

		$shortfname = $torrent = $matches[1];

	if (!empty($_POST["name"]))
		$torrent = $_POST["name"];

		$tmpname = $f["tmp_name"];

	if (!is_uploaded_file($tmpname))
		$message = T_("UPLOAD_NOT_FOUND_IN_TEMP");
	//end check form data

	if (!$message) {
	//parse torrent file
	$torrent_dir = $site_config["torrent_dir"];	
	$nfo_dir = $site_config["nfo_dir"];	

	//if(!copy($f, "$torrent_dir/$fname"))
	if(!move_uploaded_file($tmpname, "$torrent_dir/$fname"))
		show_error_msg(T_("ERROR"), T_("ERROR"). ": " . T_("UPLOAD_COULD_NOT_BE_COPIED")." $tmpname - $torrent_dir - $fname",1);

    $TorrentInfo = array();
    $TorrentInfo = ParseTorrent("$torrent_dir/$fname");


	$announce = $TorrentInfo[0];
	$infohash = $TorrentInfo[1];
	$creationdate = $TorrentInfo[2];
	$internalname = $TorrentInfo[3];
	$torrentsize = $TorrentInfo[4];
	$filecount = $TorrentInfo[5];
	$annlist = $TorrentInfo[6];
	$comment = $TorrentInfo[7];
	$filelist = $TorrentInfo[8];

/*
//for debug...
	print ("<BR><BR>announce: ".$announce."");
	print ("<BR><BR>infohash: ".$infohash."");
	print ("<BR><BR>creationdate: ".$creationdate."");
	print ("<BR><BR>internalname: ".$internalname."");
	print ("<BR><BR>torrentsize: ".$torrentsize."");
	print ("<BR><BR>filecount: ".$filecount."");
	print ("<BR><BR>annlist: ".$annlist."");
	print ("<BR><BR>comment: ".$comment."");
*/
	
	//check announce url is local or external
	if (!in_array($announce, $announce_urls, 1)){
		$external='yes';
    }else{
		$external='no';
	}

	//if externals is turned off
	if (!$site_config["ALLOWEXTERNAL"] && $external == 'yes')
		$message = T_("UPLOAD_NO_TRACKER_ANNOUNCE");
	}
	if ($message) {
		@unlink("$torrent_dir/$fname");
		@unlink($tmpname);
		@unlink("$nfo_dir/$nfofilename");
		show_error_msg(T_("UPLOAD_FAILED"), $message,1);
	}

	//release name check and adjust
	if ($name ==""){
		$name = $internalname;
	}
	$name = str_replace(".torrent","",$name);
	$name = str_replace("_", " ", $name);

	//upload images
	$maxfilesize = 512000; // 500kb

	$allowed_types = array(
		"image/gif" => "gif",
		"image/pjpeg" => "jpg",
		"image/jpeg" => "jpg",
		"image/jpg" => "jpg",
		"image/png" => "png"
	);

	for ($x=0; $x < 2; $x++) {
		if (!($_FILES[image.$x]['name'] == "")) {
			$y = $x + 1;

			if (!array_key_exists($_FILES[image.$x]['type'], $allowed_types))
				show_error_msg(T_("ERROR"), T_("INVALID_FILETYPE_IMAGE"), 1);
			
			if (!preg_match('/^(.+)\.(jpg|gif|png)$/si', $_FILES[image.$x]['name']))
				show_error_msg(T_("INVAILD_IMAGE"), T_("THIS_FILETYPE_NOT_IMAGE"), 1);

			if ($_FILES[image.$x]['size'] > $maxfilesize)
				show_error_msg(T_("ERROR"), T_("INVAILD_FILE_SIZE_IMAGE"), 1);

			$uploaddir = $site_config["torrent_dir"]."/images/";
   
			$ifile = $_FILES[image.$x]['tmp_name'];
             
            $image = getimagesize($ifile);
            
            if (!isset($image[2])) 
                 show_error_msg("Error", "Invalid Image.", 1);
   
			$ret = mysql_query("SHOW TABLE STATUS LIKE 'torrents'");
			$row = mysql_fetch_array($ret);
			$next_id = $row['Auto_increment'];

			$ifilename = $next_id . $x . substr($_FILES[image.$x]['name'], strlen($_FILES[image.$x]['name'])-4, 4);

			$copy = copy($ifile, $uploaddir.$ifilename);

			if (!$copy)
				show_error_msg(T_("ERROR"), T_("ERROR")." occured uploading image! - Image $y", 1);

			$inames[] = $ifilename;

		}

	}
	//end upload images

	//anonymous upload
	$anonyupload = $_POST["anonycheck"]; 
	if ($anonyupload == "yes") {
		$anon = "yes";
	}else{
		$anon = "no";
	}

	$ret = mysql_query("INSERT INTO torrents (filename, owner, name, descr, image1, image2, category, added, info_hash, size, numfiles, save_as, announce, external, nfo, torrentlang, anon, last_action) VALUES (".sqlesc($fname).", '".$CURUSER['id']."', ".sqlesc($name).", ".sqlesc($descr).", '".$inames[0]."', '".$inames[1]."', '".$type."', '" . get_date_time() . "', '".$infohash."', '".$torrentsize."', '".$filecount."', ".sqlesc($fname).", '".$announce."', '".$external."', '".$nfo."', '".$langid."','$anon', '".get_date_time()."')");

	$id = mysql_insert_id();
	
	if (mysql_errno() == 1062)
		show_error_msg(T_("UPLOAD_FAILED"), T_("UPLOAD_ALREADY_UPLOADED"), 1);

	//Update the members uploaded torrent count
	/*if ($ret){
		mysql_query("UPDATE users SET torrents = torrents + 1 WHERE id = $userid");*/
        
	if($id == 0){
		unlink("$torrent_dir/$fname");
		$message = T_("UPLOAD_NO_ID");
		show_error_msg(T_("UPLOAD_FAILED"), $message, 1);
	}
    
    rename("$torrent_dir/$fname", "$torrent_dir/$id.torrent"); 

	if (count($filelist)) {
		foreach ($filelist as $file) {
			$dir = '';
			$size = $file["length"];
			$count = count($file["path"]);
			for ($i=0; $i<$count;$i++) {
				if (($i+1) == $count)
					$fname = $dir.$file["path"][$i];
				else
					$dir .= $file["path"][$i]."/";
			}
			mysql_query("INSERT INTO `files` (`torrent`, `path`, `filesize`) VALUES($id, ".sqlesc($fname).", $size)");
		}
	} else {
		mysql_query("INSERT INTO `files` (`torrent`, `path`, `filesize`) VALUES($id, ".sqlesc($TorrentInfo[3]).", $torrentsize)");
	}

	if (!count($annlist)) {
		$annlist = array(array($announce));
	}
	foreach ($annlist as $ann) {
		foreach ($ann as $val) {
			if (strtolower(substr($val, 0, 4)) != "udp:") {
				mysql_query("INSERT INTO `announce` (`torrent`, `url`) VALUES($id, ".sqlesc($val).")");
			}
		}
	}

	if ($nfo == 'yes') { 
            move_uploaded_file($nfofilename, "$nfo_dir/$id.nfo"); 
    } 

	//EXTERNAL SCRAPE
	if ($external=='yes' && $site_config['UPLOADSCRAPE']){
		$tracker=str_replace("/announce","/scrape",$announce);	
		$stats 			= torrent_scrape_url($tracker, $infohash);
		$seeders 		= strip_tags($stats['seeds']);
		$leechers 		= strip_tags($stats['peers']);
		$downloaded 	= strip_tags($stats['downloaded']);

		mysql_query("UPDATE torrents SET leechers='".$leechers."', seeders='".$seeders."',times_completed='".$downloaded."',last_action= '".get_date_time()."',visible='yes' WHERE id='".$id."'"); 
	}
	//END SCRAPE

	write_log("Torrent $id (".htmlspecialchars($name).") was Uploaded by $CURUSER[username]");

	//insert email notif, irc, req notif, etc here
	
	//Uploaded ok message (update later)
	if ($external=='no')
		$message = T_("UPLOAD_OK").":<BR><BR>".$name." was uploaded.<BR><BR>  ".T_("UPLOAD_OK_MSG")."<BR><BR><a href=download.php?id=".$id.">".T_("DOWNLOAD_NOW")."</a><BR><a href=torrents-details.php?id=".$id.">".T_("UPLOAD_VIEW_DL")."</a><BR><BR>";
	else
		$message = "".T_("UPLOAD_OK").":<BR><BR>".$name." was uploaded.<BR><BR><a href=torrents-details.php?id=".$id.">".T_("UPLOAD_VIEW_DL")."</a><BR><BR>";
	show_error_msg(T_("UPLOAD_COMPLETE"), $message, 1);

	die();
}//takeupload


///////////////////// FORMAT PAGE ////////////////////////

stdhead(T_("UPLOAD"));

begin_frame(T_("UPLOAD_RULES"));
	echo "<b>".stripslashes($site_config["UPLOADRULES"])."</b>";
	echo "<BR>";
end_frame();

begin_frame(T_("UPLOAD"));
?>
<form name="upload" enctype="multipart/form-data" action="torrents-upload.php" method="post">
<input type="hidden" name="takeupload" value="yes" />
<table border="0" cellspacing="0" cellpadding="6" align="center">
<?php
print ("<TR><TD align=right valign=top>" . T_("ANNOUNCE_URL") . ": </td><td align=left>");

while (list($key,$value) = each($announce_urls)) {
	echo "<b>$value</B><br>";
}

if ($site_config["ALLOWEXTERNAL"]){
	echo "<BR><b>".T_("THIS_SITE_ACCEPTS_EXTERNAL")."</B>";
}
print ("</td></tr>");
print ("<TR><TD align=right>" . T_("TORRENT_FILE") . ": </td><td align=left> <input type=file name=torrent size=50 value=" . $_FILES['torrent']['name'] . ">\n</td></tr>");
print ("<TR><TD align=right>" .T_("NFO"). ": </td><td align=left> <input type=file name=nfo size=50 value=" . $_FILES['nfo']['name'] . "><br />\n</td></tr>");
print ("<TR><TD align=right>" . T_("TORRENT_NAME") . ": </td><td align=left><input type=text name=name size=60 value=" . $_POST['name'] . "><BR>".T_("THIS_WILL_BE_TAKEN_TORRENT")." \n</td></tr>");
print ("<TR><TD align=right>".T_("IMAGE")."</b>: </td><td align=left>Max File Size: 500kb<br>Accepted Formats: .gif, .jpg, .png<br><b>".T_("IMAGE")." 1:</b>&nbsp&nbsp<input type=file name=image0 size=50><br><b>".T_("IMAGE")." 2:</b>&nbsp&nbsp<input type=file name=image1 size=50>\n</td></tr>");

$category = "<select name=\"type\">\n<option value=\"0\">" . T_("CHOOSE_ONE") . "</option>\n";

$cats = genrelist();
foreach ($cats as $row)
	$category .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["parent_cat"]) . ": " . htmlspecialchars($row["name"]) . "</option>\n";

$category .= "</select>\n";
print ("<TR><TD align=right>" . T_("CATEGORY") . ": </td><td align=left>".$category."</td></tr>");


$language = "<select name=\"lang\">\n<option value=\"0\">Unknown/NA</option>\n";

$langs = langlist();
foreach ($langs as $row)
	$language .= "<option value=\"" . $row["id"] . "\">" . htmlspecialchars($row["name"]) . "</option>\n";

$language .= "</select>\n";
print ("<TR><TD align=right>".T_("LANGAUGE").": </td><td align=left>".$language."</td></tr>");

if ($site_config['ANONYMOUSUPLOAD'] && $site_config["MEMBERSONLY"] ){ ?>
	<TR><TD align=right><?php echo T_("UPLOAD_ANONY");?>: </td><td><?php printf("<input name=anonycheck value=yes type=radio" . ($anonycheck ? " checked" : "") . ">Yes <input name=anonycheck value=no type=radio" . (!$anonycheck ? " checked" : "") . ">No"); ?> &nbsp;<I><?php echo T_("UPLOAD_ANONY_MSG");?></font>
	</td></tr>
	<?php
}

print ("<TR><TD align=center colspan=2>" . T_("DESCRIPTION") . "</td></tr></table>");

require_once("backend/bbcode.php");
print ("".textbbcode("upload","descr","$descr")."");
?>

<BR><BR><CENTER><input type="submit" value="<?php echo T_("UPLOAD_TORRENT"); ?>"><BR>
<I><?php echo T_("CLICK_ONCE_IMAGE");?></I>
</CENTER>
</form>

<?php
end_frame();
stdfoot();
?>