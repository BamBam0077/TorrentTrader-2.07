<?php
//
//  TorrentTrader v2.x
//	$LastChangedDate: 2011-06-04 15:19:54 +0100 (Sat, 04 Jun 2011) $
//      $LastChangedBy: torrentialstorm $
//	
//	http://www.torrenttrader.org
//
//

// VERY BASIC ADMINCP

require_once ("backend/functions.php");
require_once ("backend/bbcode.php");
dbconn(false);

loggedinonly();

if (!$CURUSER || $CURUSER["control_panel"]!="yes"){
 show_error_msg(T_("ERROR"), T_("SORRY_NO_RIGHTS_TO_ACCESS"), 1);
}




function navmenu(){
global $site_config;

//Get Last Cleanup
$res = mysql_query("SELECT last_time FROM tasks WHERE task = 'cleanup'");
$row = mysql_fetch_array($res);
if (!$row){
		$lastclean="never done...";
}else{
	$row[0]=gmtime()-$row[0]; $days=intval($row[0] / 86400);$row[0]-=$days*86400;
	$hours=intval($row[0] / 3600); $row[0]-=$hours*3600; $mins=intval($row[0] / 60);
	$secs=$row[0]-($mins*60);
	$lastclean = "$days days, $hours hrs, $mins minutes, $secs seconds ago.";
}

	begin_frame(T_("MENU"));
	print "Last cleanup performed: ".$lastclean." [<a href=admincp.php?action=forceclean>".T_("FORCE_CLEAN")."</a>]<BR><BR>";

	if ($site_config["ttversion"] != "2-svn") {
		$file = @file_get_contents('http://www.torrenttrader.org/tt2version.php');
		if ($site_config['ttversion'] >= $file){
			echo "<BR><center><b>".T_("YOU_HAVE_LATEST_VER_INSTALLED")." v$site_config[ttversion]</b></center>";
		}else{
			echo "<BR><center><b><font color=red>".T_("NEW_VERSION_OF_TT_NOW_AVAIL").": v".$file." you have v".$site_config['ttversion']."<BR> Please visit <a href=http://www.torrenttrader.org>TorrentTrader.org</a> to upgrade.</font></b></center>";
		}
	}

	$res = mysql_query("SELECT VERSION() AS mysql_version");
    $row = mysql_fetch_array($res);
    $mysqlver = $row['mysql_version']; 

	$pending = get_row_count("users", "WHERE status='pending'");
	echo "<CENTER><b>".T_("USERS_AWAITING_VALIDATION").":</b> <a href='admincp.php?action=confirmreg'>($pending)</a></CENTER><BR>";

	echo "<CENTER>".T_("VERSION_MYSQL").": <b>" . $mysqlver . "</B><BR>".T_("VERSION_PHP").": <b>" . phpversion() . "</B></CENTER>";

?>
<p align="center"><table border="0" width="100%" cellspacing="0" cellpadding="0">

<tr><td align="center"><a href=admincp.php?action=usersearch><img src="images/admin/userssearch.gif " border=0 width=32 height=32><BR><?php echo T_("ADVANCED_USER_SEARCH"); ?></a><BR></td>
<td align="center"><a href=admincp.php?action=avatars><img src="images/admin/avatar.gif" border=0 width=32 height=32><BR><?php echo T_("AVATAR_LOG"); ?></a><BR></td>
<td align="center"><a href=admincp.php?action=backups><img src="images/admin/database.png" border=0 width=32 height=32><BR><?php echo T_("BACKUPS"); ?></a><BR></td>
<td align="center"><a href=admincp.php?action=ipbans><img src="images/admin/blocked.gif" border=0 width=32 height=32><BR><?php echo T_("BANNED_IPS"); ?></a><BR></td>
<td align="center"><a href=admincp.php?action=bannedtorrents><img src="images/admin/bannedtorrents.gif" border=0 width=32 height=32><BR><?php echo T_("BANNED_TORRENTS"); ?></a><BR></td>
</tr>
<tr><td colspan=5>&nbsp;</td></tr>
<tr>
<td align="center"><a href=admincp.php?action=blocks&do=view><img src="images/admin/banners.gif" border=0 width=32 height=32><BR><?php echo T_("BLOCKS"); ?></a><BR></td>
<td align="center"><a href=admincp.php?action=cheats><img src="images/admin/blocked.gif" border=0 width=32 height=32><BR><?php echo T_("DETECT_POSS_CHEATS"); ?></a><BR></td>
<td align="center"><a href=admincp.php?action=emailbans><img src="images/admin/mail.gif" border=0 width=32 height=32><BR><?php echo T_("EMAIL_BANS"); ?></a><BR></td>
<td align="center"><a href=faq-manage.php><img src="images/admin/faq.png" border=0 width=32 height=32><BR><?php echo T_("FAQ"); ?></a><BR></td>
<td align="center"><a href=admincp.php?action=freetorrents><img src="images/admin/external.gif" border=0 width=32 height=32><BR><?php echo T_("FREE_LEECH_TORRENTS"); ?></a><BR></td>
</tr>
<tr><td colspan=5>&nbsp;</td></tr>
<tr>
<td align="center"><a href=admincp.php?action=lastcomm><img src="images/admin/forums.gif" border=0 width=32 height=32><BR><?php echo T_("LATEST_COMMENTS"); ?></a><BR></td>
<td align="center"><a href=admincp.php?action=masspm><img src="images/admin/massmessage.gif" border=0 width=32 height=32><BR><?php echo T_("MASS_PM"); ?></a><BR></td>
<td align="center"><a href=admincp.php?action=messagespy><img src="images/admin/messagespy.gif" border=0 width=32 height=32><BR><?php echo T_("MESSAGE_SPY"); ?></a><BR></td>
<td align="center"><a href=admincp.php?action=news&do=view><img src="images/admin/news.png" border=0 width=32 height=32><BR><?php echo T_("NEWS"); ?></a><BR></td>
<td align="center"><a href=admincp.php?action=peers><img src="images/admin/list_peers.png" border=0 width=32 height=32><BR><?php echo T_("PEERS_LIST"); ?></a><BR></td>
</tr>
<tr><td colspan=5>&nbsp;</td></tr>
<tr>
<td align="center"><a href=admincp.php?action=polls&do=view><img src="images/admin/uploadervote.gif" border=0 width=32 height=32><BR><?php echo T_("POLLS"); ?></a><BR></td>
<td align="center"><a href=admincp.php?action=reports&do=view><img src="images/admin/requests.gif" border=0 width=32 height=32><BR><?php echo T_("REPORTS"); ?></a><BR></td>
<td align="center"><a href=admincp.php?action=rules&do=view><img src="images/admin/rules.gif" border=0 width=32 height=32><BR><?php echo T_("RULES"); ?></a><BR></td>
<td align="center"><a href=admincp.php?action=sitelog><img src="images/admin/log.gif" border=0 width=32 height=32><BR><?php echo T_("SITELOG"); ?></a><BR></td>
<td align="center"><a href=teams-create.php><img src="images/admin/userssearch.gif" border=0 width=32 height=32><BR><?php echo T_("TEAMS"); ?></a><BR></td>
</tr>
<tr><td colspan=5>&nbsp;</td></tr>
<tr>
<td align="center"><a href=admincp.php?action=style><img src="images/admin/themes.gif" border=0 width=32 height=32><BR><?php echo T_("THEME_MANAGEMENT"); ?></a><BR></td>
<td align="center"><a href=admincp.php?action=categories&do=view><img src="images/admin/categories.gif" border=0 width=32 height=32><BR><?php echo T_("TORRENT_CAT_VIEW"); ?></a><BR></td>
<td align="center"><a href=admincp.php?action=torrentlangs&do=view><img src="images/admin/langs.png" border=0 width=32 height=32><BR><?php echo T_("TORRENT_LANG"); ?></a><BR></td>
<td align="center"><a href=admincp.php?action=torrentmanage><img src="images/admin/torrents.gif" border=0 width=32 height=32><BR><?php echo T_("TORRENTS"); ?></a><BR></td>
<td align="center"><a href=admincp.php?action=groups&do=view><img src="images/admin/usersgrp.gif" border=0 width=32 height=32><BR><?php echo T_("USER_GROUPS_VIEW"); ?></a><BR></td>
</tr>
<tr><td colspan=5>&nbsp;</td></tr>
<tr>
<td align="center"><a href=admincp.php?action=warned><img src="images/admin/warnedaccounts.gif" border=0 width=32 height=32><BR><?php echo T_("WARNED_USERS"); ?></a><BR></td>
<td align="center"><a href=admincp.php?action=whoswhere><img src="images/admin/ipchecker.gif" border=0 width=32 height=32><BR><?php echo T_("WHOS_WHERE"); ?></a><BR></td>
<td align="center"><a href=admincp.php?action=censor><img src="images/admin/censor.png" border=0 width=32 height=32><BR><?php echo T_("WORD_CENSOR"); ?></a><BR></td>
</tr>

</table></p>
<?php
	end_frame();
}


if (!$action){
	stdhead(T_("ADMIN_CP"));
	navmenu();
	stdfoot();
}

/////////////////////// GROUPS MANAGEMENT ///////////////////////
if ($action=="groups" && $do=="view"){
	stdhead(T_("GROUPS_MANAGEMENT"));
	navmenu();


	begin_frame(T_("GROUPS_USER"));
	print("<CENTER><a href=admincp.php?action=groups&do=add>".T_("GROUPS_ADD_NEW")."</a></CENTER>\n");

	print("<br><br>\n<table width=\"100%\" align=\"center\" border=1 class=table_table>\n");
	print("<tr>\n");
	print("<td class=table_head>Name</td>\n");
	print("<td class=table_head>Torrents<br>".T_("GROUPS_VIEW_EDIT_DEL")."</td>\n");
	print("<td class=table_head>Members<br>".T_("GROUPS_VIEW_EDIT_DEL")."</td>\n");
	print("<td class=table_head>News<br>".T_("GROUPS_VIEW_EDIT_DEL")."</td>\n");
	print("<td class=table_head>Forum<br>".T_("GROUPS_VIEW_EDIT_DEL")."</td>\n");
	print("<td class=table_head>Upload</td>\n");
	print("<td class=table_head>Download</td>\n");
	print("<td class=table_head>View CP</td>\n");
	print("<td class=table_head>Delete</td>\n");
	print("</tr>\n");

	$getlevel=mysql_query("SELECT * from groups ORDER BY group_id");
	while ($level=mysql_fetch_array($getlevel)) {
		 print("<tr>\n");
		 print("<td class=table_col1><a href=admincp.php?action=groups&do=edit&group_id=".$level["group_id"].">".$level["level"]."<a></td>\n");
		 print("<td class=table_col2>".$level["view_torrents"]."/".$level["edit_torrents"]."/".$level["delete_torrents"]."</td>\n");
		 print("<td class=table_col1>".$level["view_users"]."/".$level["edit_users"]."/".$level["delete_users"]."</td>\n");
		 print("<td class=table_col2>".$level["view_news"]."/".$level["edit_news"]."/".$level["delete_news"]."</td>\n");
		 print("<td class=table_col1>".$level["view_forum"]."/".$level["edit_forum"]."/".$level["delete_forum"]."</td>\n");
		 print("<td class=table_col2>".$level["can_upload"]."</td>\n");
		 print("<td class=table_col1>".$level["can_download"]."</td>\n");
		 print("<td class=table_col2>".$level["control_panel"]."</td>\n");
		 print("<td class=table_col1><a href=admincp.php?action=groups&do=delete&group_id=".$level["group_id"].">Del<a></td>\n");

		 print("</tr>\n");
	}

	print("</table><BR><BR>");
	end_frame();
	stdfoot();
}

if ($action=="groups" && $do=="edit"){
	$group_id=intval($_GET["group_id"]);
	$rlevel=mysql_query("SELECT * FROM groups WHERE group_id=$group_id");
	if (!$rlevel)
		show_error_msg("ERROR","No Goup with that ID found",1);

	$level=mysql_fetch_array($rlevel);

	stdhead(T_("GROUPS_MANAGEMENT"));
	navmenu();


	begin_frame("Edit Group");
	?>
	<form action="admincp.php?action=groups&do=update&group_id=<?php echo $level["group_id"]; ?>" name="level" method="post">
	<table width="100%" align="center">
	<tr><td>Name:</td><td><input type="text" name="gname" value="<?php echo $level["level"];?>" size="40" /></td></tr>
	<tr><td>View Torrents:</td><td>  <?php echo T_("YES");?> <input type="radio" name="vtorrent" value="yes" <?php if ($level["view_torrents"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="vtorrent" value="no" <?php if ($level["view_torrents"]=="no") echo "checked" ?> /></td></tr>
	<tr><td>Edit Torrents:</td><td>  <?php echo T_("YES");?> <input type="radio" name="etorrent" value="yes" <?php if ($level["edit_torrents"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="etorrent" value="no" <?php if ($level["edit_torrents"]=="no") echo "checked" ?> /></td></tr>
	<tr><td>Delete Torrents:</td><td>  <?php echo T_("YES");?> <input type="radio" name="dtorrent" value="yes" <?php if ($level["delete_torrents"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="dtorrent" value="no" <?php if ($level["delete_torrents"]=="no") echo "checked" ?> /></td></tr>
	<tr><td>View Users:</td><td>  <?php echo T_("YES");?> <input type="radio" name="vuser" value="yes" <?php if ($level["view_users"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="vuser" value="no" <?php if ($level["view_users"]=="no") echo "checked" ?> /></td></tr>
	<tr><td>Edit Users:</td><td>  <?php echo T_("YES");?> <input type="radio" name="euser" value="yes" <?php if ($level["edit_users"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="euser" value="no" <?php if ($level["edit_users"]=="no") echo "checked" ?> /></td></tr>
	<tr><td>Delete Users:</td><td>  <?php echo T_("YES");?> <input type="radio" name="duser" value="yes" <?php if ($level["delete_users"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="duser" value="no" <?php if ($level["delete_users"]=="no") echo "checked" ?> /></td></tr>
	<tr><td>View News:</td><td>  <?php echo T_("YES");?> <input type="radio" name="vnews" value="yes" <?php if ($level["view_news"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="vnews" value="no" <?php if ($level["view_news"]=="no") echo "checked" ?> /></td></tr>
	<tr><td>Edit News:</td><td>  <?php echo T_("YES");?> <input type="radio" name="enews" value="yes" <?php if ($level["edit_news"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="enews" value="no" <?php if ($level["edit_news"]=="no") echo "checked" ?> /></td></tr>
	<tr><td>Delete News:</td><td> <?php echo T_("YES");?> <input type="radio" name="dnews" value="yes" <?php if ($level["delete_news"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="dnews" value="no" <?php if ($level["delete_news"]=="no") echo "checked" ?> /></td></tr>
	<tr><td>View Forums:</td><td>  <?php echo T_("YES");?> <input type="radio" name="vforum" value="yes" <?php if ($level["view_forum"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="vforum" value="no" <?php if ($level["view_forum"]=="no") echo "checked" ?> /></td></tr>
	<tr><td>Edit In Forums:</td><td>  <?php echo T_("YES");?> <input type="radio" name="eforum" value="yes" <?php if ($level["edit_forum"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="eforum" value="no" <?php if ($level["edit_forum"]=="no") echo "checked" ?> /></td></tr>
	<tr><td>Delete In Forums:</td><td>  <?php echo T_("YES");?> <input type="radio" name="dforum" value="yes" <?php if ($level["delete_forum"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="dforum" value="no" <?php if ($level["delete_forum"]=="no") echo "checked" ?> /></td></tr>
	<tr><td>Can Upload:</td><td>  <?php echo T_("YES");?> <input type="radio" name="upload" value="yes" <?php if ($level["can_upload"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="upload" value="no" <?php if ($level["can_upload"]=="no") echo "checked" ?> /></td></tr>
	<tr><td>Can Download:</td><td>  <?php echo T_("YES");?> <input type="radio" name="down" value="yes" <?php if ($level["can_download"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="down" value="no" <?php if ($level["can_download"]=="no") echo "checked" ?> /></td></tr>
	<tr><td>Can View CP:</td><td>  <?php echo T_("YES");?> <input type="radio" name="admincp" value="yes" <?php if ($level["control_panel"]=="yes") echo "checked" ?> />&nbsp;&nbsp; <?php echo T_("NO");?> <input type="radio" name="admincp" value="no" <?php if ($level["control_panel"]=="no") echo "checked" ?> /></td></tr>
	<?php
	print("\n<tr><td align=\"center\" class=\"header\"><input type=\"submit\" name=\"write\" value=\"CONFIRM\" /></td></tr>");
	print("</table></form><BR><BR>");
	end_frame();
	stdfoot();
}

if ($action=="groups" && $do=="update"){
		stdhead(T_("GROUPS_MANAGEMENT"));
		navmenu();

		begin_frame("Update");

		 $update=array();
		 $update[]="level='".mysql_escape_string($_POST["gname"])."'";
		 $update[]="view_torrents='".$_POST["vtorrent"]."'";
		 $update[]="edit_torrents='".$_POST["etorrent"]."'";
		 $update[]="delete_torrents='".$_POST["dtorrent"]."'";
		 $update[]="view_users='".$_POST["vuser"]."'";
		 $update[]="edit_users='".$_POST["euser"]."'";
		 $update[]="delete_users='".$_POST["duser"]."'";
		 $update[]="view_news='".$_POST["vnews"]."'";
		 $update[]="edit_news='".$_POST["enews"]."'";
		 $update[]="delete_news='".$_POST["dnews"]."'";
		 $update[]="view_forum='".$_POST["vforum"]."'";
		 $update[]="edit_forum='".$_POST["eforum"]."'";
		 $update[]="delete_forum='".$_POST["dforum"]."'";
		 $update[]="can_upload='".$_POST["upload"]."'";
		 $update[]="can_download='".$_POST["down"]."'";
		 $update[]="control_panel='".$_POST["admincp"]."'";
		 $strupdate=implode(",",$update);

		 $group_id=intval($_GET["group_id"]);
		 mysql_query("UPDATE groups SET $strupdate WHERE group_id=$group_id") or die(mysql_error());

		echo "<BR><center><b>Updated OK</b></center><BR>";
		end_frame();
		stdfoot();	
}

if ($action=="groups" && $do=="delete"){
		//Needs to be secured!!!!
		$group_id=intval($_GET["group_id"]);
		if (($group_id=="1") || ($group_id=="7"))
			show_error_msg("ERROR","You cannot delete this group!",1);

		stdhead(T_("GROUPS_MANAGEMENT"));

		navmenu();

		begin_frame(T_("_DEL_"));
		mysql_query("DELETE FROM groups WHERE group_id=$group_id") or die(mysql_error());
		echo "<BR><center><b>Deleted OK</b></center><BR>";
		end_frame();
		stdfoot();	
}


if ($action=="groups" && $do=="add") {
	stdhead(T_("GROUPS_MANAGEMENT"));

	navmenu();

	begin_frame(T_("GROUPS_ADD_NEW"));
	?>
	<form action="admincp.php?action=groups&do=addnew" name="level" method="post">
	<table width="100%" align="center">
	<tr><td>Group Name:</td><td><input type="text" name="gname" value="" size="40" /></td></tr>
	<tr><td>Copy Settings From: </td><td><select name="getlevel" size="1">
	<?php
	$rlevel=mysql_query("SELECT DISTINCT group_id, level FROM groups ORDER BY group_id");

	while($level=mysql_fetch_array($rlevel)) {
		print("\n<option value=".$level["group_id"].">".$level["level"]."</option>");
	}
	print("\n</select></td></tr>");
	print("\n<tr><td align=\"center\" class=\"header\"><input type=\"submit\" name=\"confirm\" value=\"Confirm\" /></td></tr>");
	print("</table></form><BR><BR>");
	end_frame();
	stdfoot();	
}

if ($action=="groups" && $do=="addnew") {
	
	stdhead(T_("GROUPS_MANAGEMENT"));

	navmenu();

	begin_frame(T_("GROUPS_ADD_NEW"));

	$group_id=intval($_POST["getlevel"]);

	$rlevel=mysql_query("SELECT * FROM groups WHERE group_id=$group_id") or die(mysql_error());
	$level=mysql_fetch_array($rlevel);
	if (!$level)
	   show_error_msg(T_("ERROR"),"Invalid ID",1);

	$update=array();
	$update[]="level='".mysql_escape_string($_POST["gname"])."'";
	$update[]="view_torrents='".$level["view_torrents"]."'";
	$update[]="edit_torrents='".$level["edit_torrents"]."'";
	$update[]="delete_torrents='".$level["delete_torrents"]."'";
	$update[]="view_users='".$level["view_users"]."'";
	$update[]="edit_users='".$level["edit_users"]."'";
	$update[]="delete_users='".$level["delete_users"]."'";
	$update[]="view_news='".$level["view_news"]."'";
	$update[]="edit_news='".$level["edit_news"]."'";
	$update[]="delete_news='".$level["delete_news"]."'";
	$update[]="view_forum='".$level["view_forum"]."'";
	$update[]="edit_forum='".$level["edit_forum"]."'";
	$update[]="delete_forum='".$level["delete_forum"]."'";
	$update[]="can_upload='".$level["can_upload"]."'";
	$update[]="can_download='".$level["can_download"]."'";
	$update[]="control_panel='".$level["control_panel"]."'";
	$strupdate=implode(",",$update);
	$group_id=intval($_GET["group_id"]);
	mysql_query("INSERT INTO groups SET $strupdate") or die(mysql_error());

	echo "<BR><center><b>Added OK</b></center><BR>";
	end_frame();
	stdfoot();	
}

#====================================#
#		Theme Management		#
#====================================#

if ($action == "style") {
	if ($do == "add") {
		stdhead();
		navmenu();
		if ($_POST) {
			if (empty($_POST['name']))
				$error .= T_("THEME_NAME_WAS_EMPTY")."<BR>";
			if (empty($_POST['uri']))
				$error .= T_("THEME_FOLDER_NAME_WAS_EMPTY");
			if ($error)
				show_error_msg(T_("ERROR"), T_("THEME_NOT_ADDED_REASON")." $error", 1);
			if (mysql_query("INSERT INTO stylesheets (name, uri) VALUES (".sqlesc($_POST[name]).", ".sqlesc($_POST[uri]).")"))
				show_error_msg("Success", "Theme '".htmlspecialchars($_POST[name])."' added.", 0);
			elseif (mysql_errno() == 1062)
				show_error_msg(T_("FAILED"), T_("THEME_ALREADY_EXISTS"), 0);
			else
				show_error_msg(T_("FAILED"), T_("THEME_NOT_ADDED_DB_ERROR")." ".mysql_error(), 0);
		}
		begin_frame(T_("THEME_ADD"), "center");
		?>
		<table align='center' width='80%' bgcolor='#cecece' cellspacing='2' cellpadding='2' style='border: 1px solid black'>
		<form action='admincp.php' method='post'>
		<input type='hidden' name='action' value='style'>
		<input type='hidden' name='do' value='add'>
		<tr>
		<td><?php echo T_("THEME_NAME_OF_NEW")?>:</td>
		<td align='right'><input type='text' name='name' size='30' maxlength='30' value='<?php echo $name?>'></td>
		</tr>
		<tr>
		<td><?php echo T_("THEME_FOLDER_NAME_CASE_SENSITIVE")?>:</td>
		<td align='right'><input type='text' name='uri' size='30' maxlength='30' value='<?php echo $uri?>'></td>
		</tr>
		<tr>
		<td colspan='2' align='center'>
		<input type='submit' value='Add new theme'>
		<input type='reset' value='Reset'>
		</td>
		</tr>
		</table>
		<br><?php echo T_("THEME_PLEASE_NOTE_ALL_THEMES_MUST")?>
		<?php
		end_frame();
		stdfoot();
	} elseif ($do == "del") {
		if (is_array($ids))
			$ids = implode(",", $ids);
		
		mysql_query("DELETE FROM stylesheets WHERE id IN ($ids)");
		header("Refresh: 1;url=admincp.php?action=style");
		stdhead();
		show_error_msg(T_("THEME_SUCCESS_THEME_DELETED"));
		stdfoot();
	}elseif ($do == "add2") {
		stdhead();

		$add = $_POST["add"];
		$a = 0;
		foreach ($add as $theme) {
			if ($theme['add'] != 1) { $a++; continue; }
			if (!mysql_query("INSERT INTO stylesheets (name, uri) VALUES(".sqlesc($theme['name']).", ".sqlesc($theme['uri']).")")) {
				if (mysql_errno() == 1062)
					$error .= htmlspecialchars($theme['name'])." - ".T_("THEME_ALREADY_EXISTS").".<BR>";
				else
					$error .= htmlspecialchars($theme['name']).": ".T_("THEME_DATEBASE_ERROR")." ".mysql_error()." (".mysql_errno().")<BR>";
			}else
				$added .= htmlspecialchars($theme['name'])."<BR>";
		}
		if ($a == count($add)) {
			header("Refresh: 3;url=admincp.php?action=style");
			show_error_msg(T_("ERROR"), T_("THEME_NOTHING_SELECTED"), 1);
		}

		header("Refresh: 3;url=admincp.php?action=style");
		if ($added)
			show_error_msg("Success", T_("THEME_THE_FOLLOWING_THEMES_WAS_ADDED"), 0);
		if ($error)
			show_error_msg(T_("FAILED"), T_("THEME_THE_FOLLOWING_THEMES_WAS_NOT_ADDED"), 0);
		stdfoot();
		
	}else{
		stdhead(T_("THEME_MANAGEMENT"));
		navmenu();
		begin_frame(T_("THEME_MANAGEMENT"), "center");
		$res = mysql_query("SELECT * FROM stylesheets");
		echo "<center><a href='admincp.php?action=style&do=add'>".T_("THEME_ADD")."</a><!-- - <b>".T_("THEME_CLICK_A_THEME_TO_EDIT")."</b>--></center><BR>";
		echo "".T_("THEME_CURRENT").":<form method='POST' action='admincp.php?action=style&do=del'><table width='60%' class=table_table align='center'>".
			"<tr><td class=table_head><b>ID</B></td><td class=table_head><b>".T_("NAME")."</B></td><td class=table_head><b>".T_("THEME_FOLDER_NAME")."</B></td><td width='5%' class=table_head>&nbsp;</td></tr>";
		while ($row=mysql_fetch_assoc($res)) {
			if (!is_dir("themes/$row[uri]"))
				$row['uri'] .= " <b>- ".T_("THEME_DIR_DONT_EXIST")."</B>";
			echo "<tr><td class=table_col1 align=center>$row[id]</td><td class=table_col2 align=center>$row[name]</td><td class=table_col1 align=center>$row[uri]</td><td class=table_col2 align=center><input name='ids[]' type='checkbox' value='$row[id]'></td></tr>";
		}
		mysql_free_result($res);
		echo "</table><p align='center'><input type='button' value='Check All' onclick='this.value=check(form)'>&nbsp;<input type='Submit' value='Delete Selected'></p></form>";
		
		echo "<p>".T_("THEME_IN_THEMES_BUT_NOT_IN_DB")."<form action='admincp.php?action=style&do=add2' method='POST'><table width='60%' class=table_table align='center'>".
			"<tr><td class=table_head align=center><b>".T_("NAME")."</B></td><td class=table_head align=center><b>".T_("THEME_FOLDER_NAME")."</B></td><td width='5%' class=table_head align=center>&nbsp;</td></tr>";
		$dh = opendir("themes/");
		$i=0;
		while (($file = readdir($dh)) !== false) {
			if ($file == "." || $file == ".." || !is_dir("themes/$file"))
				continue;
			if (is_file("themes/$file/header.php")) {
					$res = mysql_query("SELECT id FROM stylesheets WHERE uri = '$file' ");
					if (mysql_num_rows($res) == 0) {
						echo "<tr><td class=table_col1 align=center><input type='text' name='add[$i][name]' value='$file'></td><td class=table_col2 align=center>$file<input type='hidden' name='add[$i][uri]' value='$file'></td><td class=table_col1 align=center><input type='checkbox' name='add[$i][add]' value='1'></td></tr>";
						$i++;
					}
				}
		}
		if (!$i) echo "<tr><td class=table_col1 align=center colspan=3>".T_("THEME_NOTHING_TO_SHOW")."</td></tr>";
		echo "</table><p align='center'>".($i?"<input type='submit' value='Add Selected'>":"")."</p></form></p>";
		end_frame();
		stdfoot();
	}
}

/////////////////////// NEWS ///////////////////////
if ($action=="news" && $do=="view"){
	stdhead(T_("NEWS_MANAGEMENT"));
	navmenu();

	begin_frame("News");
	echo "<CENTER><a href=admincp.php?action=news&do=add><b>Add News Item</B></a></CENTER><br>";

	$res = mysql_query("SELECT * FROM news ORDER BY added DESC") or die(mysql_error());
	if (mysql_num_rows($res) > 0){
		
		while ($arr = mysql_fetch_array($res)) {
			$newsid = $arr["id"];
			$body = $arr["body"];
			$title = $arr["title"];
			$userid = $arr["userid"];
			$added = $arr["added"] . " GMT (" . (get_elapsed_time(sql_timestamp_to_unix_timestamp($arr["added"]))) . " ago)";

			$res2 = mysql_query("SELECT username FROM users WHERE id = $userid") or die(mysql_error());
			$arr2 = mysql_fetch_array($res2);
			
			$postername = $arr2["username"];
			
			if ($postername == "")
				$by = "Unknown";
			else
				$by = "<a href=account-details.php?id=$userid><b>$postername</b></a>";
			
			print("<table border=0 cellspacing=0 cellpadding=0><tr><td>");
			print("$added&nbsp;---&nbsp;by&nbsp$by");
			print(" - [<a href=?action=news&do=edit&newsid=$newsid><b>Edit</b></a>]");
			print(" - [<a href=?action=news&do=delete&newsid=$newsid><b>Delete</b></a>]");
			print("</td></tr>\n");

			print("<tr valign=top><td class=comment><b>$title</b><br>$body</td></tr></table><BR>\n");
		}

	}else{
	 echo "No News Posted";
	}

	end_frame();
	stdfoot();
}

if ($action=="news" && $do=="takeadd"){
	$body = $_POST["body"];
	
	if (!$body)
		show_error_msg(T_("ERROR"),"The news item cannot be empty!",1); 

	$title = $_POST['title'];

	if (!$title)
		show_error_msg(T_("ERROR"),"The news title cannot be empty!",1);
	
	$added = $_POST["added"];

	if (!$added)
		$added = sqlesc(get_date_time());

	mysql_query("INSERT INTO news (userid, added, body, title) VALUES (".

	$CURUSER['id'] . ", $added, " . sqlesc($body) . ", " . sqlesc($title) . ")") or die(mysql_error());

	if (mysql_affected_rows() == 1)
		show_error_msg(T_("COMPLETED"),"News item was added successfully.",1);
	else
		show_error_msg(T_("ERROR"),"Unable to add news",1);
}

if ($action=="news" && $do=="add"){
	stdhead(T_("NEWS_MANAGEMENT"));
	navmenu();

	begin_frame("Add News");
	print("<CENTER><form method=post action=admincp.php name=news>\n");
	print("<input type=hidden name=action value=news>\n");
	print("<input type=hidden name=do value=takeadd>\n");

	print("<center><b>News Title:</B> <input type=text name=title><br>\n");

	echo "<BR>".textbbcode("news","body")."<br>";

	print("<br><br><div align=center><input type=submit value='Submit' class=btn></div>\n");

	print("</form><br><br></CENTER>\n");
	end_frame();
	stdfoot();
}

if ($action=="news" && $do=="edit"){
	stdhead(T_("NEWS_MANAGEMENT"));
	navmenu();

	$newsid = (int)$_GET["newsid"];
	
	if (!is_valid_id($newsid))
		show_error_msg(T_("ERROR"),"Invalid news item ID.",1);

	$res = mysql_query("SELECT * FROM news WHERE id=$newsid") or die(mysql_error());

	if (mysql_num_rows($res) != 1)
		show_error_msg(T_("ERROR"), "No news item with ID $newsid.",1);

	$arr = mysql_fetch_array($res);

	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  		$body = $_POST['body'];

		if ($body == "")
    		show_error_msg(T_("ERROR"), T_("FORUMS_BODY_CANNOT_BE_EMPTY"),1);

		$title = $_POST['title'];

		if ($title == "")
			show_error_msg(T_("ERROR"), "Title cannot be empty!",1);

		$body = sqlesc($body);

		$editedat = sqlesc(get_date_time());

		mysql_query("UPDATE news SET body=$body, title='$title' WHERE id=$newsid") or die(mysql_error());

		$returnto = $_POST['returnto'];

		if ($returnto != "")
			header("Location: $returnto");
		else
			show_error_msg(T_("COMPLETED"),"News item was edited successfully.",0);
	} else {
		$returnto = htmlspecialchars($_GET['returnto']);
		begin_frame("Edit News");
		print("<form method=post action=?action=news&do=edit&newsid=$newsid name=news>\n");
		print("<CENTER>");
		print("<input type=hidden name=returnto value='$returnto'>\n");
		print("<b>News Title: </B><input type=text name=title value=\"".$arr['title']."\"><BR><BR>\n");
		echo "<BR>".textbbcode("news","body","".$arr["body"]."")."<br>";
		print("<BR><input type=submit value='Okay' class=btn>\n");
		print("</CENTER>\n");
		print("</form>\n");
	}
	end_frame();
	stdfoot();
}

if ($action=="news" && $do=="delete"){
	stdhead(T_("NEWS_MANAGEMENT"));
	navmenu();

	$newsid = (int)$_GET["newsid"];
	
	if (!is_valid_id($newsid))
		show_error_msg(T_("ERROR"),"Invalid news item ID",1);

	mysql_query("DELETE FROM news WHERE id=$newsid") or die(mysql_error());
	
	show_error_msg(T_("COMPLETED"),"News item was deleted successfully.",1);
}

///////////////// BLOCKS MANAGEMENT /////////////
if ($action=="blocks" && $do=="view") {
    stdhead(T_("_BLC_MAN_"));

    navmenu();

    begin_frame("View Blocks");

    $enabled = mysql_query("SELECT named, name, description, position, sort FROM blocks WHERE enabled=1 ORDER BY position, sort") or show_error_msg(T_("ERROR"),"Database Query failed: " . mysql_error());
    $disabled = mysql_query("SELECT named, name, description, position, sort FROM blocks WHERE enabled=0 ORDER BY position, sort") or show_error_msg(T_("ERROR"),"Database Query failed: " . mysql_error());
    
    print("<table align=\"center\" width=\"600\"><tr><td>");
    print("<table class=\"tablebg\" cellspacing=\"1\" align=\"center\" width=\"100%\">".
            "<tr>".
                "<td class=\"rowTabHead\" align=\"center\"><font size=\"2\"><b>Enabled Blocks</b></font></td>".
            "</tr>".
        "</table><br />".
        "<table class=\"tablebg\" cellspacing=\"1\" align=\"center\" width=\"100%\">".
            "<tr>".
                "<td class=\"rowTabHead\" align=\"center\">Name</td>".
                "<td class=\"rowTabHead\" align=\"center\">Description</td>".
                "<td class=\"rowTabHead\" align=\"center\">Position</td>".
                "<td class=\"rowTabHead\" align=\"center\">Sort<br />Order</td>".
                "<td class=\"rowTabHead\" align=\"center\">Preview</td>".
            "</tr>");
        while($blocks = mysql_fetch_assoc($enabled)){
        if(!$setclass){
            $class="row2";$setclass=true;}
        else{
            $class="row1";$setclass=false;}
    
            print("<tr>".
                        "<td class=$class valign=\"top\">".$blocks["named"]."</td>".
                        "<td class=$class>".$blocks["description"]."</td>".
                        "<td class=$class align=\"center\">".$blocks["position"]."</td>".
                        "<td class=$class align=\"center\">".$blocks["sort"]."</td>".
                        "<td class=$class align=\"center\">[<a href=\"blocks-edit.php?preview=true&name=".$blocks["name"]."#".$blocks["name"]."\" target=\"_blank\">preview</a>]</td>".
                    "</tr>");
        }
    print("<tr><td colspan=\"5\" class=\"rowTabHead\" align=\"center\"><form action=blocks-edit.php><input type=\"submit\" class=\"btn\" value=\"Edit\" /></td></tr>");
    print("</table></form>");
    print("</td></tr></table>");    
    
    print("<hr>");
    $setclass=false;
    print("<table align=\"center\" width=\"600\"><tr><td>");
    print("<table class=\"tablebg\" cellspacing=\"1\" align=\"center\" width=\"100%\">".
            "<tr>".
                "<td class=\"rowTabHead\" align=\"center\"><font size=\"2\"><b>Disabled Blocks</b></font></td>".
            "</tr>".
        "</table><br />".
        "<table class=\"tablebg\" cellspacing=\"1\" align=\"center\" width=\"100%\">".
            "<tr>".
                "<td class=\"rowTabHead\" align=\"center\">Name</td>".
                "<td class=\"rowTabHead\" align=\"center\">Description</td>".
                "<td class=\"rowTabHead\" align=\"center\">Position</td>".
                "<td class=\"rowTabHead\" align=\"center\">Sort<br />Order</td>".
                "<td class=\"rowTabHead\" align=\"center\">Preview</td>".
            "</tr>");
        while($blocks = mysql_fetch_assoc($disabled)){
        if(!$setclass){
            $class="row2";$setclass=true;}
        else{
            $class="row1";$setclass=false;}
    
            print("<tr>".
                        "<td class=$class valign=\"top\">".$blocks["named"]."</td>".
                        "<td class=$class>".$blocks["description"]."</td>".
                        "<td class=$class align=\"center\">".$blocks["position"]."</td>".
                        "<td class=$class align=\"center\">".$blocks["sort"]."</td>".
                        "<td class=$class align=\"center\">[<a href=\"blocks-edit.php?preview=true&name=".$blocks["name"]."#".$blocks["name"]."\" target=\"_blank\">preview</a>]</td>".
                    "</tr>");
        }
    print("<tr><td colspan=\"5\" class=\"rowTabHead\" align=\"center\" valign=\"bottom\"><form action=blocks-edit.php><input type=\"submit\" class=\"btn\" value=\"Edit\" /></td></tr>");
    print("</table></form>");
    print("</td></tr></table>");    
    end_frame();
    stdfoot();    
}


////////// categories /////////////////////
if ($action=="categories" && $do=="view"){
	stdhead(T_("Categories Management"));
	navmenu();

	begin_frame(T_("TORRENT_CATEGORIES"));
	echo "<CENTER><a href=admincp.php?action=categories&do=add><b>Add New Category</B></a></CENTER><br>";

	print("<i>Please note that if no image is specified, the category name will be displayed</i><br><br>");

	echo("<center><table width=95% class=table_table>");
	echo("<td width=10 class=table_head><b>Sort</B></td><td class=table_head><b>Parent Cat</B></td><td class=table_head><b>Sub Cat</B></td><td class=table_head><b>Image</B></td><td width=30 class=table_head></td>");
	$query = "SELECT * FROM categories ORDER BY parent_cat ASC, sort_index ASC";
	$sql = mysql_query($query);
	while ($row = mysql_fetch_array($sql)) {
		$id = $row['id'];
		$name = $row['name'];
		$priority = $row['sort_index'];
		$parent = $row['parent_cat'];

		print("<tr><td class=table_col1>$priority</td><td class=table_col2>$parent</td><td class=table_col1>$name</a></td><td class=table_col2 align=center>");
		if (isset($row["image"]) && $row["image"] != "")
			print("<img border=\"0\"src=\"" . $site_config['SITEURL'] . "/images/categories/" . $row["image"] . "\" alt=\"" . $row["name"] . "\" />");
		else
			print("-");	
		print("</td><td class=table_col1><a href=admincp.php?action=categories&do=edit&id=$id>[EDIT]</a> <a href=admincp.php?action=categories&do=delete&id=$id>[DELETE]</a></td></tr>");
	}
	echo("</table></center>");
	end_frame();
	stdfoot();
}


if ($action=="categories" && $do=="edit"){
	stdhead(T_("Categories Management"));
	navmenu();

	$id = (int)$_GET["id"];
	
	if (!is_valid_id($id))
		show_error_msg(T_("ERROR"),T_("INVALID_ID"),1);

	$res = mysql_query("SELECT * FROM categories WHERE id=$id") or die(mysql_error());

	if (mysql_num_rows($res) != 1)
		show_error_msg(T_("ERROR"), "No category with ID $id.",1);

	$arr = mysql_fetch_array($res);

	if ($_GET["save"] == '1'){
  		$parent_cat = $_POST['parent_cat'];
		if ($parent_cat == "")
    		show_error_msg(T_("ERROR"), "Parent Cat cannot be empty!",1);

		$name = $_POST['name'];
		if ($name == "")
			show_error_msg(T_("ERROR"), "Sub cat cannot be empty!",1);

		$sort_index = $_POST['sort_index'];
		$image = $_POST['image'];

		$parent_cat = sqlesc($parent_cat);
		$name = sqlesc($name);
		$sort_index = sqlesc($sort_index);
		$image = sqlesc($image);

		mysql_query("UPDATE categories SET parent_cat=$parent_cat, name=$name, sort_index=$sort_index, image=$image WHERE id=$id") or die(mysql_error());

		show_error_msg(T_("COMPLETED"),"category was edited successfully.",0);

	} else {
		begin_frame(T_("CATEGORY_EDIT"));
		print("<form method=post action=?action=categories&do=edit&id=$id&save=1>\n");
		print("<CENTER><table border=0 cellspacing=0 cellpadding=5>\n");
		print("<tr><td align=left><b>Parent Category: </B><input type=text name=parent_cat value=\"".$arr['parent_cat']."\"> All Subcats with EXACTLY the same parent cat are grouped</td></tr>\n");
		print("<tr><td align=left><b>Sub Category: </B><input type=text name=name value=\"".$arr['name']."\"></td></tr>\n");
		print("<tr><td align=left><b>Sort: </B><input type=text name=sort_index value=\"".$arr['sort_index']."\"></td></tr>\n");
		print("<tr><td align=left><b>Image: </B><input type=text name=image value=\"".$arr['image']."\"> single filename</td></tr>\n");
		print("<tr><td align=center><input type=submit value='Submit' class=btn></td></tr>\n");
		print("</table></CENTER>\n");
		print("</form>\n");
	}
	end_frame();
	stdfoot();
}

if ($action=="categories" && $do=="delete"){
	stdhead(T_("Categories Management"));
	navmenu();

	$id = (int)$_GET["id"];

	if ($_GET["sure"] == '1'){

		if (!is_valid_id($id))
			show_error_msg(T_("ERROR"),"Invalid news item ID",1);

		$newcatid = (int) $_POST["newcat"];

		mysql_query("UPDATE torrents SET category=$newcatid WHERE category=$id") or die(mysql_error()); //move torrents to a new cat

		mysql_query("DELETE FROM categories WHERE id=$id") or die(mysql_error()); //delete old cat
		
		show_error_msg(T_("COMPLETED"),"Category Deleted OK",1);

	}else{
		begin_frame(T_("CATEGORY_DEL"));
		print("<form method=post action=?action=categories&do=delete&id=$id&sure=1>\n");
		print("<CENTER><table border=0 cellspacing=0 cellpadding=5>\n");
		print("<tr><td align=left><b>Category ID to move all Torrents To: </B><input type=text name=newcat> (Cat ID)</td></tr>\n");
		print("<tr><td align=center><input type=submit value='Submit' class=btn></td></tr>\n");
		print("</table></CENTER>\n");
		print("</form>\n");
	}
	end_frame();
	stdfoot();
}

if ($action=="categories" && $do=="takeadd"){
  		$name = $_POST['name'];
		if ($name == "")
    		show_error_msg(T_("ERROR"), "Sub Cat cannot be empty!",1);

		$parent_cat = $_POST['parent_cat'];
		if ($parent_cat == "")
			show_error_msg(T_("ERROR"), "Parent Cat cannot be empty!",1);

		$sort_index = $_POST['sort_index'];
		$image = $_POST['image'];

		$parent_cat = sqlesc($parent_cat);
		$name = sqlesc($name);
		$sort_index = sqlesc($sort_index);
		$image = sqlesc($image);

	mysql_query("INSERT INTO categories (name, parent_cat, sort_index, image) VALUES ($name, $parent_cat, $sort_index, $image)") or die(mysql_error());

	if (mysql_affected_rows() == 1)
		show_error_msg(T_("COMPLETED"),"Category was added successfully.",1);
	else
		show_error_msg(T_("ERROR"),"Unable to add category",1);
}

if ($action=="categories" && $do=="add"){
	stdhead(T_("CATEGORY_MANAGEMENT"));
	navmenu();

	begin_frame(T_("CATEGORY_ADD"));
	print("<CENTER><form method=post action=admincp.php>\n");
	print("<input type=hidden name=action value=categories>\n");
	print("<input type=hidden name=do value=takeadd>\n");

	print("<table border=0 cellspacing=0 cellpadding=5>\n");

	print("<tr><td align=left><b>Parent Category:</B> <input type=text name=parent_cat></td></tr>\n");
	print("<tr><td align=left><b>Sub Category:</B> <input type=text name=name></td></tr>\n");
	print("<tr><td align=left><b>Sort:</B> <input type=text name=sort_index></td></tr>\n");
	print("<tr><td align=left><b>Image:</B> <input type=text name=image></td></tr>\n");

	print("<br><br><div align=center><input type=submit value='Submit' class=btn></div></td></tr>\n");

	print("</table></form><br><br></CENTER>\n");
	end_frame();
	stdfoot();
}


if ($action=="whoswhere"){
	stdhead("Where are members");
	navmenu();

	begin_frame("Last 100 page views");
	print("<CENTER><table class=table_table width=80%><tr><td class=table_head>User</td><td class=table_head>Page</td><td class=table_head>Accessed</td></tr>");
	$res = mysql_query("SELECT id, username, page, last_access FROM users ORDER BY last_access DESC LIMIT 100");
	while ($arr = mysql_fetch_assoc($res))
	print("<tr><td class=table_col1><a href=account-details.php?id=$arr[id]><b>$arr[username]</b></a></td><td class=table_col2>".htmlspecialchars($arr["page"])."</td><td  class=table_col1>$arr[last_access]</td></tr>");
	print("</table></CENTER>");
	end_frame();

	stdfoot();
}

if ($action=="peers"){
	stdhead("Peers List");
	navmenu();

	begin_frame("Peers List");

	$count1 = number_format(get_row_count("peers"));

	print("<center>We have $count1 peers</center><br>");

	$res4 = mysql_query("SELECT COUNT(*) FROM peers $limit") or die(mysql_error());
	$row4 = mysql_fetch_array($res4);

	$count = $row4[0];
	$peersperpage = 50;

	list($pagertop, $pagerbottom, $limit) = pager($peersperpage, $count, "admincp.php?action=peers&");

	print("$pagertop");

	$sql = "SELECT * FROM peers ORDER BY started DESC $limit";
	$result = mysql_query($sql);

	if( mysql_num_rows($result) != 0 ) {
		print'<CENTER><table width=100% border=1 cellspacing=0 cellpadding=3 class=table_table>';
		print'<tr>';
		print'<td class=table_head align=center>User</td>';
		print'<td class=table_head align=center>Torrent</td>';
		print'<td class=table_head align=center>IP</td>';
		print'<td class=table_head align=center>Port</td>';
		print'<td class=table_head align=center>Upl.</td>';
		print'<td class=table_head align=center>Downl.</td>';
		print'<td class=table_head align=center>Peer-ID</td>';
		print'<td class=table_head align=center>Conn.</td>';
		print'<td class=table_head align=center>Seeding</td>';
		print'<td class=table_head align=center>Started</td>';
		print'<td class=table_head align=center>Last<br>Action</td>';
		print'</tr>';

		while($row = mysql_fetch_assoc($result)) {
			if ($site_config['MEMBERSONLY']) {
				$sql1 = "SELECT id, username FROM users WHERE id = $row[userid]";
				$result1 = mysql_query($sql1);
				$row1 = mysql_fetch_assoc($result1);
			}

			if ($row1['username'])
				print'<tr><td class=table_col1><a href="account-details.php?id=' . $row['userid'] . '">' . $row1['username'] . '</a></td>';
			else
				print'<tr><td class=table_col1>'.$row[ip].'</td>';

			$sql2 = "SELECT id, name FROM torrents WHERE id = $row[torrent]";
			$result2 = mysql_query($sql2);

			while ($row2 = mysql_fetch_assoc($result2)) {

				$smallname =substr(htmlspecialchars($row2["name"]) , 0, 40);
					if ($smallname != htmlspecialchars($row2["name"])) {
						$smallname .= '...';
					}

				print'<td class=table_col1><a href="torrents-details.php?id=' . $row['torrent'] . '">' . $smallname . '</td>';
				print'<td align=center class=table_col1>' . $row['ip'] . '</td>';
				print'<td align=center class=table_col1>' . $row['port'] . '</td>';

				if ($row['uploaded'] < $row['downloaded'])
					print'<td align=center class=table_col1><font color=red>' . mksize($row['uploaded']) . '</font></td>';
				else
					if ($row['uploaded'] == '0')
						print'<td align=center class=table_col1>' . mksize($row['uploaded']) . '</td>';
					else
						print'<td align=center class=table_col1><font color=green>' . mksize($row['uploaded']) . '</font></td>';
				print'<td align=center class=table_col1>' . mksize($row['downloaded']) . '</td>';
				print'<td align=center class=table_col1>' . htmlspecialchars($row['peer_id']) . '</td>';
				if ($row['connectable'] == 'yes')
					print'<td align=center class=table_col1><font color=green>' . $row['connectable'] . '</font></td>';
				else
					print'<td align=center class=table_col1><font color=red>' . $row['connectable'] . '</font></td>';
				if ($row['seeder'] == 'yes')
					print'<td align=center class=table_col1><font color=green>' . $row['seeder'] . '</font></td>';
				else
					print'<td align=center class=table_col1><font color=red>' . $row['seeder'] . '</font></td>';
				print'<td align=center class=table_col1>' . $row['started'] . '</td>';
				print'<td align=center class=table_col1>' . $row['last_action'] . '</td>';
				print'</tr>';
			}
		}
		print'</table>';
		print("$pagerbottom</CENTER>");
	}else{
		print'<b><CENTER>No Peers</CENTER></B><BR>';
	}
	end_frame();

	stdfoot();
}


if ($action=="lastcomm"){
	stdhead("Latest Comments");
	navmenu();

	$res = mysql_query("SELECT COUNT(*) FROM comments WHERE torrent > '0'") or die(mysql_error());
	$arr = mysql_fetch_row($res);
	$count = $arr[0];

	list($pagertop, $pagerbottom, $limit) = pager(20, $count, "admincp.php?action=lastcomm&");

	begin_frame("Last Comments");

	echo $pagertop;

	$res = mysql_query("SELECT comments.id, comments.added, comments.user, comments.torrent, comments.text, torrents.name as tnome, users.username as unome FROM comments LEFT JOIN users ON users.id = comments.user LEFT JOIN torrents ON torrents.id = comments.torrent ORDER BY comments.id DESC $limit") or die(mysql_error());

	while ($arr = mysql_fetch_assoc($res)) {
		$userid = $arr["user"];
		$username = $arr["unome"];
		$data = $arr["added"];
		$tid = $arr["torrent"];
		$tnome = stripslashes($arr["tnome"]);
		$comentario = stripslashes(format_comment($arr["text"]));
		$cid = $arr["id"];
		echo "<table align=center cellpadding=1 cellspacing=0 style='border-collapse: collapse' bordercolor=#B5B5B5 width=100% border=1><tr><td class=ttable_col1 align=center>Torrent: <a href=\"torrents-details.php?id=$tid\">".$tnome."</a></td></tr><tr><td class=ttable_col2>".$comentario."</td></tr><tr><td class=ttable_col1 align=center>Posted in <b>".$data."</B> by <a href=\"account-details.php?id=".$userid."\">".$username."</a><!--  [ <a href=\"edit-comments.php?cid=".$cid."\">edit</a> | <a href=\"edit-comments.php?action=delete&cid=".$cid."\">delete</a> ] --></td></tr></table><br>";

	}
	echo $pagerbottom;
	end_frame();
	stdfoot();
}


if ($action=="messagespy"){
	if ($do == "del") {
		if ($_POST["delall"])
			mysql_query("DELETE FROM `messages`");
		else {
			if (!@count($_POST["del"])) show_error_msg(T_("ERROR"), "Nothing selected", 1);
			$ids = array_map("intval", $_POST["del"]);
			$ids = implode(", ", $ids);
			mysql_query("DELETE FROM `messages` WHERE `id` IN ($ids)") or sqlerr();
		}
		header("Refresh: 2;url=admincp.php?action=messagespy");
		stdhead();
		show_error_msg("Success", "Entries deleted", 0);
		stdfoot();
		die;
	}


	stdhead("Message Spy");
	navmenu();

	$res2 = mysql_query("SELECT COUNT(*) FROM messages");
	$row = mysql_fetch_array($res2);
	$count = $row[0];

	$perpage = 50;

	list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "admincp.php?action=messagespy&");

	begin_frame("Message Spy");

	echo $pagertop;
?>
	<script language="JavaScript" type="text/Javascript">
		function checkAll(box) {
			var x = document.getElementsByTagName('input');
			for(var i=0;i<x.length;i++) {
				if(x[i].type=='checkbox') {
					if (box.checked)
						x[i].checked = false;
					else
						x[i].checked = true;
				}
			}
			if (box.checked)
				box.checked = false;
			else
				box.checked = true;
		}
	</script>
<?php
	
	$res = mysql_query("SELECT * FROM messages WHERE location in ('in', 'both') ORDER BY id DESC $limit") or die(mysql_error());

	print("<CENTER><form method=post action='?action=messagespy&do=del'><table border=1 cellspacing=0 cellpadding=3 class=table_table>\n");

	print("<tr><td class=table_head align=left><input type='checkbox' id='checkAll' onclick='checkAll(this)'></td><td class=table_head align=left>Sender</td><td class=table_head align=left>Receiver</td><td class=table_head align=left>Text</td><td class=table_head align=left>Date</td></tr>\n");

	while ($arr = mysql_fetch_assoc($res)){
		$res2 = mysql_query("SELECT username FROM users WHERE id=" . $arr["receiver"]) or die(mysql_error());

		if ($arr2 = mysql_fetch_assoc($res2))
			$receiver = "<a href=account-details.php?id=" . $arr["receiver"] . "><b>" . $arr2["username"] . "</b></a>";
		else
			$receiver = "<i>Deleted</i>";

		$res3 = mysql_query("SELECT username FROM users WHERE id=" . $arr["sender"]) or die(mysql_error());
		$arr3 = mysql_fetch_assoc($res3);

		$sender = "<a href=account-details.php?id=" . $arr["sender"] . "><b>" . $arr3["username"] . "</b></a>";
		if( $arr["sender"] == 0 )
			$sender = "<font color=red><b>System</b></font>";
		$msg = format_comment($arr["msg"]);

		$added = utc_to_tz($arr["added"]);

		print("<tr><td class=table_col2><input type='checkbox' name='del[]' value='$arr[id]'><td align=left class=table_col1>$sender</td><td align=left class=table_col2>$receiver</td><td align=left class=table_col1>$msg</td><td align=left class=table_col2>$added</td></TR>");
	}

	print("</table></CENTER><BR>");
	echo "<input type='submit' value='Delete Checked'> <input type='submit' value='Delete All' name='delall'></form>";


	print($pagerbottom);

	end_frame();
	stdfoot();
}


if ($action=="torrentmanage"){
	stdhead(T_("TORRENT_MANAGEMENT"));
	navmenu();

	$search = trim($search);

	if ($search != '' ){
		$where = "WHERE name LIKE " . sqlesc("%$search%") . "";
	}

	
	$res2 = mysql_query("SELECT COUNT(*) FROM torrents $where");
	$row = mysql_fetch_array($res2);
	$count = $row[0];

	$perpage = 50;

	list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "admincp.php?action=torrentmanage&");

	begin_frame(T_("TORRENT_MANAGEMENT"));

	print("<CENTER><form method=get action=?>\n");
	print("<input type=hidden name=action value=torrentmanage>\n");
	print("".T_("SEARCH").": <input type=text size=30 name=search>\n");
	print("<input type=submit value='Search'>\n");
	print("</form></CENTER>\n");

	echo $pagertop;
	?>
	<CENTER><table align=center cellpadding="0" cellspacing="0" class="table_table" width="100%" border="1">
	<tr>
	<td class=table_head align=center>Name</td>
	<td class=table_head align=center>Visible</td>
	<td class=table_head align=center>Banned</td>
	<td class=table_head align=center>Seeders</td>
	<td class=table_head align=center>Leechers</td>
	<td class=table_head align=center>External?</td>
	<td class=table_head align=center>Edit?</td>
	</tr>
	<?php
	$rqq = "SELECT id, name, seeders, leechers, visible, banned, external FROM torrents $where ORDER BY name $limit";
	$resqq = mysql_query($rqq);

	while ($row = mysql_fetch_array($resqq)){
		extract ($row);

		$char1 = 35; //cut name length 
		$smallname = CutName(htmlspecialchars($row["name"]), $char1);

		echo "<tr><td class=table_col1><a href=\"torrents-details.php?id=$row[id]\">" . $smallname . "</a></td><td class=table_col2>$row[visible]</td><td class=table_col1>$row[banned]</td><td class=table_col2>$row[seeders]</td><td class=table_col1>$row[leechers]</td><td class=table_col2>$row[external]</td><td class=table_col1><a href=\"torrents-edit.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;id=" . $row["id"] . "\"><font size=1 face=Verdana>EDIT</a></td></tr>\n";
	}

	echo "</table></CENTER>\n";

	print($pagerbottom);

	end_frame();
	stdfoot();
}


if ($action == "users") {
	if ($do == "delete") {
		if (!@count($_POST['userids']))
			show_error_msg(T_("ERROR"), "Nothing selected.<BR><a href='admincp.php?action=users'>Click here</a> to go back.", 1);
		$userids = implode(", ",array_map("intval", $_POST['userids']));
		$r = mysql_query("SELECT id, username FROM users WHERE id IN ($userids)");
		while($rr=mysql_fetch_row($r))
			write_log("Account '$rr[1]' (ID: $rr[0]) was deleted by $CURUSER[username]");
		mysql_query("DELETE FROM users WHERE id IN ($userids)");
		$aff = mysql_affected_rows();
		header("Refresh: 3;url=admincp.php?action=users");
		show_error_msg("Users Deleted", "$aff user".($aff==1?'':'s')." deleted.<BR><a href='admincp.php?action=users'>Click here</a> to go back.", 1);
	}

	stdhead("Users Management");
	navmenu();

	$search = trim($search);

	if ($search != '' ){
		$where = "WHERE username LIKE " . sqlesc("%$search%") . " AND status='confirmed'";
	}

	
	$res2 = mysql_query("SELECT COUNT(*) FROM users $where");
	$row = mysql_fetch_array($res2);
	$count = $row[0];

	$perpage = 50;

	list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "admincp.php?action=users&");

	begin_frame("Users Management");

	print("<CENTER><form method=get action=?>\n");
	print("<input type=hidden name=action value=users>\n");
	print("".T_("SEARCH").": <input type=text size=30 name=search>\n");
	print("<input type=submit value='Search'>\n");
	print("</form></CENTER>\n");

	echo $pagertop;
	?>
	<CENTER><table align=center cellpadding="0" cellspacing="0" class="table_table" width="100%" border="1">
	<tr>
	<td class=table_head align=center>Username</td>
	<td class=table_head align=center>Class</td>
	<td class=table_head align=center><?php echo T_("EMAIL")?></td>
	<td class=table_head align=center>IP</td>
	<td class=table_head align=center>Added</td>
	<td class=table_head align=center>Last Visit</td>
	<td class=table_head align=center>Delete?</td>
	</tr>
	<?php
	
	$rqq = "SELECT * FROM users $where ORDER BY username $limit";
	$resqq = mysql_query($rqq);
	echo "<form action='admincp.php?action=users' method='POST'><input type='hidden' name='do' value='delete'>";
	while ($row = mysql_fetch_array($resqq)){
		echo "
		<tr><td class=table_col1 align=center><a href=account-details.php?id=$row[id]>$row[username]</a></td>
		<td class=table_col2 align=center>".get_user_class_name($row['class'])."</td>
		<td class=table_col1 align=center>$row[email]</td>
		<td class=table_col2 align=center>$row[ip]</td>
		<td class=table_col1 align=center>".utc_to_tz($row['added'])."</td>
		<td class=table_col2 align=center>$row[last_access]</td>
		<td class=table_col1 align=center><input type=checkbox name='userids[]' value='$row[id]'></td>
		</tr>\n";
	}

	echo "</table><BR><input type='button' value='Check All' onclick='this.value=check(form)'>&nbsp;<input type='submit' value='Delete checked'></form></CENTER>\n";

	print($pagerbottom);

	end_frame();
	stdfoot();
}


if ($action == "sitelog") {
	if ($do == "del") {
		if ($_POST["delall"])
			mysql_query("DELETE FROM `log`");
		else {
			if (!@count($_POST["del"])) show_error_msg(T_("ERROR"), "Nothing selected", 1);
			$ids = array_map("intval", $_POST["del"]);
			$ids = implode(", ", $ids);
			mysql_query("DELETE FROM `log` WHERE `id` IN ($ids)") or sqlerr();
		}
		header("Refresh: 2;url=admincp.php?action=sitelog");
		stdhead();
		show_error_msg("Success", "Entries deleted", 0);
		stdfoot();
		die;
	}

	stdhead("Site Log");
	navmenu();

	$search = trim($search);

	if ($search != '' ){
		$where = "WHERE txt LIKE " . sqlesc("%$search%") . "";
	}

	
	$res2 = mysql_query("SELECT COUNT(*) FROM log $where");
	$row = mysql_fetch_array($res2);
	$count = $row[0];

	$perpage = 50;

	list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "admincp.php?action=sitelog&");

	begin_frame("Site Log");

	print("<CENTER><form method=get action=?>\n");
	print("<input type=hidden name=action value=sitelog>\n");
	print("".T_("SEARCH").": <input type=text size=30 name=search>\n");
	print("<input type=submit value='Search'>\n");
	print("</form></CENTER>\n");

	echo $pagertop;
	?>
	<script language="JavaScript" type="text/Javascript">
		function checkAll(box) {
			var x = document.getElementsByTagName('input');
			for(var i=0;i<x.length;i++) {
				if(x[i].type=='checkbox') {
					if (box.checked)
						x[i].checked = false;
					else
						x[i].checked = true;
				}
			}
			if (box.checked)
				box.checked = false;
			else
				box.checked = true;
		}
	</script>

	<CENTER><table align=center cellpadding="0" cellspacing="0" class="table_table" width="100%" border="1">
	<tr>
	<td class=table_head align=left><input type='checkbox' id='checkAll' onclick='checkAll(this)'></td>
	<td class=table_head align=center>Date</td>
	<td class=table_head align=center>Time</td>
	<td class=table_head align=center>Event</td>
	</tr>
	<?php
	
	$rqq = "SELECT id, added, txt FROM log $where ORDER BY id DESC $limit";
	$res = mysql_query($rqq);

	echo "<form action='admincp.php?action=sitelog&do=del' method='POST'>";
	 while ($arr = MYSQL_FETCH_ARRAY($res)){
		$arr['added'] = utc_to_tz($arr['added']);
		$date = substr($arr['added'], 0, strpos($arr['added'], " "));
		$time = substr($arr['added'], strpos($arr['added'], " ") + 1);
		print("<tr><td class=table_col2><input type='checkbox' name='del[]' value='$arr[id]'></td></td><td class=table_col1>$date</td><td class=table_col2>$time</td><td class=table_col1 align=left>".stripslashes($arr[txt])."</td><!--<td class=table_col2><a href='staffcp.php?act=view_log&do=del_log&lid=$arr[id]' title='delete this entry'>delete</a></td>--></tr>\n");
	 }
	echo "</table></CENTER>\n";
	echo "<input type='submit' value='Delete Checked'> <input type='submit' value='Delete All' name='delall'></form>";

	print($pagerbottom);

	end_frame();
	stdfoot();
}

if ($action == "cheats") {
	stdhead("Possible Cheater Detection");
	navmenu();

	if ($daysago && $megabts){

		$timeago = 84600 * $daysago; //last 7 days
		$bytesover = 1048576 * $megabts; //over 500MB Upped

		$result = mysql_query("select * FROM users WHERE UNIX_TIMESTAMP('" . get_date_time() . "') - UNIX_TIMESTAMP(added) < '$timeago' AND status='confirmed' AND uploaded > '$bytesover' ORDER BY uploaded DESC "); 
		$num = mysql_num_rows($result); // how many uploaders

		begin_frame("Possible Cheater Detection");
		echo "<p>" . $num . " Users with found over last ".$daysago." days with more than ".$megabts." MB (".$bytesover.") Bytes Uploaded.</p>";

		$zerofix = $num - 1; // remove one row because mysql starts at zero

		if ($num > 0){
		echo "<table align=center class=table_table>";
		echo "<tr>";
		 echo "<td class=table_head>No.</td>";
		 echo "<td class=table_head>" .T_("USERNAME"). "</td>";
		 echo "<td class=table_head>" .T_("UPLOADED"). "</td>";
		 echo "<td class=table_head>" .T_("DOWNLOADED"). "</td>";
		 echo "<td class=table_head>" .T_("RATIO"). "</td>";
		 echo "<td class=table_head>" .T_("TORRENTS_POSTED"). "</td>";
		 echo "<td class=table_head>AVG Daily Upload</td>";
		 echo "<td class=table_head>" .T_("ACCOUNT_SEND_MSG"). "</td>";
		 echo "<td class=table_head>Joined</td>";
		echo "</tr>";

		for ($i = 0; $i <= $zerofix; $i++) {
			 $id = mysql_result($result, $i, "id");
			 $username = mysql_result($result, $i, "username");
			 $added = mysql_result($result, $i, "added");
			 $uploaded = mysql_result($result, $i, "uploaded");
			 $downloaded = mysql_result($result, $i, "downloaded");
			 $donated = mysql_result($result, $i, "donated");
			 $warned = mysql_result($result, $i, "warned");
			 $joindate = "" . get_elapsed_time(sql_timestamp_to_unix_timestamp($added)) . " ago";
			 $upperquery = "SELECT added FROM torrents WHERE owner = $id";
			 $upperresult = mysql_query($upperquery);
			 $seconds = mkprettytime(utc_to_tz_time() - utc_to_tz_time($added));
			 $days = explode("d ", $seconds);

			 if(sizeof($days) > 1) {
				 $dayUpload  = $uploaded / $days[0];
				 $dayDownload = $downloaded / $days[0];
			}
		 
		  $torrentinfo = mysql_fetch_array($upperresult);
		 
		  $numtorrents = mysql_num_rows($upperresult);
		   
		  if ($downloaded > 0){
		   $ratio = $uploaded / $downloaded;
		   $ratio = number_format($ratio, 3);
		   $color = get_ratio_color($ratio);
		   if ($color)
		   $ratio = "<font color=$color>$ratio</font>";
		   }
		  else
		   if ($uploaded > 0)
			$ratio = "Inf.";
		   else
			$ratio = "---";
		  
		 
		 $counter = $i + 1;
		 
		 echo "<tr>";
		  echo "<td align=center class=table_col1>$counter.</td>";
		  echo "<td class=table_col2><a href=account-details.php?id=$id>$username</a></td>";
		  echo "<td class=table_col1>" . mksize($uploaded). "</td>";
		  echo "<td class=table_col2>" . mksize($downloaded) . "</td>";
		  echo "<td class=table_col1>$ratio</td>";
		  if ($numtorrents == 0) echo "<td class=table_col2><font color=red>$numtorrents torrents</font></td>";
		  else echo "<td class=table_col2>$numtorrents torrents</td>";

		  echo "<td class=table_col1>" . mksize($dayUpload) . "</td>";

		  echo "<td align=center class=table_col2><a href=mailbox.php?compose&$id>PM</a></td>";
		  echo "<td class=table_col1>" . $joindate . "</td>";
		 echo "</tr>";

		 
		 }
		echo "</table><br><br>";
		end_frame();
		}

		if ($num == 0)
		{
		end_frame();
		}

	}else{
	begin_frame("Possible Cheater Detection");?>
	<CENTER><form action='admincp.php?action=cheats' method='post'>
		Number of days joined: <input type='text' size='4' maxlength='4' name='daysago'> Days<br /><br />
		MB Uploaded: <input type='text' size='6' maxlength='6' name='megabts'> MB<br />
		<input type='submit' value='   Submit   ' style='background:#eeeeee'>
		</form></CENTER><?php
	end_frame();
	}
	stdfoot();
}


if ($action=="emailbans"){
	stdhead(T_("EMAIL_BANS"));
	navmenu();

	$remove = $_GET['remove'];

	if (is_valid_id($remove)){
		mysql_query("DELETE FROM email_bans WHERE id=$remove") or die(mysql_error());
		write_log(T_("EMAIL_BANS_REM"));
	}

	if ($_GET["add"] == '1'){
		$mail_domain = trim($_POST["mail_domain"]);
		$comment = trim($_POST["comment"]);

		if (!$mail_domain || !$comment){
			show_error_msg(T_("ERROR"), T_("MISSING_FORM_DATA").".",0);
			stdfoot();
			die;
		}
		$mail_domain= sqlesc($mail_domain);
		$comment = sqlesc($comment);
		$added = sqlesc(get_date_time());

		mysql_query("INSERT INTO email_bans (added, addedby, mail_domain, comment) VALUES($added, $CURUSER[id], $mail_domain, $comment)") or die(mysql_error());

		write_log(T_("EMAIL_BANS_ADD"));
		show_error_msg(T_("COMPLETE"), T_("EMAIL_BAN")." Added",0);
		stdfoot();
		die;
	}

	begin_frame(T_("EMAILS_OR_DOMAINS_BANS"));
	print("You can block specific email addresses or domains from signing up to your tracker<BR><BR><BR><b>&nbsp;Add ".T_("EMAIL")."s OR Domains Ban</b>\n");
	print("<table border=0 cellspacing=0 cellpadding=5 align=center>\n");
	print("<form method=post action=admincp.php?action=emailbans&add=1>\n");
	print("<tr><td align=right>".T_("EMAIL_ADDRESS")." OR Domain To Ban</td><td><input type=text name=mail_domain size=40></td>\n");
	print("<tr><td align=right>Comment</td><td><input type=text name=comment size=40></td>\n");
	print("<tr><td colspan=2 align=center><input type=submit value='Add Ban'></td></tr>\n");
	print("</form>\n</table>\n<br>");
	//}

	$res2 = mysql_query("SELECT count(id) FROM email_bans") or die(mysql_error());
	$row = mysql_fetch_array($res2);
	$count = $row[0];
	$perpage = 40;list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, basename(__FILE__)."?action=emailbans&");
	print("<BR><b>&nbsp;Current ".T_("EMAIL")." Bans ($count)</b>\n");

	if ($count == 0){
		print("<p align=center><b>".T_("NOTHING_FOUND")."</b></p><br>\n");
	}else{
		echo $pagertop;
		print("<table border=0 cellspacing=0 cellpadding=5 width=90% align=center class=table_table>\n");
		print("<tr><td class=table_head>Added</td><td  class=table_head align=left>Mail Address Or Domain</td>"."<td class=table_head align=left>Banned By</td><td  class=table_head align=left>Comment</td><td class=table_head>Remove</td></tr>\n");
		$res = mysql_query("SELECT * FROM email_bans ORDER BY added DESC $limit") or die(mysql_error());

		while ($arr = mysql_fetch_assoc($res)){
			$r2 = mysql_query("SELECT username FROM users WHERE id=$arr[userid]") or die(mysql_error());
			$a2 = mysql_fetch_assoc($r2);

			$r4 = mysql_query("SELECT username,id FROM users WHERE id=$arr[addedby]") or die(mysql_error());
			$a4 = mysql_fetch_assoc($r4);
			print("<tr><td class=table_col1>".utc_to_tz($arr['added'])."</td><td align=left class=table_col2>$arr[mail_domain]</td><td align=left class=table_col1><a href=account-details.php?id=$a4[id]>$a4[username]"."</a></td><td align=left class=table_col2>$arr[comment]</td><td class=table_col1><a href=admincp.php?action=emailbans&remove=$arr[id]>Remove</a></td></tr>\n");
		}

		print("</table>\n");

		echo $pagerbottom;
		echo "<br>";
	}
	end_frame();
	stdfoot();
}

if ($action=="polls" && $do=="view"){
	stdhead(T_("POLLS_MANAGEMENT"));
	navmenu();
	begin_frame(T_("POLLS_MANAGEMENT"));

	echo "<CENTER><a href=admincp.php?action=polls&do=add>Add New Poll</a></CENTER>";
	echo "<CENTER><a href=admincp.php?action=polls&do=results>View Poll Results</a></CENTER>";

	echo "<BR><BR><b>Polls</b> (Top poll is current)<BR>";

	$query = mysql_query("SELECT id,question,added FROM polls ORDER BY added DESC") or die(mysql_error());

	while($row = MYSQL_FETCH_ARRAY($query)){
		echo "<a href=admincp.php?action=polls&do=add&subact=edit&pollid=$row[id]>".stripslashes($row["question"])."</a> - ".utc_to_tz($row['added'])." - <a href=admincp.php?action=polls&do=delete&id=$row[id]>Delete</a><BR>\n\n";
	}

	end_frame();

	stdfoot();
}


/////////////
if ($action=="polls" && $do=="results"){
	stdhead("Polls");
	navmenu();
	begin_frame("Results");
	echo "<table class=\"table_table\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\" width=\"95%\">";
	echo '<tr>';
	echo '<td class="table_head" align="left">Username</td>';
	echo '<td class="table_head" align="left">Question</td>';
	echo '<td class="table_head" align="left">Voted</td>';
	echo '</tr>';

	$poll = mysql_query("SELECT * FROM pollanswers ORDER BY pollid DESC");

	while ($res = mysql_fetch_assoc($poll)) {
		$user = mysql_fetch_assoc(mysql_query("SELECT username,id FROM users WHERE id = '".$res['userid']."'"));
		$option = "option".$res["selection"];
		if ($res["selection"] < 255) {
			$vote = mysql_fetch_assoc(mysql_query("SELECT ".$option." FROM polls WHERE id = '".$res['pollid']."'"));
		} else {
			$vote["option255"] = "Blank vote";
		}
		$sond = mysql_fetch_assoc(mysql_query("SELECT question FROM polls WHERE id = '".$res['pollid']."'"));
		
		echo '<tr>';
		echo '<td class="table_col1" align="left"><b>';
		echo '<a href=./account-details.php?id='.$user["id"].'>';
		echo '&nbsp;&nbsp;'.$user['username'];
		echo '</a>';
		echo '</b></td>';
		echo '<td class="table_col2" align="center">';
		echo '&nbsp;&nbsp;'.$sond['question'];
		echo '</td>';
		echo '<td class="table_col1" align="center">';
		echo $vote["$option"];
		echo '</td>';
		echo '</tr>';
	}

	echo '</table>';
	end_frame();
	stdfoot();
}


if ($action=="polls" && $do=="delete"){
	stdhead("Delete Poll");
	navmenu();

	$id = (int)$_GET["id"];
	
	if (!is_valid_id($id))
		show_error_msg(T_("ERROR"),"Invalid news item ID",1);

	mysql_query("DELETE FROM polls WHERE id=$id") or die(mysql_error());
	mysql_query("DELETE FROM pollanswers WHERE  pollid=$id") or die(mysql_error());
	
	show_error_msg(T_("COMPLETED"),"Poll and answers deleted",1);
}

if ($action=="polls" && $do=="add"){
	stdhead("Polls");
	navmenu();

	$pollid = (int)$_GET["pollid"];

	if ($subact == "edit"){
		$res = mysql_query("SELECT * FROM polls WHERE id = $pollid");
		$poll = mysql_fetch_array($res);
	}

	begin_frame("Polls");
	?>
	<table border=0 cellspacing=0 cellpadding=3>
	<form method=post action=admincp.php?action=polls&do=save>
	<tr><td class=rowhead>Question <font color=red>*</font></td><td align=left><input name=question size=60 maxlength=255 value="<?php echo $poll['question']?>"></td></tr>
	<tr><td class=rowhead>Option 1 <font color=red>*</font></td><td align=left><input name=option0 size=60 maxlength=40 value="<?php echo $poll['option0']?>"><br></td></tr>
	<tr><td class=rowhead>Option 2 <font color=red>*</font></td><td align=left><input name=option1 size=60 maxlength=40 value="<?php echo $poll['option1']?>"><br></td></tr>
	<tr><td class=rowhead>Option 3</td><td align=left><input name=option2 size=60 maxlength=40 value="<?php echo $poll['option2']?>"><br></td></tr>
	<tr><td class=rowhead>Option 4</td><td align=left><input name=option3 size=60 maxlength=40 value="<?php echo $poll['option3']?>"><br></td></tr>
	<tr><td class=rowhead>Option 5</td><td align=left><input name=option4 size=60 maxlength=40 value="<?php echo $poll['option4']?>"><br></td></tr>
	<tr><td class=rowhead>Option 6</td><td align=left><input name=option5 size=60 maxlength=40 value="<?php echo $poll['option5']?>"><br></td></tr>
	<tr><td class=rowhead>Option 7</td><td align=left><input name=option6 size=60 maxlength=40 value="<?php echo $poll['option6']?>"><br></td></tr>
	<tr><td class=rowhead>Option 8</td><td align=left><input name=option7 size=60 maxlength=40 value="<?php echo $poll['option7']?>"><br></td></tr>
	<tr><td class=rowhead>Option 9</td><td align=left><input name=option8 size=60 maxlength=40 value="<?php echo $poll['option8']?>"><br></td></tr>
	<tr><td class=rowhead>Option 10</td><td align=left><input name=option9 size=60 maxlength=40 value="<?php echo $poll['option9']?>"><br></td></tr>
	<tr><td class=rowhead>Option 11</td><td align=left><input name=option10 size=60 maxlength=40 value="<?php echo $poll['option10']?>"><br></td></tr>
	<tr><td class=rowhead>Option 12</td><td align=left><input name=option11 size=60 maxlength=40 value="<?php echo $poll['option11']?>"><br></td></tr>
	<tr><td class=rowhead>Option 13</td><td align=left><input name=option12 size=60 maxlength=40 value="<?php echo $poll['option12']?>"><br></td></tr>
	<tr><td class=rowhead>Option 14</td><td align=left><input name=option13 size=60 maxlength=40 value="<?php echo $poll['option13']?>"><br></td></tr>
	<tr><td class=rowhead>Option 15</td><td align=left><input name=option14 size=60 maxlength=40 value="<?php echo $poll['option14']?>"><br></td></tr>
	<tr><td class=rowhead>Option 16</td><td align=left><input name=option15 size=60 maxlength=40 value="<?php echo $poll['option15']?>"><br></td></tr>
	<tr><td class=rowhead>Option 17</td><td align=left><input name=option16 size=60 maxlength=40 value="<?php echo $poll['option16']?>"><br></td></tr>
	<tr><td class=rowhead>Option 18</td><td align=left><input name=option17 size=60 maxlength=40 value="<?php echo $poll['option17']?>"><br></td></tr>
	<tr><td class=rowhead>Option 19</td><td align=left><input name=option18 size=60 maxlength=40 value="<?php echo $poll['option18']?>"><br></td></tr>
	<tr><td class=rowhead>Option 20</td><td align=left><input name=option19 size=60 maxlength=40 value="<?php echo $poll['option19']?>"><br></td></tr>
	<tr><td class=rowhead>Sort</td><td>
	<input type=radio name=sort value=yes <?php echo $poll["sort"] != "no" ? " checked" : "" ?>>Yes
	<input type=radio name=sort value=no <?php echo $poll["sort"] == "no" ? " checked" : "" ?>> No
	</td></tr>
	<tr><td colspan=2 align=center><input type=submit value=<?php echo $pollid?"'Edit poll'":"'Create poll'"?> style='height: 20pt'></td></tr>
	</table>
	<p><font color=red>*</font> required</p>
	<input type=hidden name=pollid value=<?php echo $poll["id"]?>>
	<input type=hidden name=subact value=<?php echo $pollid?'edit':'create'?>>
	</form>
	<?php
	end_frame();
	stdfoot();
}

if ($action=="polls" && $do=="save"){

	$subact = $_POST["subact"];
	$pollid = (int)$_POST["pollid"];

	$question = $_POST["question"];
	$option0 = $_POST["option0"];
	$option1 = $_POST["option1"];
	$option2 = $_POST["option2"];
	$option3 = $_POST["option3"];
	$option4 = $_POST["option4"];
	$option5 = $_POST["option5"];
	$option6 = $_POST["option6"];
	$option7 = $_POST["option7"];
	$option8 = $_POST["option8"];
	$option9 = $_POST["option9"];
	$option10 = $_POST["option10"];
	$option11 = $_POST["option11"];
	$option12 = $_POST["option12"];
	$option13 = $_POST["option13"];
	$option14 = $_POST["option14"];
	$option15 = $_POST["option15"];
	$option16 = $_POST["option16"];
	$option17 = $_POST["option17"];
	$option18 = $_POST["option18"];
	$option19 = $_POST["option19"];
	$sort = (int)$_POST["sort"];

	if (!$question || !$option0 || !$option1)
		show_error_msg(T_("ERROR"), T_("MISSING_FORM_DATA")."!");

	if ($subact == "edit"){

		if (!is_valid_id($pollid))
			show_error_msg(T_("ERROR"),T_("INVALID_ID"),1);

		mysql_query("UPDATE polls SET " .
		"question = " . sqlesc($question) . ", " .
		"option0 = " . sqlesc($option0) . ", " .
		"option1 = " . sqlesc($option1) . ", " .
		"option2 = " . sqlesc($option2) . ", " .
		"option3 = " . sqlesc($option3) . ", " .
		"option4 = " . sqlesc($option4) . ", " .
		"option5 = " . sqlesc($option5) . ", " .
		"option6 = " . sqlesc($option6) . ", " .
		"option7 = " . sqlesc($option7) . ", " .
		"option8 = " . sqlesc($option8) . ", " .
		"option9 = " . sqlesc($option9) . ", " .
		"option10 = " . sqlesc($option10) . ", " .
		"option11 = " . sqlesc($option11) . ", " .
		"option12 = " . sqlesc($option12) . ", " .
		"option13 = " . sqlesc($option13) . ", " .
		"option14 = " . sqlesc($option14) . ", " .
		"option15 = " . sqlesc($option15) . ", " .
		"option16 = " . sqlesc($option16) . ", " .
		"option17 = " . sqlesc($option17) . ", " .
		"option18 = " . sqlesc($option18) . ", " .
		"option19 = " . sqlesc($option19) . ", " .
		"sort = " . sqlesc($sort) . " " .
    "WHERE id = $pollid");
	}else{
  	mysql_query("INSERT INTO polls VALUES(0" .
		", '" . get_date_time() . "'" .
    ", " . sqlesc($question) .
    ", " . sqlesc($option0) .
    ", " . sqlesc($option1) .
    ", " . sqlesc($option2) .
    ", " . sqlesc($option3) .
    ", " . sqlesc($option4) .
    ", " . sqlesc($option5) .
    ", " . sqlesc($option6) .
    ", " . sqlesc($option7) .
    ", " . sqlesc($option8) .
    ", " . sqlesc($option9) .
 		", " . sqlesc($option10) .
		", " . sqlesc($option11) .
		", " . sqlesc($option12) .
		", " . sqlesc($option13) .
		", " . sqlesc($option14) .
		", " . sqlesc($option15) .
		", " . sqlesc($option16) .
		", " . sqlesc($option17) .
		", " . sqlesc($option18) .
		", " . sqlesc($option19) . 
    ", " . sqlesc($sort) .
  	")");
	}

	stdhead();
	navmenu();
	show_error_msg("OK","Poll Updates ".T_("COMPLETE")."",0);
	stdfoot();
	die;
}

if ($action=="backups"){
	stdhead("Backups");
	navmenu();
	begin_frame("Backups");
	echo "<a href=backup-database.php>Backup Database</a> (or create a CRON task on ".$site_config["SITEURL"]."/backup-database.php)";
	end_frame();
	stdfoot();
	die;
}

if ($action=="forceclean"){
	$now = gmtime();
	mysql_query("UPDATE tasks SET last_time=$now WHERE task='cleanup'");
	require_once("backend/cleanup.php");
	do_cleanup();
	show_error_msg(T_("COMPLETE"),T_("FORCE_CLEAN_COMPLETED"),1);
	die;
}

if ($action=="torrentlangs" && $do=="view"){
	stdhead(T_("TORRENT_LANGUAGES"));
	navmenu();
	begin_frame(T_("TORRENT_LANGUAGES"));
	echo "<CENTER><a href=admincp.php?action=torrentlangs&do=add><b>Add New Language</B></a></CENTER><br>";

	print("<i>Please that language image is optional</i><br><br>");

	echo("<center><table width=95% class=table_table>");
	echo("<td width=10 class=table_head><b>Sort</B></td><td class=table_head><b>".T_("NAME")."</B></td><td class=table_head><b>Image</B></td><td width=30 class=table_head></td>");
	$query = "SELECT * FROM torrentlang ORDER BY sort_index ASC";
	$sql = mysql_query($query);
	while ($row = mysql_fetch_array($sql)) {
		$id = $row['id'];
		$name = $row['name'];
		$priority = $row['sort_index'];

		print("<tr><td class=table_col1>$priority</td><td class=table_col2>$name</td><td class=table_col1 align=center>");
		if (isset($row["image"]) && $row["image"] != "")
			print("<img border=\"0\"src=\"" . $site_config['SITEURL'] . "/images/languages/" . $row["image"] . "\" alt=\"" . $row["name"] . "\" />");
		else
			print("-");	
		print("</td><td class=table_col1><a href=admincp.php?action=torrentlangs&do=edit&id=$id>[EDIT]</a> <a href=admincp.php?action=torrentlangs&do=delete&id=$id>[DELETE]</a></td></tr>");
	}
	echo("</table></center>");
	end_frame();
	stdfoot();
	die;
}


if ($action=="torrentlangs" && $do=="edit"){
	stdhead(T_("TORRENT_LANG_MANAGEMENT"));
	navmenu();

	$id = (int)$_GET["id"];
	
	if (!is_valid_id($id))
		show_error_msg(T_("ERROR"),T_("INVALID_ID"),1);

	$res = mysql_query("SELECT * FROM torrentlang WHERE id=$id") or die(mysql_error());

	if (mysql_num_rows($res) != 1)
		show_error_msg(T_("ERROR"), "No Language with ID $id.",1);

	$arr = mysql_fetch_array($res);

	if ($_GET["save"] == '1'){
  	
		$name = $_POST['name'];
		if ($name == "")
			show_error_msg(T_("ERROR"), "Language cat cannot be empty!",1);

		$sort_index = $_POST['sort_index'];
		$image = $_POST['image'];

		$name = sqlesc($name);
		$sort_index = sqlesc($sort_index);
		$image = sqlesc($image);

		mysql_query("UPDATE torrentlang SET name=$name, sort_index=$sort_index, image=$image WHERE id=$id") or die(mysql_error());

		show_error_msg(T_("COMPLETED"),"Language was edited successfully.",0);

	} else {
		begin_frame("Edit Language");
		print("<form method=post action=?action=torrentlangs&do=edit&id=$id&save=1>\n");
		print("<CENTER><table border=0 cellspacing=0 cellpadding=5>\n");
		print("<tr><td align=left><b>Name: </B><input type=text name=name value=\"".$arr['name']."\"></td></tr>\n");
		print("<tr><td align=left><b>Sort: </B><input type=text name=sort_index value=\"".$arr['sort_index']."\"></td></tr>\n");
		print("<tr><td align=left><b>Image: </B><input type=text name=image value=\"".$arr['image']."\"> single filename</td></tr>\n");
		print("<tr><td align=center><input type=submit value='Submit' class=btn></td></tr>\n");
		print("</table></CENTER>\n");
		print("</form>\n");
	}
	end_frame();
	stdfoot();
}

if ($action=="torrentlangs" && $do=="delete"){
	stdhead(T_("TORRENT_LANG_MANAGEMENT"));
	navmenu();

	$id = (int)$_GET["id"];

	if ($_GET["sure"] == '1'){

		if (!is_valid_id($id))
			show_error_msg(T_("ERROR"),"Invalid Language item ID",1);

		$newcatid = $_POST["newcat"];

		mysql_query("UPDATE torrents SET torrentlang=$newlangid WHERE torrentlang=$id") or die(mysql_error()); //move torrents to a new cat

		mysql_query("DELETE FROM torrentlang WHERE id=$id") or die(mysql_error()); //delete old cat
		
		show_error_msg(T_("COMPLETED"),"Language Deleted OK",1);

	}else{
		begin_frame("Delete Language");
		print("<form method=post action=?action=torrentlangs&do=delete&id=$id&sure=1>\n");
		print("<CENTER><table border=0 cellspacing=0 cellpadding=5>\n");
		print("<tr><td align=left><b>Language ID to move all Languages To: </B><input type=text name=newlangid> (Lang ID)</td></tr>\n");
		print("<tr><td align=center><input type=submit value='Submit' class=btn></td></tr>\n");
		print("</table></CENTER>\n");
		print("</form>\n");
	}
	end_frame();
	stdfoot();
}

if ($action=="torrentlangs" && $do=="takeadd"){
  		$name = $_POST['name'];
		if ($name == "")
    		show_error_msg(T_("ERROR"), "Name cannot be empty!",1);

		$sort_index = $_POST['sort_index'];
		$image = $_POST['image'];

		$name = sqlesc($name);
		$sort_index = sqlesc($sort_index);
		$image = sqlesc($image);

	mysql_query("INSERT INTO torrentlang (name, sort_index, image) VALUES ($name, $sort_index, $image)") or die(mysql_error());

	if (mysql_affected_rows() == 1)
		show_error_msg(T_("COMPLETED"),"Language was added successfully.",1);
	else
		show_error_msg(T_("ERROR"),"Unable to add Language",1);
}

if ($action=="torrentlangs" && $do=="add"){
	stdhead(T_("TORRENT_LANG_MANAGEMENT"));
	navmenu();

	begin_frame("Add Language");
	print("<CENTER><form method=post action=admincp.php>\n");
	print("<input type=hidden name=action value=torrentlangs>\n");
	print("<input type=hidden name=do value=takeadd>\n");

	print("<table border=0 cellspacing=0 cellpadding=5>\n");

	print("<tr><td align=left><b>Name:</B> <input type=text name=name></td></tr>\n");
	print("<tr><td align=left><b>Sort:</B> <input type=text name=sort_index></td></tr>\n");
	print("<tr><td align=left><b>Image:</B> <input type=text name=image></td></tr>\n");

	print("<br><br><div align=center><input type=submit value='Submit' class=btn></div></td></tr>\n");

	print("</table></form><br><br></CENTER>\n");
	end_frame();
	stdfoot();
}

if ($action=="avatars"){
	stdhead("Avatar Log");
	navmenu();

	begin_frame("Avatar Log");

	$query = mysql_query("SELECT count(*) FROM users WHERE enabled='yes' AND avatar !=''");
	$count = mysql_fetch_row($query);
	$count = $count[0];

	list($pagertop, $pagerbottom, $limit) = pager(50, $count, 'admincp.php?action=avatars&');
	echo ($pagertop);
	?>
	<CENTER><TABLE class=table_table>
	<TR>
	<TD class=table_head><b><?php echo T_("USER")?></b></TD>
	<TD class=table_head><b><center>Avatar</center></b></TD>
	</TR><?php

	$query = "SELECT username, id, avatar FROM users WHERE enabled='yes' AND avatar !='' $limit";
	$res = mysql_query($query);

	while($arr = mysql_fetch_array($res)){
			echo("<TR><TD class=table_col1><b><A href=\"account-details.php?id=" . $arr['id'] . "\">" . $arr['username'] . "</b></A></TD><TD class=table_col2>");

			if (!$arr['avatar'])
				echo "<img width=\"80\" src=images/default_avatar.gif></td>";
			else
				echo "<img width=\"80\" src=\"".htmlspecialchars($arr["avatar"])."\"></td>";
	}
	?>
	</TABLE></CENTER>
	<?php
	echo ($pagerbottom);
	end_frame();
	stdfoot();
}

if ($action=="freetorrents"){
	stdhead("Free Leech ".T_("TORRENT_MANAGEMENT")."");
	navmenu();

	$search = trim($search);

	if ($search != '' ){
		$whereand = "AND name LIKE " . sqlesc("%$search%") . "";
	}

	
	$res2 = mysql_query("SELECT COUNT(*) FROM torrents WHERE freeleech='1' $whereand");
	$row = mysql_fetch_array($res2);
	$count = $row[0];

	$perpage = 50;

	list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "admincp.php?action=freetorrents&");

	begin_frame("Free Leech ".T_("TORRENT_MANAGEMENT")."");

	print("<CENTER><form method=get action=?>\n");
	print("<input type=hidden name=action value=torrentmanage>\n");
	print("".T_("SEARCH").": <input type=text size=30 name=search>\n");
	print("<input type=submit value='Search'>\n");
	print("</form></CENTER>\n");

	echo $pagertop;
	?>
	<CENTER><table align=center cellpadding="0" cellspacing="0" class="table_table" width="100%" border="1">
	<tr>
	<td class=table_head align=center>Name</td>
	<td class=table_head align=center>Visible</td>
	<td class=table_head align=center>Banned</td>
	<td class=table_head align=center>Seeders</td>
	<td class=table_head align=center>Leechers</td>
	<td class=table_head align=center>Edit?</td>
	</tr>
	<?php
	$rqq = "SELECT id, name, seeders, leechers, visible, banned FROM torrents WHERE freeleech='1' $whereand ORDER BY name $limit";
	$resqq = mysql_query($rqq);

	while ($row = mysql_fetch_array($resqq)){
		extract ($row);

		$char1 = 35; //cut name length 
		$smallname = CutName(htmlspecialchars($row["name"]), $char1);

		echo "<tr><td class=table_col1>" . $smallname . "</td><td class=table_col2>$row[visible]</td><td class=table_col1>$row[banned]</td><td class=table_col2>$row[seeders]</td><td class=table_col1>$row[leechers]</td><td class=table_col2><a href=\"torrents-edit.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;id=" . $row["id"] . "\"><font size=1 face=Verdana>EDIT</a></td></tr>\n";
	}

	echo "</table></CENTER>\n";

	print($pagerbottom);

	end_frame();
	stdfoot();
}

if ($action=="bannedtorrents"){
	stdhead("Banned Torrents");
	navmenu();

		
	$res2 = mysql_query("SELECT COUNT(*) FROM torrents WHERE banned='yes'");
	$row = mysql_fetch_array($res2);
	$count = $row[0];

	$perpage = 50;

	list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "admincp.php?action=bannedtorrents&");

	begin_frame("Banned ".T_("TORRENT_MANAGEMENT")."");

	print("<CENTER><form method=get action=?>\n");
	print("<input type=hidden name=action value=bannedtorrents>\n");
	print("".T_("SEARCH").": <input type=text size=30 name=search>\n");
	print("<input type=submit value='Search'>\n");
	print("</form></CENTER>\n");

	echo $pagertop;
	?>
	<CENTER><table align=center cellpadding="0" cellspacing="0" class="table_table" width="100%" border="1">
	<tr>
	<td class=table_head align=center>Name</td>
	<td class=table_head align=center>Visible</td>
	<td class=table_head align=center>Seeders</td>
	<td class=table_head align=center>Leechers</td>
	<td class=table_head align=center>External?</td>
	<td class=table_head align=center>Edit?</td>
	</tr>
	<?php
	$rqq = "SELECT id, name, seeders, leechers, visible, banned, external FROM torrents WHERE banned='yes' ORDER BY name";
	$resqq = mysql_query($rqq);

	while ($row = mysql_fetch_array($resqq)){
		extract ($row);

		$char1 = 35; //cut name length 
		$smallname = CutName(htmlspecialchars($row["name"]), $char1);

		echo "<tr><td class=table_col1>" . $smallname . "</td><td class=table_col2>$row[visible]</td><td class=table_col1>$row[seeders]</td><td class=table_col2>$row[leechers]</td><td class=table_col1>$row[external]</td><td class=table_col2><a href=\"torrents-edit.php?returnto=" . urlencode($_SERVER["REQUEST_URI"]) . "&amp;id=" . $row["id"] . "\"><font size=1 face=Verdana>EDIT</a></td></tr>\n";
	}

	echo "</table></CENTER>\n";

	print($pagerbottom);

	end_frame();
	stdfoot();
}


if ($action=="masspm"){
	stdhead("Mass Private Message");
	navmenu();


	//send pm
	if ($_GET["send"] == '1'){

		$sender_id = ($_POST['sender'] == 'system' ? 0 : $CURUSER['id']);

		$dt = sqlesc(get_date_time());
		$msg = $_POST['msg'];

		if (!$msg)
			show_error_msg(T_("ERROR"),"Please Enter Something!",1);

		$updateset = array_map("intval", $_POST['clases']);

		$query = mysql_query("SELECT id FROM users WHERE class IN (".implode(",", $updateset).")");
		while($dat=mysql_fetch_assoc($query)){
			mysql_query("INSERT INTO messages (sender, receiver, added, msg) VALUES ($sender_id, $dat[id], '" . get_date_time() . "', " . sqlesc($msg) .")");
		}

		write_log("A Mass PM was sent by ($CURUSER[username])");
		show_error_msg(T_("COMPLETE"), "Mass PM Sent",1);
		die;
	}

	begin_frame("Mass Private Message");
	print("<table border=0 cellspacing=0 cellpadding=5 align=center width=90%>\n");
	print("<form method=post action=admincp.php?action=masspm&send=1>\n");
	print("<b>Send to:</B><BR>\n");

	$query = "SELECT group_id, level FROM groups";
	$res = mysql_query($query);

	while ($row = mysql_fetch_array($res)){
		extract ($row);
	
		echo "<input type=checkbox name=clases[] value=$row[group_id]> $row[level]<BR>\n";
	}

	?>
	<BR><b>Message: </b><BR>
	<input type=hidden name=receiver value=<?php echo $receiver?>>
	<tr>
	<td><textarea name=msg cols=60 rows=10><?php echo $body?></textarea>
	<br>NOTE: Remember that BB can be used (NO HTML)</td>
	</tr>

	<tr>
	<td><b>Sender: </b>
	<?php echo $CURUSER['username']?> <input name="sender" type="radio" value="self" checked>
	System <input name="sender" type="radio" value="system"></td>
	</tr>

	<tr>
	<td><input type=submit value="Send" class=btn></td>
	</tr>
	</table></form>
	<?php
	end_frame();
	stdfoot();
}

if ($action=="rules" && $do=="view"){
	stdhead(T_("SITE_RULES_EDITOR"));
	navmenu();

	begin_frame(T_("SITE_RULES_EDITOR"));

	$res = mysql_query("SELECT * FROM rules ORDER BY id");

	print("<CENTER><a href=admincp.php?action=rules&do=addsect>Add New Rules Section</a></CENTER><BR>\n");	

	while ($arr=mysql_fetch_assoc($res)){
		begin_frame($arr[title]);
		print("<form method=post action=admincp.php?action=rules&do=edit><table width=95% border=1 class=table_table>");
		print("<tr><td width=100%>");
		print(format_comment($arr["text"]));
		print("</td></tr><tr><td><input type=hidden value=$arr[id] name=id><input type=submit value='Edit'></td></tr></table></form>");
		end_frame();
	}
	end_frame();
	stdfoot();
}

if ($action=="rules" && $do=="edit"){

	if ($_GET["save"]=="1"){
		$id = (int)$_POST["id"];
		$title = sqlesc($_POST["title"]);
		$text = sqlesc($_POST["text"]);
		$public = sqlesc($_POST["public"]);
		$class = sqlesc($_POST["class"]);
		mysql_query("update rules set title=$title, text=$text, public=$public, class=$class where id=$id");
		write_log("Rules have been changed by ($CURUSER[username])");
		show_error_msg(T_("COMPLETE"), "Rules edited ok<BR><BR><a href=admincp.php?action=rules&do=view>Back To Rules</a>",1);
		die;
	}


	stdhead(T_("SITE_RULES_EDITOR"));
	navmenu();
	
	begin_frame("Edit Rule Section");
	$id = (int)$_POST["id"];
	$res = @mysql_fetch_array(@mysql_query("select * from rules where id='$id'"));

	print("<form method=\"post\" action=\"admincp.php?action=rules&do=edit&save=1\">");
	print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\">\n");
	print("<tr><td>Section Title:</td><td><input style=\"width: 400px;\" type=\"text\" name=\"title\" value=\"$res[title]\" /></td></tr>\n");
	print("<tr><td style=\"vertical-align: top;\">Rules:</td><td><textarea cols=60 rows=15 name=\"text\">" . stripslashes($res["text"]) . "</textarea><br>NOTE: Remember that BB can be used (NO HTML)</td></tr>\n");

	print("<tr><td colspan=\"2\" align=\"center\"><input type=\"radio\" name='public' value=\"yes\" ".($res["public"]=="yes"?"checked":"").">For everybody<input type=\"radio\" name='public' value=\"no\" ".($res["public"]=="no"?"checked":"").">Members Only (Min User Class: <input type=\"text\" name='class' value=\"$res[class]\" size=1>)</td></tr>\n");
	print("<tr><td colspan=\"2\" align=\"center\"><input type=hidden value=$res[id] name=id><input type=\"submit\" value=\"Save\" style=\"width: 60px;\"></td></tr>\n");
	print("</table>");
	end_frame();
	stdfoot();
}

if ($action=="rules" && $do=="addsect"){

	if ($_GET["save"]=="1"){
		$title = sqlesc($_POST["title"]);
		$text = sqlesc($_POST["text"]);
		$public = sqlesc($_POST["public"]);
		$class = sqlesc($_POST["class"]);
		mysql_query("insert into rules (title, text, public, class) values($title, $text, $public, $class)");
		show_error_msg(T_("COMPLETE"), "New Section Added<BR><BR><a href=admincp.php?action=rules&do=view>Back To Rules</a>",1);
		die();
	}
	stdhead(T_("SITE_RULES_EDITOR"));
	navmenu();
	begin_frame(T_("ADD_NEW_RULES_SECTION"));
	print("<form method=\"post\" action=\"admincp.php?action=rules&do=addsect&save=1\">");
	print("<table border=\"0\" cellspacing=\"0\" cellpadding=\"10\" align=\"center\">\n");
	print("<tr><td>Section Title:</td><td><input style=\"width: 400px;\" type=\"text\" name=\"title\"/></td></tr>\n");
	print("<tr><td style=\"vertical-align: top;\">Rules:</td><td><textarea cols=60 rows=15 name=\"text\"></textarea><br>\n");
	print("<br>NOTE: Remember that BB can be used (NO HTML)</td></tr>\n");

	print("<tr><td colspan=\"2\" align=\"center\"><input type=\"radio\" name='public' value=\"yes\" checked>For everybody<input type=\"radio\" name='public' value=\"no\">&nbsp;Members Only - (Min User Class: <input type=\"text\" name='class' value=\"0\" size=1>)</td></tr>\n");
	print("<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"Add\" style=\"width: 60px;\"></td></tr>\n");
	print("</table></form>");
	end_frame();
	stdfoot();
}


if ($action=="reports" && $do=="view"){
	stdhead("Reported Items");
	navmenu();

	begin_frame("Reported Items");
/*	$type = $_GET["type"];
	if ($type == "user")
	$where = " WHERE type = 'user'";
	else if ($type == "torrent")
	$where = " WHERE type = 'torrent'";
	else if ($type == "forum")
	$where = " WHERE type = 'forum'";
	else if ($type == "comment")
	$where = " WHERE type = 'comment'";
	else
	$where = "";*/

	$res = mysql_query("SELECT count(id) FROM reports WHERE complete='0'") or die(mysql_error());
	$row = mysql_fetch_array($res);

	$count = $row[0];
	$perpage = 25;
	list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, basename(__FILE__) . "?type=" . $_GET["type"] . "&" );

	echo "<BR><CENTER><b><a href=#>View Archived Reports</a></B></CENTER><BR>";

	echo $pagertop;

	print("<table border=1 cellspacing=0 cellpadding=1 align=center width=95% class=table_table>\n");
	print("<tr><td class=table_head align=center>By</td><td class=table_head align=center>Reported</td><td class=table_head align=center>".T_("TYPE")."</td><td class=table_head align=center>Reason</td><td class=table_head align=center>Dealt With</td>");
	print("</tr>");
	$res = mysql_query("SELECT reports.id, reports.dealtwith,reports.dealtby, reports.addedby, reports.votedfor,reports.votedfor_xtra, reports.reason, reports.type, users.username, reports.complete FROM reports INNER JOIN users on reports.addedby = users.id WHERE complete = '0' ORDER BY id desc $limit");

	while ($arr = mysql_fetch_assoc($res))
	{
	if ($arr[dealtwith])
	{
	$res3 = mysql_query("SELECT username FROM users WHERE id=$arr[dealtby]");
	$arr3 = mysql_fetch_assoc($res3);
	$dealtwith = "<font color=green><b>Yes - <a href=account-details.php?id=$arr[dealtby]><b>$arr3[username]</b></a></b></font>";
	}
	else
	$dealtwith = "<font color=red><b>No</b></font>";
	if ($arr[type] == "user")
	{
	$type = "account-details";
	$res2 = mysql_query("SELECT username FROM users WHERE id=$arr[votedfor]");
	$arr2 = mysql_fetch_assoc($res2);
	$name = $arr2[username];
	}
	else if  ($arr[type] == "forum")
	{
	$type = "forums";
	$res2 = mysql_query("SELECT subject FROM forum_topics WHERE id=$arr[votedfor]");
	$arr2 = mysql_fetch_assoc($res2);
	$subject = $arr2[subject];
	}
	else if  ($arr[type] == "comment")
	{
	$type = "comment";
	$res2 = mysql_query("SELECT text FROM comments WHERE id=$arr[votedfor]");
	$arr2 = mysql_fetch_assoc($res2);
	$subject = format_comment($arr2[text]);
	}
	else if ($arr[type] == "torrent")
	{
	$type = "torrents-details";
	$res2 = mysql_query("SELECT name FROM torrents WHERE id=$arr[votedfor]");
	$arr2 = mysql_fetch_assoc($res2);
	$name = $arr2[name];
	if ($name == "")
	 $name = "<b>[Deleted]</b>";
	}

	if ($arr[type] == "forum")
	  { print("<tr><td class=table_col1><a href=account-details.php?id=$arr[addedby]><b>$arr[username]</b></a></td><td align=left class=table_col2><a href=$type.php?action=viewtopic&topicid=$arr[votedfor]&page=p#$arr[votedfor_xtra]><b>$subject</b></a></td><td align=left class=table_col1>$arr[type]</td><td align=left class=table_col2>".htmlspecialchars($arr[reason])."</td><td align=left class=table_col1>$dealtwith</td></tr>\n");
	  }
	else {
	print("<tr><td class=table_col1><a href=account-details.php?id=$arr[addedby]><b>$arr[username]</b></a></td><td align=left class=table_col2><a href=$type.php?id=$arr[votedfor]><b>$name</b></a></td><td align=left class=table_col1>$arr[type]</td><td align=left class=table_col2>".htmlspecialchars($arr[reason])."</td><td align=left class=table_col1>$dealtwith</td>\n");
	print("</tr>");
	}}

	print("</table>\n");



	echo $pagerbottom;

	end_frame();
	stdfoot();
}

if ($action == "warned") {
	stdhead("Warned Users Management");
	navmenu();

	
	$res2 = mysql_query("SELECT COUNT(*) FROM users WHERE warned='yes'");
	$row = mysql_fetch_array($res2);
	$count = $row[0];

	$perpage = 50;

	list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "admincp.php?action=warned&");

	begin_frame("Warned Users Management");

	echo $pagertop;
	?>
	<CENTER><table align=center cellpadding="0" cellspacing="0" class="table_table" width="100%" border="1">
	<tr>
	<td class=table_head align=center>Username</td>
	<td class=table_head align=center>Added</td>
	<td class=table_head align=center>Last Visit</td>
	<td class=table_head align=center>Uploaded</td>
	<td class=table_head align=center>Downloaded</td>
	<td class=table_head align=center>Edit?</td>
	</tr>
	<?php
	
	$rqq = "SELECT id, username, last_access, added, uploaded, downloaded FROM users WHERE warned='yes' ORDER BY username $limit";
	$resqq = mysql_query($rqq);

	while ($row = mysql_fetch_array($resqq)){
		extract ($row);

		echo "<tr><td class=table_col1><a href=account-details.php?id=$row[id]>$row[username]</a></td><td class=table_col2>".utc_to_tz($row['added'])."</td><td class=table_col1>$row[last_access]</td><td class=table_col2>".mksize($row["uploaded"])."</td><td class=table_col1>".mksize($row["downloaded"])."</td><td class=table_col2><a href=account-details.php?id=$row[id]>EDIT</a></td></tr>\n";
	}

	echo "</table></CENTER>\n";

	print($pagerbottom);

	end_frame();
	stdfoot();
}

#======================================================================#
#    Manual Conf Reg
#======================================================================#
if($action == "confirmreg")
{
stdhead("Manual Registration Confirm");
navmenu();
begin_frame("Info On This List", justify);
?>
<p align="justify">This page shows all users that have not clicked the ACTIVATION link in the signup email, they cannot access the site until they have clicked this link.  You should only manually confirm a user if they request it (via email, irc or other method), where they have lost or not received the email.  All PENDING users will be cleaned from the system every so often.</p>
<?php
end_frame();
begin_frame("Manual Registration Confirm", center);
begin_table();
$perpage = 100;
print("<tr><td align=\"center\"  class=alt3 align=left><font size=1 face=Verdana>Username</td><td align=\"center\"  class=alt3><font size=1 face=Verdana>".T_("EMAIL_ADDRESS")."</td><td align=\"center\"  class=alt3><font size=1 face=Verdana>Date Registered</td><td align=\"center\"  class=alt3 align=left><font size=1 face=Verdana>IP</td><td align=\"center\"  class=alt3><font size=1 face=Verdana>Status</td></tr>\n");

$resww = "SELECT * FROM users WHERE status='pending' ORDER BY username";
$reqww = mysql_query($resww);
while ($row = mysql_fetch_array($reqww))
    {
     extract ($row);
  echo "<tr><td align='center'>$row[username]</td><td align='center'>$row[email]</td><td align='center'>$row[added]</td><td align='center'>$row[ip]</td><td align='center'><a href='admincp.php?action=editreg&id=$row[id]'>$row[status]</a></td></tr>\n";

    }
end_table();
end_frame();
stdfoot();
}

if($action == "save_editreg")
// SAVE THEME EDIT FUNCTION
    {
        mysql_query("UPDATE users SET status='$ed_status' WHERE id=$id");
show_error_msg("Updated", "<br><br><center><b>Updated ".T_("COMPLETED")."</b><BR><BR><a href='admincp.php?action=confirmreg'>Click here</a> to go back.</center>");
}

if($action == "editreg" && $id != "")
// EDIT USER REG FORM
{
    $qq = MYSQL_QUERY("SELECT * FROM users WHERE id = $id");
    $ee = MYSQL_FETCH_ARRAY($qq);
    stdhead();
    navmenu();
    begin_frame();
    ?>

    <form action='admincp.php' method='post'>
    <input type='hidden' name='id' value='<?php echo $id?>'>
    <input type='hidden' name='action' value='save_editreg'>
    Name: <?php echo $ee[username]?><br />
    Surrent Status: <?php echo $ee[status]?><br>
    <select name='ed_status'>
        <option value='pending' <?php if($status == "pending") echo "selected"; ?>>pending
        <option value='confirmed' <?php if($status == "confirmed") echo "selected"; ?>>confirmed
        </select>
    <!--<input type='text' value='<?php echo $ee[status]?>' size='30' maxlength='30' name='ed_status'><br />-->
    <input type='submit' value='   Save   ' style='background:#eeeeee'>&nbsp;&nbsp;&nbsp;<input type='reset' value='  Reset  ' style='background:#eeeeee'>
    </form>
    <?php
        end_frame();
}

#======================================================================#
# Word Censor Filter
#======================================================================#
if($action == "censor") {
stdhead("Censor");
navmenu();
//Output
if ($_POST['submit'] == 'Add Censor'){
$query = "INSERT INTO censor (word, censor) VALUES (" . sqlesc($_POST['word']) . "," . sqlesc($_POST['censor']) . ");";
             mysql_query($query);
             }
if ($_POST['submit'] == 'Delete Censor'){
  $aquery = "DELETE FROM censor WHERE word = " . sqlesc($_POST['censor']) . " LIMIT 1";
  mysql_query($aquery);
  }

begin_frame("Edit Censored Words", center);  
/*------------------
|HTML form for Word Censor
------------------*/
?>
<div align="center">
<table width='100%' cellspacing='3' cellpadding='3'>
<form id="Add Censor" name="Add Censor" method="POST" action="admincp.php?action=censor">
<tr>
<td bgcolor='#eeeeee'><font face="Verdana" size="1">Word:  <input type="text" name="word" id="word" size="50" maxlength="255" value=""></font></td></tr>
<tr><td bgcolor='#eeeeee'><font face="Verdana" size="1">Censor With:  <input type="text" name="censor" id="censor" size="50" maxlength="255" value=""></font></td></tr>
<tr><td bgcolor='#eeeeee' align='left'>
<font size="1" face="Verdana"><input type="submit" name="submit" value="Add Censor"></font></td>
</tr>
</form>

<form id="Delete Censor" name="Delete Censor" method="POST" action="./admincp.php?action=censor">
<tr>
<td bgcolor='#eeeeee'><font face="Verdana" size="1">Remove Censor For: <select name="censor">
<?php
/*-------------
|Get the words currently censored
-------------*/
$select = "SELECT word FROM censor ORDER BY word";
$sres = mysql_query($select);
while ($srow = mysql_fetch_array($sres))
{
        echo "<option>" . $srow[0] . "</option>\n";
        }
echo'</select></font></td></tr><tr><td bgcolor="#eeeeee" align="left">
<font size="1" face="Verdana"><input type="submit" name="submit" value="Delete Censor"></font></td>
</tr></form></table><br>';
end_frame();
stdfoot();
}
// End forum Censored Words


// IP Bans (TorrentialStorm)
if ($action == "ipbans") {
    stdhead("Banned IPs");
    navmenu();

    if ($do == "del") {
        $delids = implode(", ", array_map("intval", $_POST["delids"]));
        $res = mysql_query("SELECT * FROM bans WHERE id IN ($delids)");
        while ($row = mysql_fetch_assoc($res)) {
            mysql_query("DELETE FROM bans WHERE id=$row[id]");
            // This still needs updating for IPv6
            write_log("IP Ban (".long2ip($row["first"])." - ".long2ip($row["last"]).") was removed by $CURUSER[id] ($CURUSER[username])");
        }
        show_error_msg("Success", "Ban(s) deleted.", 0);
    }

    if ($do == "add") {
        $first = trim($_POST["first"]);
        $last = trim($_POST["last"]);
        $comment = trim($_POST["comment"]);
        if ($first == "" || $last == "" || $comment == "")
            show_error_msg(T_("ERROR"), T_("MISSING_FORM_DATA").". Go back and try again", 1);

	if (!validip($first) || !validip($last))
            show_error_msg(T_("ERROR"), "Bad IP address.");
        $comment = sqlesc($comment);
        $added = sqlesc(get_date_time());
        mysql_query("INSERT INTO bans (added, addedby, first, last, comment) VALUES($added, $CURUSER[id], '$first', '$last', $comment)");
        switch (mysql_errno()) {
            case 1062:
                show_error_msg(T_("ERROR"), "Duplicate ban.", 0);
            break;
            case 0:
                show_error_msg("Success", "Ban added.", 0);
            break;
            default:
                show_error_msg(T_("ERROR"), "".T_("THEME_DATEBASE_ERROR")." ".htmlspecialchars(mysql_error()), 0);
        }
    }

    begin_frame("Banned IPs", "center");
    echo "<p align=\"justify\">This page allows you to prevent individual users or groups of users from accessing your tracker by placing a block on their IP or IP range.<BR>
    If you wish to temporarily disable an account, but still wish a user to be able to view your tracker, you can use the 'Disable Account' option which is found in the user's profile page.</p><BR>";

    $count = get_row_count("bans");
    if ($count == 0)
    print("<b>No Bans Found</b><br />\n");
    else {
        list($pagertop, $pagerbottom, $limit) = pager(50, $count, "admincp.php?action=ipbans&"); // 50 per page
        echo $pagertop;

        echo "<form action='admincp.php?action=ipbans&do=del' method='POST'><table border=1 cellspacing=0 cellpadding=5 align=center class=ttable_headinner>
        <tr>
            <td class=ttable_head>".T_("DATE_ADDED")."</td>
            <td class=table_head align=left>First IP</td>
            <td class=ttable_head align=left>Last IP</td>
            <td class=ttable_head align=left>".T_("ADDED_BY")."</td>
            <td class=ttable_head align=left>Comment</td>
            <td class=ttable_head>Del?</td>
        </tr>";

        $res = mysql_query("SELECT bans.*, users.username FROM bans LEFT JOIN users ON bans.addedby=users.id ORDER BY added $limit");
        while ($arr = mysql_fetch_assoc($res)) {
            echo "<tr>
                <td align=center class=ttable_col1>".date('d/m/Y<\B\R>H:i:s', utc_to_tz_time($arr["added"]))."</td>
                <td align=center class=ttable_col2>$arr[first]</td>
                <td align=center class=ttable_col1>$arr[last]</td>
                <td align=center class=ttable_col2><a href='account-details.php?id=$arr[addedby]'>$arr[username]</a></td>
                <td align=center class=ttable_col1>$arr[comment]</td>
                <td align=center class=ttable_col2><input type='checkbox' name='delids[]' value='$arr[id]'></td>
            </tr>";
        }
        echo "</table><BR><input type='submit' value='Delete Checked'>&nbsp;<input type='button' onclick='this.value=check(form)' value='Check All'></form>";
        echo $pagerbottom;
    }

    echo "<BR><BR>";
    begin_frame("Add Ban", "center");
    print("<table border=1 cellspacing=0 cellpadding=5>\n");
    print("<form method=post action=admincp.php?action=ipbans&do=add>\n");
    print("<tr><td class=rowhead>First IP</td><td><input type=text name=first size=40></td>\n");
    print("<tr><td class=rowhead>Last IP</td><td><input type=text name=last size=40></td>\n");
    print("<tr><td class=rowhead>Comment</td><td><input type=text name=comment size=40></td>\n");
    print("<tr><td colspan=2><input type=submit value='Okay' class=btn></td></tr>\n");
    print("</form>\n</table>\n");
    end_frame();

    end_frame();
    stdfoot();
}
// End IP Bans (TorrentialStorm)



// Advanced User Search (Ported from v1 - TorrentialStorm)
if ($action == "usersearch") {
	if ($do == "warndisable") {
		if (empty($_POST["warndisable"]))
			show_error_msg(T_("ERROR"), "You must select a user to edit.", 1);

		if (!empty($_POST["warndisable"])){
			$enable = $_POST["enable"];
			$disable = $_POST["disable"];
			$unwarn = $_POST["unwarn"];
			$warnlength = 0 + $_POST["warnlength"];
			$warnpm = $_POST["warnpm"];
			$_POST['warndisable'] = array_map("intval", $_POST['warndisable']);
			$userid = implode(", ", $_POST['warndisable']);

			if ($disable != '') {
				mysql_query("UPDATE users SET enabled='no' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")");
			}

			if ($enable != '') {
				mysql_query("UPDATE users SET enabled='yes' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")");
			}

			if ($unwarn != '') {
				$msg = "Your Warning Has Been Removed";
				foreach ($_POST["warndisable"] as $userid) {
					mysql_query("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES ('0', '0', '".$userid."', '" . get_date_time() . "', " . sqlesc($msg) . ")") or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n".T_("ERROR").": (" . mysql_errno() . ") " . mysql_error());
				}

				$r = mysql_query("SELECT modcomment FROM users WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")")or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n".T_("ERROR").": (" . mysql_errno() . ") " . mysql_error());
				$user = mysql_fetch_array($r);
				$exmodcomment = $user["modcomment"];
				$modcomment = gmdate("Y-m-d") . " - Warning Removed By " . $CURUSER['username'] . ".\n". $modcomment . $exmodcomment;
				mysql_query("UPDATE users SET modcomment=" . sqlesc($modcomment) . " WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")") or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n".T_("ERROR").": (" . mysql_errno() . ") " . mysql_error());

				mysql_query("UPDATE users SET warned='no' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")");
			}

			if ($warn != '') {
				if (empty($_POST["warnpm"]))
					show_error_msg(T_("ERROR"), "You must type a reason/mod comment.", 1);

					$msg = "You have received a warning, Reason: $warnpm";

					$r = mysql_query("SELECT modcomment FROM users WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")")or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n".T_("ERROR").": (" . mysql_errno() . ") " . mysql_error());
					$user = mysql_fetch_array($r);
					$exmodcomment = $user["modcomment"];
					$modcomment = gmdate("Y-m-d") . " - Warned by " . $CURUSER['username'] . ".\nReason: $warnpm\n" . $modcomment . $exmodcomment;
					mysql_query("UPDATE users SET modcomment=" . sqlesc($modcomment) . " WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")") or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n".T_("ERROR").": (" . mysql_errno() . ") " . mysql_error());

					mysql_query("UPDATE users SET warned='yes' WHERE id IN (" . implode(", ", $_POST['warndisable']) . ")");
					foreach ($_POST["warndisable"] as $userid) {
						mysql_query("INSERT INTO messages (poster, sender, receiver, added, msg) VALUES ('0', '0', '".$userid."', '" . get_date_time() . "', " . sqlesc($msg) . ")") or die("<b>A fatal MySQL error occured</b>.\n<br />Query: " . $query . "<br />\n".T_("ERROR").": (" . mysql_errno() . ") " . mysql_error());
					}
			}

		}

		header("Location: $_POST[referer]");
		die;
	}
	stdhead("Advanced User Search");
	navmenu();
	begin_frame("Search");

	if ($_GET['h']) {
		echo "<table width=65% border=0 align=center><tr><td class=embedded bgcolor='#F5F4EA'><div align=left>\n
			Fields left blank will be ignored;\n
			Wildcards * and ? may be used in Name, ".T_("EMAIL")." and Comments, as well as multiple values\n
			separated by spaces (e.g. 'wyz Max*' in Name will list both users named\n
			'wyz' and those whose names start by 'Max'. Similarly '~' can be used for\n
			negation, e.g. '~alfiest' in comments will restrict the search to users\n
			that do not have 'alfiest' in their comments).<br><br>\n
			The Ratio field accepts 'Inf' and '---' besides the usual numeric values.<br><br>\n
			The subnet mask may be entered either in dotted decimal or CIDR notation\n
			(e.g. 255.255.255.0 is the same as /24).<br><br>\n
			Uploaded and Downloaded should be entered in GB.<br><br>\n
			For search parameters with multiple text fields the second will be\n
			ignored unless relevant for the type of search chosen. <br><br>\n
			The History column lists the number of forum posts and comments,\n
			respectively, as well as linking to the history page.\n
			</div></td></tr></table><br><br>\n";
	} else {
		echo "<p align=center>[<a href='admincp.php?action=usersearch&amp;h=1'>Instructions</a>]";
		echo "&nbsp;-&nbsp;[<a href='admincp.php?action=usersearch'>Reset</a>]</p>\n";
	}

	$highlight = " bgcolor=#BBAF9B";

?>
	<center>
	<form method=get action="admincp.php">
	<input type="hidden" name="action" value="usersearch"/>
	<table border="1" style="border-collapse: collapse" bordercolor="#646262" cellspacing="0" cellpadding="2">
	<tr>

	<td valign="middle" class=rowhead>Name:</td>
	<td<?php echo $_GET['n']?$highlight:""?>><input name="n" type="text" value="<?php echo $_GET['n']?>" size=35></td>

	<td valign="middle" class=rowhead>Ratio:</td>
	<td<?php echo $_GET['r']?$highlight:""?>><select name="rt">
	<?php
	$options = array("equal","above","below","between");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value=$i ".(($_GET['rt']=="$i")?"selected":"").">".$options[$i]."</option>\n";
	}
	?>
	</select>
	<input name="r" type="text" value="<?php echo $_GET['r']?>" size="5" maxlength="4">
	<input name="r2" type="text" value="<?php echo $_GET['r2']?>" size="5" maxlength="4"></td>

	<td valign="middle" class=rowhead>Member status:</td>
	<td<?php echo $_GET['st']?$highlight:""?>><select name="st">
	<?php
	$options = array("(any)","confirmed","pending");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value=$i ".(($_GET['st']=="$i")?"selected":"").">".$options[$i]."</option>\n";
	}
	?>
	</select></td></tr>
	<tr><td valign="middle" class=rowhead><?php echo T_("EMAIL")?>:</td>
	<td<?php echo $_GET['em']?$highlight:""?>><input name="em" type="text" value="<?php echo $_GET['em']?>" size="35"></td>
	<td valign="middle" class=rowhead>IP:</td>
	<td<?php echo $_GET['ip']?$highlight:""?>><input name="ip" type="text" value="<?php echo $_GET['ip']?>" maxlength="17"></td>

	<td valign="middle" class=rowhead>Account status:</td>
	<td<?php echo $_GET['as']?$highlight:""?>><select name="as">
	<?php
	$options = array("(any)", "enabled", "disabled");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value=$i ".(($_GET['as']=="$i")?"selected":"").">".$options[$i]."</option>\n";
	}
	?>
	</select></td></tr>
	<tr>
	<td valign="middle" class=rowhead>Comment:</td>
	<td<?php echo $_GET['co']?$highlight:""?>><input name="co" type="text" value="<?php echo $_GET['co']?>" size="35"></td>
	<td valign="middle" class=rowhead>Mask:</td>
	<td<?php echo $_GET['ma']?$highlight:""?>><input name="ma" type="text" value="<?php echo $_GET['ma']?>" maxlength="17"></td>
	<td valign="middle" class=rowhead>Class:</td>
	<td<?php echo ($_GET['c'] && $_GET['c'] != 1)?$highlight:""?>><select name="c"><option value='1'>(any)</option>
	<?php
	$class = $_GET['c'];
	if (!is_valid_id($class)) {
		$class = '';
	}
	$groups = classlist();
	foreach ($groups as $group) {
		$id = $group["group_id"] + 2;
		echo "<option value='$id'".($class == $id ? " selected" : "").">".htmlspecialchars($group["level"])."</option>\n";
	}
	?>
	</select></td></tr>
	<tr>

	<td valign="middle" class=rowhead>Joined:</td>

	<td<?php echo $_GET['d']?$highlight:""?>><select name="dt">
	<?php
	$options = array("on","before","after","between");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value=$i ".(($_GET['dt']=="$i")?"selected":"").">".$options[$i]."</option>\n";
	}
	?>
	</select>

	<input name="d" type="text" value="<?php echo $_GET['d']?>" size="12" maxlength="10">

	<input name="d2" type="text" value="<?php echo $_GET['d2']?>" size="12" maxlength="10"></td>


	<td valign="middle" class=rowhead>Uploaded (GB):</td>

	<td<?php echo $_GET['ul']?$highlight:""?>><select name="ult" id="ult">
	<?php
	$options = array("equal","above","below","between");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value=$i ".(($_GET['ult']=="$i")?"selected":"").">".$options[$i]."</option>\n";
	}
	?>
	</select>

	<input name="ul" type="text" id="ul" size="8" maxlength="7" value="<?php echo $_GET['ul']?>">

	<input name="ul2" type="text" id="ul2" size="8" maxlength="7" value="<?php echo $_GET['ul2']?>"></td>
	<td valign="middle" class="rowhead">&nbsp;</td>

	<td>&nbsp;</td></tr>
	<tr>

	<td valign="middle" class=rowhead>Last Seen:</td>

	<td <?php echo $_GET['ls']?$highlight:""?>><select name="lst">
	<?php
	$options = array("on","before","after","between");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value=$i ".(($_GET['lst']=="$i")?"selected":"").">".$options[$i]."</option>\n";
	}
	?>
	</select>

	<input name="ls" type="text" value="<?php echo $_GET['ls']?>" size="12" maxlength="10">

	<input name="ls2" type="text" value="<?php echo $_GET['ls2']?>" size="12" maxlength="10"></td>
	<td valign="middle" class=rowhead>Downloaded (GB):</td>

	<td<?php echo $_GET['dl']?$highlight:""?>><select name="dlt" id="dlt">
	<?php
	$options = array("equal","above","below","between");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value=$i ".(($_GET['dlt']=="$i")?"selected":"").">".$options[$i]."</option>\n";
	}
	?>
	</select>

	<input name="dl" type="text" id="dl" size="8" maxlength="7" value="<?php echo $_GET['dl']?>">

	<input name="dl2" type="text" id="dl2" size="8" maxlength="7" value="<?php echo $_GET['dl2']?>"></td>

	<td valign="middle" class=rowhead>Warned:</td>

	<td<?php echo $_GET['w']?$highlight:""?>><select name="w">
	<?php
	$options = array("(any)","Yes","No");
	for ($i = 0; $i < count($options); $i++){
	echo "<option value=$i ".(($_GET['w']=="$i")?"selected":"").">".$options[$i]."</option>\n";
	}
	?>
	</select></td></tr>
	<tr><td colspan="6" align=center><input name="submit" value="Search" type=submit class=btn></td></tr>
	</table>
	<br><br>
	</form>

<?php

	// Validates date in the form [yy]yy-mm-dd;
	// Returns date if valid, 0 otherwise.
	function mkdate($date) {
		if (strpos($date, '-'))
			$a = explode('-', $date);
		elseif (strpos($date, '/'))
			$a = explode('/', $date);
		else
			return 0;
		for ($i = 0; $i < 3; $i++) {
			if (!is_numeric($a[$i]))
				return 0;
		}
		if (checkdate($a[1], $a[2], $a[0]))
			return date ("Y-m-d", mktime (0,0,0,$a[1],$a[2],$a[0]));
		else
			return 0;
	}

	// ratio as a string
	function ratios ($up, $down, $color = true) {
		if ($down > 0) {
			$r = number_format($up / $down, 2);
			if ($color)
				$r = "<font color=".get_ratio_color($r).">$r</font>";
		} elseif ($up > 0)
			$r = "Inf.";
		else
			$r = "---";
		return $r;
	}

	// checks for the usual wildcards *, ? plus mySQL ones
	function haswildcard ($text){
		if (strpos($text, '*') === false && strpos($text, '?') === false && strpos($text,'%') === False && strpos($text,'_') === False)
			return False;
		else
			return True;
	}

	///////////////////////////////////////////////////////////////////////////////

	if (count($_GET) > 0 && !$_GET['h']) {
		// name
		$names = explode(' ',trim($_GET['n']));
		if ($names[0] !== "") {
			foreach($names as $name) {
				if (substr($name,0,1) == '~') {
					if ($name == '~') continue;
					$names_exc[] = substr($name,1);
				} else
					$names_inc[] = $name;
			}

			if (is_array($names_inc)) {
				$where_is .= isset($where_is)?" AND (":"(";
				foreach($names_inc as $name) {
					if (!haswildcard($name))
						$name_is .= (isset($name_is)?" OR ":"")."u.username = ".sqlesc($name);
					else {
						$name = str_replace(array('?','*'), array('_','%'), $name);
						$name_is .= (isset($name_is)?" OR ":"")."u.username LIKE ".sqlesc($name);
					}
				}
				$where_is .= $name_is.")";
				unset($name_is);
			}

			if (is_array($names_exc)) {
				$where_is .= isset($where_is)?" AND NOT (":" NOT (";
				foreach($names_exc as $name) {
					if (!haswildcard($name))
						$name_is .= (isset($name_is)?" OR ":"")."u.username = ".sqlesc($name);
					else {
						$name = str_replace(array('?','*'), array('_','%'), $name);
						$name_is .= (isset($name_is)?" OR ":"")."u.username LIKE ".sqlesc($name);
					}
				}
				$where_is .= $name_is.")";
			}
			$q .= ($q ? "&amp;" : "") . "n=".urlencode(trim($_GET['n']));
		}

		// email
		$emaila = explode(' ', trim($_GET['em']));
		if ($emaila[0] !== "") {
			$where_is .= isset($where_is)?" AND (":"(";
			foreach($emaila as $email) {
				if (strpos($email,'*') === False && strpos($email,'?') === False && strpos($email,'%') === False) {
					if (validemail($email) !== 1) {
						show_error_msg(T_("ERROR"), "Bad email.");
					}
					$email_is .= (isset($email_is)?" OR ":"")."u.email =".sqlesc($email);
				} else {
					$sql_email = str_replace(array('?','*'), array('_','%'), $email);
					$email_is .= (isset($email_is)?" OR ":"")."u.email LIKE ".sqlesc($sql_email);
				}
			}
			$where_is .= $email_is.")";
			$q .= ($q ? "&amp;" : "") . "em=".urlencode(trim($_GET['em']));
		}

		//class
		// NB: the c parameter is passed as two units above the real one
		$class = $_GET['c'] - 2;
		if (is_valid_id($class + 1)) {
			$where_is .= (isset($where_is)?" AND ":"")."u.class=$class";
			$q .= ($q ? "&amp;" : "") . "c=".($class+2);
		}

		// IP
		$ip = trim($_GET['ip']);
		if ($ip) {
			$regex = "/^(((1?\d{1,2})|(2[0-4]\d)|(25[0-5]))(\.\b|$)){4}$/";
			if (!preg_match($regex, $ip)) {
				show_error_msg(T_("ERROR"), "Bad IP.");
			}

			$mask = trim($_GET['ma']);
			if ($mask == "" || $mask == "255.255.255.255") {
				$where_is .= (isset($where_is)?" AND ":"")."u.ip = '$ip'";
			} else {
				if (substr($mask,0,1) == "/") {
					$n = substr($mask, 1, strlen($mask) - 1);
					if (!is_numeric($n) or $n < 0 or $n > 32) {
						show_error_msg(T_("ERROR"), "Bad subnet mask.");
					} else {
						$mask = long2ip(pow(2,32) - pow(2,32-$n));
					}
				} elseif (!preg_match($regex, $mask)) {
					show_error_msg(T_("ERROR"), "Bad subnet mask.");
				}
				$where_is .= (isset($where_is)?" AND ":"")."INET_ATON(u.ip) & INET_ATON('$mask') = INET_ATON('$ip') & INET_ATON('$mask')";
				$q .= ($q ? "&amp;" : "") . "ma=$mask";
			}
			$q .= ($q ? "&amp;" : "") . "ip=$ip";
		}

		// ratio
		$ratio = trim($_GET['r']);
		if ($ratio) {
			if ($ratio == '---') {
				$ratio2 = "";
				$where_is .= isset($where_is)?" AND ":"";
				$where_is .= " u.uploaded = 0 and u.downloaded = 0";
			} elseif (strtolower(substr($ratio,0,3)) == 'inf') {
				$ratio2 = "";
				$where_is .= isset($where_is)?" AND ":"";
				$where_is .= " u.uploaded > 0 and u.downloaded = 0";
			} else {
				if (!is_numeric($ratio) || $ratio < 0) {
					show_error_msg(T_("ERROR"), "Bad ratio.");
				}
				$where_is .= isset($where_is)?" AND ":"";
				$where_is .= " (u.uploaded/u.downloaded)";
				$ratiotype = $_GET['rt'];
				$q .= ($q ? "&amp;" : "") . "rt=$ratiotype";
				if ($ratiotype == "3") {
					$ratio2 = trim($_GET['r2']);
					if (!$ratio2) {
						show_error_msg(T_("ERROR"), "Two ratios needed for this type of search.");
					}
					if (!is_numeric($ratio2) or $ratio2 < $ratio) {
						show_error_msg(T_("ERROR"), "Bad second ratio.");
					}
					$where_is .= " BETWEEN $ratio and $ratio2";
					$q .= ($q ? "&amp;" : "") . "r2=$ratio2";
				} elseif ($ratiotype == "2") {
					$where_is .= " < $ratio";
				} elseif ($ratiotype == "1") {
					$where_is .= " > $ratio";
				} else {
					$where_is .= " BETWEEN ($ratio - 0.004) and ($ratio + 0.004)";
				}
			}
			$q .= ($q ? "&amp;" : "") . "r=$ratio";
		}

		// comment
		$comments = explode(' ',trim($_GET['co']));
		if ($comments[0] !== "") {
			foreach($comments as $comment) {
				if (substr($comment,0,1) == '~') {
					if ($comment == '~') continue;
					$comments_exc[] = substr($comment,1);
				} else {
					$comments_inc[] = $comment;
				}

				if (is_array($comments_inc)) {
					$where_is .= isset($where_is)?" AND (":"(";
					foreach($comments_inc as $comment) {
						if (!haswildcard($comment))
							$comment_is .= (isset($comment_is)?" OR ":"")."u.modcomment LIKE ".sqlesc("%".$comment."%");
						else {
							$comment = str_replace(array('?','*'), array('_','%'), $comment);
							$comment_is .= (isset($comment_is)?" OR ":"")."u.modcomment LIKE ".sqlesc($comment);
						}
					}
					$where_is .= $comment_is.")";
					unset($comment_is);
				}

				if (is_array($comments_exc)) {
					$where_is .= isset($where_is)?" AND NOT (":" NOT (";
					foreach($comments_exc as $comment) {
						if (!haswildcard($comment))
							$comment_is .= (isset($comment_is)?" OR ":"")."u.modcomment LIKE ".sqlesc("%".$comment."%");
						else {
							$comment = str_replace(array('?','*'), array('_','%'), $comment);
							$comment_is .= (isset($comment_is)?" OR ":"")."u.modcomment LIKE ".sqlesc($comment);
						}
					}
					$where_is .= $comment_is.")";
				}
			}
				$q .= ($q ? "&amp;" : "") . "co=".urlencode(trim($_GET['co']));
		}

		$unit = 1073741824; // 1GB

		// uploaded
		$ul = trim($_GET['ul']);
		if ($ul) {
			if (!is_numeric($ul) || $ul < 0) {
				show_error_msg(T_("ERROR"), "Bad uploaded amount.");
			}
			$where_is .= isset($where_is)?" AND ":"";
			$where_is .= " u.uploaded ";
			$ultype = $_GET['ult'];
			$q .= ($q ? "&amp;" : "") . "ult=$ultype";
			if ($ultype == "3") {
				$ul2 = trim($_GET['ul2']);
				if(!$ul2) {
					show_error_msg(T_("ERROR"), "Two uploaded amounts needed for this type of search.");
				}
				if (!is_numeric($ul2) or $ul2 < $ul) {
					show_error_msg(T_("ERROR"), "Bad second uploaded amount.");
				}
				$where_is .= " BETWEEN ".$ul*$unit." and ".$ul2*$unit;
				$q .= ($q ? "&amp;" : "") . "ul2=$ul2";
			} elseif ($ultype == "2") {
				$where_is .= " < ".$ul*$unit;
			} elseif ($ultype == "1") {
				$where_is .= " >". $ul*$unit;
			} else {
				$where_is .= " BETWEEN ".($ul - 0.004)*$unit." and ".($ul + 0.004)*$unit;
			}
			$q .= ($q ? "&amp;" : "") . "ul=$ul";
		}

		// downloaded
		$dl = trim($_GET['dl']);
		if ($dl) {
			if (!is_numeric($dl) || $dl < 0) {
				show_error_msg(T_("ERROR"), "Bad downloaded amount.");
			}
			$where_is .= isset($where_is)?" AND ":"";
			$where_is .= " u.downloaded ";
			$dltype = $_GET['dlt'];
			$q .= ($q ? "&amp;" : "") . "dlt=$dltype";
			if ($dltype == "3") {
				$dl2 = trim($_GET['dl2']);
				if(!$dl2) {
					show_error_msg(T_("ERROR"), "Two downloaded amounts needed for this type of search.");
				}
				if (!is_numeric($dl2) or $dl2 < $dl) {
					show_error_msg(T_("ERROR"), "Bad second downloaded amount.");
				}
				$where_is .= " BETWEEN ".$dl*$unit." and ".$dl2*$unit;
				$q .= ($q ? "&amp;" : "") . "dl2=$dl2";
			} elseif ($dltype == "2") {
				$where_is .= " < ".$dl*$unit;
			} elseif ($dltype == "1") {
				$where_is .= " > ".$dl*$unit;
			} else {
				$where_is .= " BETWEEN ".($dl - 0.004)*$unit." and ".($dl + 0.004)*$unit;
			}
			$q .= ($q ? "&amp;" : "") . "dl=$dl";
		}

		// date joined
		$date = trim($_GET['d']);
		if ($date) {
			if (!$date = mkdate($date)) {
				show_error_msg(T_("ERROR"), "Invalid date.");
			}
			$q .= ($q ? "&amp;" : "") . "d=$date";
			$datetype = $_GET['dt'];
			$q .= ($q ? "&amp;" : "") . "dt=$datetype";
			if ($datetype == "0") {
				// For mySQL 4.1.1 or above use instead
				// $where_is .= (isset($where_is)?" AND ":"")."DATE(added) = DATE('$date')";
				$where_is .= (isset($where_is)?" AND ":"")."(UNIX_TIMESTAMP(added) - UNIX_TIMESTAMP('$date')) BETWEEN 0 and 86400";
			} else {
				$where_is .= (isset($where_is)?" AND ":"")."u.added ";
				if ($datetype == "3") {
					$date2 = mkdate(trim($_GET['d2']));
					if ($date2) {
						if (!$date = mkdate($date)) {
							show_error_msg(T_("ERROR"), "Invalid date.");
						}
						$q .= ($q ? "&amp;" : "") . "d2=$date2";
						$where_is .= " BETWEEN '$date' and '$date2'";
					} else {
						show_error_msg(T_("ERROR"), "Two dates needed for this type of search.");
					}
				} elseif ($datetype == "1") {
					$where_is .= "< '$date'";
				} elseif ($datetype == "2") {
					$where_is .= "> '$date'";
				}
			}
		}

		// date last seen
		$last = trim($_GET['ls']);
		if ($last) {
			if (!$last = mkdate($last)) {
				show_error_msg(T_("ERROR"), "Invalid date.");
			}
			$q .= ($q ? "&amp;" : "") . "ls=$last";
			$lasttype = $_GET['lst'];
			$q .= ($q ? "&amp;" : "") . "lst=$lasttype";
			if ($lasttype == "0") {
				// For mySQL 4.1.1 or above use instead
				// $where_is .= (isset($where_is)?" AND ":"")."DATE(added) = DATE('$date')";
				$where_is .= (isset($where_is)?" AND ":"")."(UNIX_TIMESTAMP(last_access) - UNIX_TIMESTAMP('$last')) BETWEEN 0 and 86400";
			} else {
				$where_is .= (isset($where_is)?" AND ":"")."u.last_access ";
				if ($lasttype == "3") {
					$last2 = mkdate(trim($_GET['ls2']));
					if ($last2) {
						$where_is .= " BETWEEN '$last' and '$last2'";
						$q .= ($q ? "&amp;" : "") . "ls2=$last2";
					} else {
						show_error_msg(T_("ERROR"), "The second date is not valid.");
					}
				} elseif ($lasttype == "1") {
					$where_is .= "< '$last'";
				} elseif ($lasttype == "2") {
					$where_is .= "> '$last'";
				}
			}
		}

		// status
		$status = $_GET['st'];
		if ($status) {
			$where_is .= ((isset($where_is))?" AND ":"");
			if ($status == "1") {
				$where_is .= "u.status = 'confirmed'";
			} else {
				$where_is .= "u.status = 'pending'";
			}
			$q .= ($q ? "&amp;" : "") . "st=$status";
		}

		// account status
		$accountstatus = $_GET['as'];
		if ($accountstatus) {
			$where_is .= (isset($where_is))?" AND ":"";
			if ($accountstatus == "1") {
				$where_is .= " u.enabled = 'yes'";
			} else {
				$where_is .= " u.enabled = 'no'";
			}
			$q .= ($q ? "&amp;" : "") . "as=$accountstatus";
		}

		//donor
		$donor = $_GET['do'];
		if ($donor) {
			$where_is .= (isset($where_is))?" AND ":"";
			if ($donor == 1) {
				$where_is .= " u.donated > '1'";
			} else {
				$where_is .= " u.donated < '1'";
			}
			$q .= ($q ? "&amp;" : "") . "do=$donor";
		}

		//warned
		$warned = $_GET['w'];
		if ($warned) {
			$where_is .= (isset($where_is))?" AND ":"";
			if ($warned == 1) {
				$where_is .= " u.warned = 'yes'";
			} else {
				$where_is .= " u.warned = 'no'";
			}
			$q .= ($q ? "&amp;" : "") . "w=$warned";
		}

		// disabled IP
		$disabled = $_GET['dip'];
		if ($disabled) {
			$distinct = "DISTINCT ";
			$join_is .= " LEFT JOIN users AS u2 ON u.ip = u2.ip";
			$where_is .= ((isset($where_is))?" AND ":"")."u2.enabled = 'no'";
			$q .= ($q ? "&amp;" : "") . "dip=$disabled";
		}

		// active
		$active = $_GET['ac'];
		if ($active == "1") {
			$distinct = "DISTINCT ";
			$join_is .= " LEFT JOIN peers AS p ON u.id = p.userid";
			$q .= ($q ? "&amp;" : "") . "ac=$active";
		}


		$from_is = "users AS u".$join_is;
		$distinct = isset($distinct)?$distinct:"";

		$queryc = "SELECT COUNT(".$distinct."u.id) FROM ".$from_is.
		(($where_is == "")?"":" WHERE $where_is ");

		$querypm = "FROM ".$from_is.(($where_is == "")?" ":" WHERE $where_is ");

		$select_is = "u.id, u.username, u.email, u.status, u.added, u.last_access, u.ip,
		u.class, u.uploaded, u.downloaded, u.donated, u.modcomment, u.enabled, u.warned";

		$query = "SELECT ".$distinct." ".$select_is." ".$querypm;


		$res = mysql_query($queryc) or sqlerr();
		$arr = mysql_fetch_row($res);
		$count = $arr[0];

		$q = isset($q)?($q."&amp;"):"";

		$perpage = 30;

		list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "admincp.php?action=usersearch&amp;$q");

		$query .= $limit;

		$res = mysql_query($query) or sqlerr();

		if (mysql_num_rows($res) == 0) {
		show_error_msg("Warning","No user was found.");
		} else {
			if ($count > $perpage) {
				echo $pagertop;
			}
			echo "<table border=1 style='border-collapse: collapse' bordercolor=#646262 cellspacing=0 cellpadding=5>\n";
			echo "<tr><td class=colhead align=left>Name</td>
			<td class=colhead align=left>IP</td>
			<td class=colhead align=left>".T_("EMAIL")."</td>".
			"<td class=colhead align=left>Joined:</td>".
			"<td class=colhead align=left>Last Seen:</td>".
			"<td class=colhead align=left>Status</td>".
			"<td class=colhead align=left>Enabled</td>".
			"<td class=colhead>Ratio</td>".
			"<td class=colhead>Uploaded</td>".
			"<td class=colhead>Downloaded</td>".
			"<td class=colhead>History</td>".
			"<td class=colhead colspan=2>Status</td></tr>\n";

			while ($user = mysql_fetch_array($res)) {
				if ($user['added'] == '0000-00-00 00:00:00')
					$user['added'] = '---';
				if ($user['last_access'] == '0000-00-00 00:00:00')
					$user['last_access'] = '---';

			if ($user['ip']) {
				$ipstr = $user['ip'];
			} else {
				$ipstr = "---";
			}

			$pul = $user['uploaded'];
			$pdl = $user['downloaded'];


			$auxres = mysql_query("SELECT COUNT(DISTINCT p.id) FROM forum_posts AS p LEFT JOIN forum_topics as t ON p.topicid = t.id
			LEFT JOIN forum_forums AS f ON t.forumid = f.id WHERE p.userid = " . $user['id'] . " AND f.minclassread <= " .
			$CURUSER['class']) or sqlerr();

			$n = mysql_fetch_row($auxres);
			$n_posts = $n[0];

			$auxres = mysql_query("SELECT COUNT(id) FROM comments WHERE user = ".$user['id']) or sqlerr();
			$n = mysql_fetch_row($auxres);
			$n_comments = $n[0];

			echo "<tr><td><b><a href='account-details.php?id=$user[id]'>$user[username]</a></b></td>" .
				"<td>" . $ipstr . "</td><td>" . $user['email'] . "</td>".
				"<td><div align=center>" . $user['added'] . "</div></td>".
				"<td><div align=center>" . $user['last_access'] . "</div></td>".
				"<td><div align=center>" . $user['status'] . "</div></td>".
				"<td><div align=center>" . $user['enabled']."</div></td>".
				"<td><div align=center>" . ratios($pul,$pdl) . "</div></td>".
				"<td><div align=right>" . mksize($user['uploaded']) . "</div></td>".
				"<td><div align=right>" . mksize($user['downloaded']) . "</div></td>".
				"<td><div align=center>$n_posts ".P_("POST", $n_posts)."<br/>$n_comments ".P_("COMMENT", $n_comments)."</div></td>".
				// This line actually needs rewriting, difficult to edit.
				"<td><div align=center>".($user["enabled"] == "yes" && $user["warned"] == "no" ? "--" : ($user["enabled"] == "no" ? "<img src=\"images/disabled.gif\" title=\"".T_("DISABLED")."\" alt=\"Disabled\">" : "") . ($user["warned"] == "yes" ? "<img src=\"images/warned.gif\" title=\"".T_("WARNED")."\" alt=\"Warned\">" : "")) . "</div></td>"."         <td><div align=center><form action='admincp.php?action=usersearch&do=warndisable' method=post><input type=checkbox name=\"warndisable[]\" value=" . $user['id'] . "><input type=hidden name=\"referer\" value=\"$_SERVER[REQUEST_URI]\"></div></td>         </tr>\n";
			}
			echo "</table>
			<b><BR>
			<table border=1 cellspacing=0 cellpadding=0 class=empty>
			<tr><td style=\"border: none; padding: 4px;\" colspan=2></td></tr>
			<tr><td style=\"border: none; padding: 2px;\" align=right><img src=\"images/disabled.gif\" alt=\"Disabled\"> <input type=submit name=disable value=\"Disable Selected Accounts\"></td><td style=\"border: none; padding: 2px;\" align=left><input type=submit name=enable value=\"Enable Selected Accounts\"> <img src=\"images/disabled.gif\" alt=\"Disabled\"> <img src=\"images/check.gif\" alt=\"Ok\"></td></tr>
			<tr><td style=\"border: none; padding: 4px;\" colspan=2><BR><BR></td></tr>
			<tr><td style=\"border: none; padding: 2px;\" align=center><img src=\"images/warned.gif\" alt=\"Warned\"> <input type=submit name=warn value=\"Warn Selected\"></td><td style=\"border: none; padding: 2px;\" align=left><input type=submit name=unwarn value=\"Remove Warning Selected\"> <img src=\"images/warned.gif\" alt=\"Warned\"> <img src=\"images/check.gif\" alt=\"Ok\"></td></tr>
			<tr><td style=\"border: none; padding: 2px;\" align=center colspan=2>Mod Comment (reason):<input type=text size=30 name=warnpm></td></tr>
			</table></center></form>\n";


			if ($count > $perpage) {
				echo $pagerbottom;
			}
		}
	}

	end_frame();
	stdfoot();
}
// End Advanced User Search
?>