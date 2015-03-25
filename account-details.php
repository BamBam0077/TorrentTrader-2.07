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
dbconn(false);
loggedinonly();

if($CURUSER["view_users"]=="no")
	show_error_msg(T_("ERROR"), T_("NO_USER_VIEW"), 1);


stdhead("User CP");

$id = (int)$_GET["id"];

if (!is_valid_id($id))
  show_error_msg(T_("NO_SHOW_DETAILS"), "Bad ID.",1);

$r = @mysql_query("SELECT * FROM users WHERE id=$id") or die(mysql_error());
$user = mysql_fetch_array($r) or  show_error_msg(T_("NO_SHOW_DETAILS"), T_("NO_USER_WITH_ID")." $id.",1);

//add invites check here

if (($user["enabled"] == "no" || ($user["status"] == "pending")) && $CURUSER["class"] < 4)
	show_error_msg(T_("ERROR"), T_("NO_ACCESS_ACCOUNT_DISABLED"), 1);

//get all vars first

//$country
$res = mysql_query("SELECT name FROM countries WHERE id=$user[country] LIMIT 1") or die(mysql_error());
if (mysql_num_rows($res) == 1){
	$arr = mysql_fetch_assoc($res);
	$country = "$arr[name]";
}

//$ratio
if ($user["downloaded"] > 0) {
    $ratio = $user["uploaded"] / $user["downloaded"];
}else{
	$ratio = "---";
}

//$numtorrents
$res = mysql_query("SELECT COUNT(*) FROM torrents WHERE owner=$id") or die(mysql_error());
$arr = mysql_fetch_row($res);
$numtorrents = $arr[0];

//$numcomments
$res = mysql_query("SELECT COUNT(*) FROM comments WHERE user=$id") or die(mysql_error());
$arr = mysql_fetch_row($res);
$numcomments = $arr[0];

$avatar = htmlspecialchars($user["avatar"]);
	if (!$avatar) {
		$avatar = $site_config["SITEURL"]."/images/default_avatar.gif";
	}

function peerstable($res){
	$ret = "<table align=center cellpadding=\"3\" cellspacing=\"0\" class=\"table_table\" width=\"95%\" border=\"1\"><tr><td class=table_head>".T_("NAME")."</td><td class=table_head align=center>".T_("SIZE")."</td><td class=table_head align=center>" .T_("UPLOADED"). "</td>\n<td class=table_head align=center>" .T_("DOWNLOADED"). "</td><td class=table_head align=center>" .T_("RATIO"). "</td></tr>\n";

	while ($arr = mysql_fetch_assoc($res)){
		$res2 = mysql_query("SELECT name,size FROM torrents WHERE id=$arr[torrent] ORDER BY name");
		$arr2 = mysql_fetch_assoc($res2);
		if ($arr["downloaded"] > 0){
			$ratio = number_format($arr["uploaded"] / $arr["downloaded"], 2);
		}else{
			$ratio = "---";
		}
		$ret .= "<tr><td class=table_col1><a href=torrents-details.php?id=$arr[torrent]&amp;hit=1><b>" . htmlspecialchars($arr2[name]) . "</b></a></td><td align=center class=table_col2>" . mksize($arr2["size"]) . "</td><td align=center class=table_col1>" . mksize($arr["uploaded"]) . "</td><td align=center class=table_col2>" . mksize($arr["downloaded"]) . "</td><td align=center class=table_col1>$ratio</td></tr>\n";
  }
  $ret .= "</table>\n";
  return $ret;
}


//Layout 
stdhead(T_("USER_DETAILS_FOR")." " . $user["username"]);

begin_frame(T_("USER_DETAILS_FOR")." " . $user["username"] . "");

if ($user["privacy"] != "strong" || ($CURUSER["control_panel"] == "yes")) {
	?>
	<table align="center" border="0" cellpadding="6" cellspacing="1" width="100%">
	<tr>
		<td width="50%" class="alt1"><b><?php echo T_("PROFILE"); ?></B></td>
		<td width="50%" class="alt1"><b><?php echo T_("ADDITIONAL_INFO"); ?></B></td>
	</tr>

	<tr valign="top">
		<td align="left" class="alt2">
		<?php echo T_("USERNAME"); ?>: <?php echo htmlspecialchars($user["username"])?><BR>
		<?php echo T_("USERCLASS"); ?>: <?php echo get_user_class_name($user["class"])?><BR>
		<?php echo T_("TITLE"); ?>: <I><?php echo htmlspecialchars($user["title"])?></I><BR>
		<?php echo T_("JOINED"); ?>: <?php echo htmlspecialchars(utc_to_tz($user["added"]))?><BR>
		<?php echo T_("LAST_VISIT"); ?>: <?php echo htmlspecialchars(utc_to_tz($user["last_access"]))?><BR>
		<?php echo T_("LAST_SEEN"); ?>: <?php echo htmlspecialchars($user["page"]);?><BR>
		</td>
		
		<td align="left">
		<?php echo T_("AGE"); ?>: <?php echo htmlspecialchars($user["age"])?><BR>
		<?php echo T_("CLIENT"); ?>: <?php echo htmlspecialchars($user["client"])?><BR>
		<?php echo T_("COUNTRY"); ?>: <?php echo $country?><BR>
		<?php echo T_("DONATED"); ?>: $<?php echo htmlspecialchars($user["donated"])?><BR>
		<?php echo T_("WARNINGS"); ?>: <?php echo htmlspecialchars($user["warned"])?><BR>
		<?php if ($user["privacy"] == "strong"){ echo "Privacy: <b>Strong</b><BR>"; }?>
		</td>	
	</tr>

	<tr>
		<td width="50%"><b><?php echo T_("STATISTICS"); ?></B></td>
		<td width="50%"><b><?php echo T_("OTHER"); ?></B></td>
	</tr>

	<tr valign="top">
		<td align="left">
		<?php echo T_("UPLOADED"); ?>: <?php echo mksize($user["uploaded"])?><BR>
		<?php echo T_("DOWNLOADED"); ?>: <?php echo mksize($user["downloaded"])?><BR>
		<?php echo T_("RATIO"); ?>: <?php echo $ratio?><BR>
		<?php echo T_("AVG_DAILY_DL"); ?>: <?php echo mksize($user["downloaded"] / (DateDiff($user["added"], time()) / 86400))?><BR>
		<?php echo T_("AVG_DAILY_UL"); ?>: <?php echo mksize($user["uploaded"] / (DateDiff($user["added"], time()) / 86400))?><BR>
		<?php echo T_("TORRENT_POSTED"); ?>: <?php echo $numtorrents?><BR>
		<?php echo T_("COMMENTS_POSTED"); ?>: <?php echo $numcomments?><BR>
		</td>
		
		<td align="left">
		<img src=<?php echo $avatar?>><BR>	
		<a href=mailbox.php?compose&id=<?php echo $user["id"]?>><?php echo T_("SEND_PM"); ?></a><BR>
		<!-- <a href=#>View Forum Posts</a><BR>
		<a href=#>View Comments</a><BR> -->
		<a href=report.php?user=<?php echo $user["id"]?>><?php echo T_("REPORT_MEMBER"); ?></a><BR>
		</td>
	</tr>
	<?php if ($CURUSER["edit_users"] == "yes") { ?>
	<tr>
		<td width="50%"><b><?php echo T_("STAFF_ONLY_INFO"); ?></B></td>
	</tr>
	
	<tr valign="top">
		<td align="left">
			<?php
				if ($user["invited_by"]) {
					$res = mysql_query("SELECT username FROM users WHERE id=$user[invited_by]");
					$row = mysql_fetch_array($res);
					echo "<b>".T_("INVITED_BY").":</B> <a href=\"account-details.php?id=$user[invited_by]\">$row[username]</a><BR>";
				}
				echo "<b>".T_("INVITES")."</b>:</B> ".$user[invites]."<BR>";
				$invitees = array_reverse(explode(" ", $user["invitees"]));
				$rows = array();
				foreach ($invitees as $invitee) { 
					$res = mysql_query("SELECT id, username FROM users WHERE id='$invitee' and status='confirmed'");
					if ($row = mysql_fetch_array($res)) {
						$rows[] = "<a href=\"account-details.php?id=$row[id]\">$row[username]</a>";
					}
				}
				if ($rows)
					echo "<b>".T_("INVITEES").":</b> ".implode(", ", $rows)."<BR>";
			?>
		</td>
	</tr>
	<?php
	}
	//team
	$res = mysql_query("SELECT name,image FROM teams WHERE id=$user[team] LIMIT 1") or die(mysql_error());
	if (mysql_num_rows($res) == 1) { 
		$arr = mysql_fetch_assoc($res); 
		echo "<tr><td colspan=2 align=left><B>Team Member Of:</B><BR>";
		echo"<img src='".htmlspecialchars($arr["image"])."'><BR>".sqlesc($arr["name"])."<BR><BR><a href=teams-view.php>[View ".T_("TEAMS")."]</a></td></tr>"; 
	}  
	?>
	
	</table>

	<?php
}else{
	echo sprintf(T_("REPORT_MEMBER_MSG"), $user["id"]);
}

end_frame();

if ($user["privacy"] != "strong" || ($CURUSER["control_panel"] == "yes")) {
	begin_frame("Local Activity");

	$res = mysql_query("SELECT torrent,uploaded,downloaded FROM peers WHERE userid='$id' AND seeder='yes'");
	if (mysql_num_rows($res) > 0)
	  $seeding = peerstable($res);

	$res = mysql_query("SELECT torrent,uploaded,downloaded FROM peers WHERE userid='$id' AND seeder='no'");
	if (mysql_num_rows($res) > 0)
	  $leeching = peerstable($res);

	if ($seeding)
		print("<b>" .T_("CURRENTLY_SEEDING"). ":</B><BR>$seeding<BR><BR>");

	if ($leeching)
		print("<b>" .T_("CURRENTLY_LEECHING"). ":</B><BR>$leeching<BR><BR>");

	if (!$leeching && !$seeding)
		print("<b>".T_("NO_ACTIVE_TRANSFERS")."<BR><BR>");

	end_frame();


	begin_frame("".T_("UPLOADED_TORRENTS")."");
	//page numbers
	$page = $_GET['page'];
	$perpage = 25;
	if ($CURUSER['control_panel'] != "yes")
		$where = "AND anon='no'";
	$res = mysql_query("SELECT COUNT(*) FROM torrents WHERE owner='$id' $where") or die(mysql_error());
	$row = mysql_fetch_array($res);
	$count = $row[0];
	unset($where);

	$orderby = "ORDER BY id DESC";

	//get sql info
	if ($count) {
		list($pagertop, $pagerbottom, $limit) = pager($perpage, $count, "account-details.php?id=$id&" . $addparam);
		$query = "SELECT torrents.id, torrents.category, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name AS cat_name, categories.parent_cat AS cat_parent, categories.image AS cat_pic, users.username, users.privacy, torrents.anon, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating, torrents.announce FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id WHERE owner = $id $orderby $limit";
		$res = mysql_query($query) or die(mysql_error());
	}else{
		unset($res);
	}

	if ($count) {
		print($pagerbottom);
		torrenttable($res);
		print($pagerbottom);
	}else {
		print("<b>".T_("UPLOADED_TORRENTS_ERROR")."<BR><BR>");
	}

	end_frame();
}



if($CURUSER["edit_users"]=="yes"){
	begin_frame(T_("STAFF_ONLY_INFO"));

	$avatar = htmlspecialchars($user["avatar"]);
	$signature = htmlspecialchars($user["signature"]);
	$uploaded = $user["uploaded"];
	$downloaded = $user["downloaded"];
	$enabled = $user["enabled"] == 'yes';
	$warned = $user["warned"] == 'yes';
	$forumbanned = $user["forumbanned"] == 'yes';
	$modcomment = htmlspecialchars($user["modcomment"]);

	print("<form method=post action=admin-modtasks.php>\n");
	print("<input type=hidden name='action' value='edituser'>\n");
	print("<input type=hidden name='userid' value='$id'>\n");
	print("<table border=0 cellspacing=0 cellpadding=3>\n");
	print("<tr><td>".T_("TITLE").": </td><td align=left><input type=text size=67 name=title value=\"$user[title]\"></tr>\n");
	print("<tr><td>".T_("EMAIL")."</td><td align=left><input type=text size=67 name=email value=\"$user[email]\"></tr>\n");
	print("<tr><td>".T_("SIGNATURE").": </td><td align=left><textarea type=text cols=50 rows=10 name=signature>".htmlspecialchars($user["signature"])."</textarea></tr>\n");
	print("<tr><td>".T_("UPLOADED").": </td><td align=left><input type=text size=30 name=uploaded value=\"".mksize($user["uploaded"], 9)."\"></tr>\n");
	print("<tr><td>".T_("DOWNLOADED").": </td><td align=left><input type=text size=30 name=downloaded value=\"".mksize($user["downloaded"], 9)."\"></tr>\n");
	print("<tr><td>".T_("AVATAR_URL")."</td><td align=left><input type=text size=67 name=avatar value=\"$avatar\"></tr>\n");
	print("<tr><td>".T_("IP_ADDRESS").": </td><td align=left><input type=text size=20 name=ip value=\"$user[ip]\"></tr>\n");
	print("<tr><td>".T_("INVITES").": </td><td align=left><input type=text size=4 name=invites value=".$user["invites"]."></tr>\n");

	if ($CURUSER["class"] > $user["class"]){
		print("<tr><td>".T_("CLASS").": </td><td align=left><select name=class>\n");
		$maxclass = $CURUSER["class"];
		for ($i = 1; $i < $maxclass; ++$i)
		print("<option value=$i" . ($user["class"] == $i ? " selected" : "") . ">$prefix" . get_user_class_name($i) . "\n");
		print("</select></td></tr>\n");
	}


	print("<tr><td>".T_("DONATED_US").": </td><td align=left><input type=text size=4 name=donated value=$user[donated]></tr>\n");
	print("<tr><td>".T_("PASSWORD").": </td><td align=left><input type=password size=67 name=password value=\"$user[password]\"></tr>\n");
	print("<tr><td>".T_("CHANGE_PASS").": </td><td align=left><input type=checkbox name=chgpasswd value='yes'/></td></tr>");
	print("<tr><td>".T_("MOD_COMMENT").": </td><td align=left><textarea cols=50 rows=10 name=modcomment>$modcomment</textarea></td></tr>\n");
	print("<tr><td>".T_("ACCOUNT_STATUS").": </td><td align=left><input name=enabled value=yes type=radio" . ($enabled ? " checked" : "") . ">Enabled <input name=enabled value=no type=radio" . (!$enabled ? " checked" : "") . ">Disabled</td></tr>\n");
	print("<tr><td>".T_("WARNED").": </td><td align=left><input name=warned value=yes type=radio" . ($warned ? " checked" : "") . ">Yes <input name=warned value=no type=radio" . (!$warned ? " checked" : "") . ">No</td></tr>\n");
	print("<tr><td>".T_("FORUM_BANNED").": </td><td align=left><input name=forumbanned value=yes type=radio" . ($forumbanned ? " checked" : "") . ">Yes <input name=forumbanned value=no type=radio" . (!$forumbanned ? " checked" : "") . ">No</td></tr>\n");
	print("<tr><td>".T_("PASSKEY").": </td><td align=left>$user[passkey]<BR><input name=resetpasskey value=yes type=checkbox>".T_("RESET_PASSKEY")." (".T_("RESET_PASSKEY_MSG").")</td></tr>\n");
	print("<tr><td colspan=2 align=center><input type=submit class=btn value='Submit'></td></tr>\n");
	print("</table>\n");
	print("</form>\n");
	  
	end_frame();
}

if($CURUSER["edit_users"]=="yes"){
	begin_frame(T_("BANS_WARNINGS"));
	
	$rqq = "SELECT * FROM warnings WHERE userid=$id ORDER BY id DESC";
	$res = mysql_query($rqq);

	if (mysql_num_rows($res) > 0){

		?>
		<b>Warnings:</b><BR>
		<CENTER><table align=center cellpadding="1" cellspacing="0" class="table_table" width="80%" border="1">
		<tr>
		<td class=table_head align=center>Added</td>
		<td class=table_head<?php echo T_("EXPIRE"); ?></td>
		<td class=table_head align=center><?php echo T_("REASON"); ?></td>
		<td class=table_head align=center><?php echo T_("WARNED_BY"); ?></td>
		<td class=table_head align=center><?php echo T_("TYPE"); ?></td>
		</tr>
		<?php

		while ($arr = MYSQL_FETCH_ARRAY($res)){
			if ($arr["warnedby"] == 0) {
				$wusername = "System";
			} else {
				$res2 = mysql_query("SELECT id,username FROM users WHERE id = ".$arr['warnedby']."") or die(mysql_error());
				$arr2 = mysql_fetch_array($res2);

				$wusername = htmlspecialchars($arr2["username"]);
			}
			$arr['added'] = utc_to_tz($arr['added']);
			$arr['expiry'] = utc_to_tz($arr['expiry']);

			$addeddate = substr($arr['added'], 0, strpos($arr['added'], " "));
			$expirydate = substr($arr['expiry'], 0, strpos($arr['expiry'], " "));
			print("<tr><td class=table_col1 align=center>$addeddate</td><td class=table_col2 align=center>$expirydate</td><td class=table_col1>".format_comment($arr['reason'])."</td><td class=table_col2 align=center><a href=account-details.php?id=".$arr2['id'].">".$wusername."</a></td><td class=table_col1 align=center>".$arr['type']."</td></tr>\n");
		 }

		echo "</table></CENTER>\n";
	}else{
		echo "<CENTER><b>This member currently has no warnings</B></CENTER>\n";
	}
	

	print("<form method=post action=admin-modtasks.php>\n");
	print("<input type=hidden name='action' value='addwarning'>\n");
	print("<input type=hidden name='userid' value='$id'>\n");
	echo "<BR><BR><CENTER><table border=0><tr><td align=right><b>".T_("REASON").":</b> </td><td align=left><textarea cols=40 rows=5 name=reason></textarea></td></tr>";
	echo "<tr><td align=right><b>".T_("EXPIRE").":</b> </td><td align=left><input type=text size=4 name=expiry>(days)</td></tr>";
	echo "<tr><td align=right><b>".T_("TYPE").":</b> </td><td align=left><input type=text size=10 name=type></td></tr>";
	echo "<tr><td colspan=2 align=center><input type=submit value='".T_("ADD_WARNING")."'></td></tr></table></CENTER></form>";

	if($CURUSER["level"]=="Administrator"){
		print("<hr><CENTER><form method=post action=admin-modtasks.php>\n");
		print("<input type=hidden name='action' value='deleteaccount'>\n");
		print("<input type=hidden name='userid' value='$id'>\n");
		print("<input type=hidden name='username' value='".$user["username"]."'>\n");
		echo "<b>Reason:</B><input type=text size=30 name=delreason>";
		echo "&nbsp;<input type=submit value='".T_("DELETE_ACCOUNT")."'></form></CENTER>";
	}

	end_frame();
}

stdfoot();

?>