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

stdhead("User CP");

function navmenu(){
?>
	<BR><table align=center cellpadding=0 cellspacing=3 style='border-collapse: collapse' bordercolor=#646262 width=95% border=1><tr><td>
		<table border=0 width=100%>
		<tr>
		<td width=100% align=center>
		<?php print("<a href=account.php><b>".T_("YOUR_PROFILE")."</b></a>");?>
		&nbsp;|&nbsp;
		<?php print("<a href=account.php?action=edit_settings&do=edit><b>".T_("YOUR_SETTINGS")."</b></a>");?>
		&nbsp;|&nbsp;
		<?php print("<a href=account.php?action=changepw><b>".T_("CHANGE_PASS")."</b></a>");?>
		&nbsp;|&nbsp;
		<?php print("<a href=account.php?action=mytorrents><b>".T_("YOUR_TORRENTS")."</b></a>");?>
		&nbsp;|&nbsp;
		<?php print("<a href=mailbox.php><b>".T_("YOUR_MESSAGES")."</b></a>");?>
		</td></tr>
		</table>
	</td></tr></table>
	<BR>
	<?php
}//end func


if (!$action){
	begin_frame(T_("USER").": $CURUSER[username] (".T_("ACCOUNT_PROFILE").")");

	$usersignature = stripslashes(format_comment($CURUSER["signature"]));

	$avatar = $CURUSER["avatar"];
	if (!$avatar) {
		$avatar = $site_config["SITEURL"]."/images/default_avatar.gif";
	}
	navmenu();
	?>
	<table border=0 width=100%>
	<TR><TD width=50% valign=top>
	<b><?php echo T_("USERNAME"); ?>:</b> <?php echo $CURUSER["username"]; ?><br>
	<b><?php echo T_("CLASS"); ?>:</b> <?php echo $CURUSER["level"]; ?><br>
	<b><?php echo T_("EMAIL"); ?>:</b> <?php echo $CURUSER["email"]; ?><br>
	<b><?php echo T_("JOINED"); ?>:</b> <?php echo utc_to_tz($CURUSER["added"]); ?><br>
	<b><?php echo T_("AGE"); ?>:</b> <?php echo $CURUSER["age"]; ?><br>
	<b><?php echo T_("GENDER"); ?>:</b> <?php echo T_($CURUSER["gender"]); ?><br>
	<b><?php echo T_("PREFERRED_CLIENT"); ?>:</b> <?php echo htmlspecialchars($CURUSER["client"]); ?><br>
	<b><?php echo T_("DONATED"); ?>:</b> <?php echo $site_config['currency_symbol']; ?><?php echo number_format($CURUSER["donated"], 2); ?><br>
	<b><?php echo T_("CUSTOM_TITLE"); ?>:</b> <?php echo strip_tags($CURUSER["title"]); ?><br>
	<b><?php echo T_("ACCOUNT_PRIVACY_LVL"); ?>:</b> <?php echo T_($CURUSER["privacy"]); ?><br>
	<b><?php echo T_("SIGNATURE"); ?>:</b> <?php echo format_comment($usersignature); ?><br>
	<b><?php echo T_("PASSKEY"); ?>;</b> <?php echo $CURUSER["passkey"]; ?><br>
	<?php
		if ($CURUSER["invited_by"]) {
			$res = mysql_query("SELECT username FROM users WHERE id=$CURUSER[invited_by]");
			$row = mysql_fetch_array($res);
			echo "<b>".T_("INVITED_BY").":</B> <a href=\"account-details.php?id=$CURUSER[invited_by]\">$row[username]</a><BR>";
		}
		echo "<b>".T_("INVITES").":</B> $CURUSER[invites]<BR>";
		$invitees = array_reverse(explode(" ", $CURUSER["invitees"]));
		$rows = array();
		foreach ($invitees as $invitee) { 
			$res = mysql_query("SELECT id, username FROM users WHERE id='$invitee' and status='confirmed'");
			if ($row = mysql_fetch_array($res)) {
				$rows[] = "<a href=\"account-details.php?id=$row[id]\">$row[username]</a>";
			}
		}
		if ($rows)
			echo "<b>".T_("INVITED").":</B> ".implode(", ", $rows)."<BR>";
	?>
	<?php print("<b>".T_("IP").":</b> " . $CURUSER["ip"] . "\n"); ?><br>
	</td></tr>
	</table>
	<BR><BR>
	<?php
	end_frame();
}

/////////////// MY TORRENTS ///////////////////

if ($action=="mytorrents"){
begin_frame(T_("YOUR_TORRENTS"), center);
navmenu();
//page numbers
$page = $_GET['page'];
$perpage = 200;

$res = mysql_query("SELECT COUNT(*) FROM torrents WHERE torrents.owner = " . $CURUSER["id"] ."") or die(mysql_error());
$arr = mysql_fetch_row($res);
$pages = floor($arr[0] / $perpage);
if ($pages * $perpage < $arr[0])
  ++$pages;

if ($page < 1)
  $page = 1;
else
  if ($page > $pages)
    $page = $pages;

for ($i = 1; $i <= $pages; ++$i)
  if ($i == $page)
    $pagemenu .= "$i\n";
  else
    $pagemenu .= "<a href=account.php?action=mytorrents&page=$i>$i</a>\n";

if ($page == 1)
  $browsemenu .= "";
else
  $browsemenu .= "<a href=account.php?action=mytorrents&page=" . ($page - 1) . ">[Prev]</a>";

$browsemenu .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

if ($page == $pages)
  $browsemenu .= "";
else
  $browsemenu .= "<a href=account.php?action=mytorrents&page=" . ($page + 1) . ">[Next]</a>";

$offset = ($page * $perpage) - $perpage;
//end page numbers


$where = "WHERE torrents.owner = " . $CURUSER["id"] ."";
$orderby = "ORDER BY added DESC";

$query = mysql_query("SELECT torrents.id, torrents.category, torrents.name, torrents.added, torrents.hits, torrents.banned, torrents.comments, torrents.seeders, torrents.leechers, torrents.times_completed, categories.name AS cat_name, categories.parent_cat AS cat_parent FROM torrents LEFT JOIN categories ON category = categories.id $where $orderby LIMIT $offset,$perpage")or die(mysql_error());

$allcats = mysql_num_rows($query);
	if($allcats == 0) {
		echo "<h4>".T_("NO_UPLOADS")."</h4>\n";
	}else{
		print("<p align=center>$pagemenu<br />$browsemenu</p>");
?><table align=center cellpadding="0" cellspacing="0" class="ttable_headouter" width=100%>
<td>
<table align=center cellpadding="0" cellspacing="0" class="ttable_headinner" width="100%">
<tr>
<td class=ttable_head><?php echo T_("TYPE");?></td>
<td class=ttable_head><?php echo T_("NAME");?></td>
<td class=ttable_head><?php echo T_("COMMENTS");?></td>
<td class=ttable_head><?php echo T_("HITS");?></td>
<td class=ttable_head><?php echo T_("SEEDS");?></td>
<td class=ttable_head><?php echo T_("LEECHERS");?></td>
<td class=ttable_head><?php echo T_("COMPLETED");?></td>
<td class=ttable_head><?php echo T_("ADDED");?></font></td>
<td class=ttable_head><?php echo T_("EDIT");?></td>
</tr>
<?php
		while($row = MYSQL_FETCH_ARRAY($query))
			{
			$char1 = 35; //cut length 
			$smallname = CutName(htmlspecialchars($row["name"]), $char1);
			echo "<tr><td class=ttable_col2 align=center>$row[cat_parent]: $row[cat_name]</td><td class=ttable_col1 align=left><a href='torrents-details.php?id=$row[id]'>$smallname</A></td><td class=ttable_col2 align=center><a href=comments.php?type=torrent&id=$row[id]>$row[comments]</a></td><td class=ttable_col1 align=center>$row[hits]</td><td class=ttable_col2 align=center>$row[seeders]</td><td class=ttable_col1 align=center>$row[leechers]</td><td class=ttable_col2 align=center>$row[times_completed]</td><td class=ttable_col1 align=center>".get_elapsed_time(sql_timestamp_to_unix_timestamp($row["added"]))."</td><td class=ttable_col2><a href='torrents-edit.php?id=$row[id]'>EDIT</td></tr>\n";
			}
		echo "</td></table></td></tr></table><BR>";
		print("<p align=center>$pagemenu<br />$browsemenu</p>");
	}

end_frame();
}


/////////////////////// EDIT SETTINGS ////////////////
if ($action=="edit_settings"){

	if ($do=="edit"){
	begin_frame(T_("EDIT_SETTINGS"));

	navmenu();
	?><CENTER>
	<form method=post action=account.php>
	<input type='hidden' name='action' value='edit_settings'>
	<input type='hidden' name='do' value='save_settings'>
	<table border="1" cellspacing=0 cellpadding="5" width="95%" class="table_table">
	<?php

	$ss_r = mysql_query("SELECT * from stylesheets") or die;
	$ss_sa = array();
	while ($ss_a = mysql_fetch_array($ss_r))
	{
	  $ss_id = $ss_a["id"];
	  $ss_name = $ss_a["name"];
	  $ss_sa[$ss_name] = $ss_id;
	}
	ksort($ss_sa);
	reset($ss_sa);
	while (list($ss_name, $ss_id) = each($ss_sa))
	{
	  if ($ss_id == $CURUSER["stylesheet"]) $ss = " selected"; else $ss = "";
	  $stylesheets .= "<option value=$ss_id$ss>$ss_name</option>\n";
	}

	$countries = "<option value=0>----</option>\n";
	$ct_r = mysql_query("SELECT id,name from countries ORDER BY name") or die;
	while ($ct_a = mysql_fetch_array($ct_r))
	  $countries .= "<option value=$ct_a[id]" . ($CURUSER["country"] == $ct_a['id'] ? " selected" : "") . ">$ct_a[name]</option>\n";

	$teams = "<option value=0>--- ".T_("NONE_SELECTED")." ----</option>\n";
	$sashok = mysql_query("SELECT id,name FROM teams ORDER BY name") or die;
	while ($sasha = mysql_fetch_array($sashok))
		$teams .= "<option value=$sasha[id]" . ($CURUSER["team"] == $sasha['id'] ? " selected" : "") . ">$sasha[name]</option>\n"; 


	$acceptpms = $CURUSER["acceptpms"] == "yes";
	print ("<TR><TD align=right class=table_col2><b>" . T_("ACCEPT_PMS") . ":</B> </td><td class=table_col2><input type=radio name=acceptpms" . ($acceptpms ? " checked" : "") .
	  " value=yes><b>".T_("FROM_ALL")."</B> <input type=radio name=acceptpms" .
	  ($acceptpms ? "" : " checked") . " value=no><b>" . T_("FROM_STAFF_ONLY") . "</B><br><i>".T_("ACCEPTPM_WHICH_USERS")."</i></td></tr>");
	  
	$gender = "<option value=Male" . ($CURUSER["gender"] == "Male" ? " selected" : "") . ">" . T_("Male") . "</option>\n"
		 ."<option value=Female" . ($CURUSER["gender"] == "Female" ? " selected" : "") . ">" . T_("Female") . "</option>\n";

	// START CAT LIST SQL
	$r = mysql_query("SELECT id,name,parent_cat FROM categories ORDER BY parent_cat ASC, sort_index ASC") or die(mysql_error());
	if (mysql_num_rows($r) > 0)
	{
		$categories .= "<table><tr>\n";
		$i = 0;
		while ($a = mysql_fetch_assoc($r))
		{
		  $categories .=  ($i && $i % 2 == 0) ? "</tr><tr>" : "";
		  $categories .= "<td class=bottom style='padding-right: 5px'><input name=cat$a[id] type=\"checkbox\" " . (strpos($CURUSER['notifs'], "[cat$a[id]]") !== false ? " checked" : "") . " value='yes'>&nbsp;" .htmlspecialchars($a["parent_cat"]).": " . htmlspecialchars($a["name"]) . "</td>\n";
		  ++$i;
		}
		$categories .= "</tr></table>\n";
	}

	// END CAT LIST SQL
	function priv($name, $descr) {
		global $CURUSER;
		if ($CURUSER["privacy"] == $name)
			return "<input type=\"radio\" name=\"privacy\" value=\"$name\" checked=\"checked\" /> $descr";
		return "<input type=\"radio\" name=\"privacy\" value=\"$name\" /> $descr";
	}

	print("<TR><TD align=right class=table_col1><B>" . T_("ACCOUNT_PRIVACY_LVL") . ":</B> </TD><TD align=left class=table_col1>". priv("normal", "<B>" . T_("NORMAL") . "</B>") . " " . priv("low", "<B>" . T_("LOW") . "</B>") . " " . priv("strong", "<B>" . T_("STRONG") . "</B>") . "<br><i>".T_("ACCOUNT_PRIVACY_LVL_MSG")."</i></td></tr>");
	print("<TR><TD align=right class=table_col2><B>" . T_("EMAIL_NOTIFICATION") . ":</B> </TD><TD align=left class=table_col2><input type=checkbox name=pmnotif" . (strpos($CURUSER['notifs'], "[pm]") !== false ? " checked" : "") .
	   " value=yes><B>" . T_("PM_NOTIFY_ME") . "</B><br><i>".T_("EMAIL_WHEN_PM")."</i></td></tr>");

	   //print("<TR><TD align=right class=table_col1 valign=top><b>".T_("CATEGORY_FILTER").": </B></td><TD align=left class=table_col1><i>The system will only display the following categories when browsing (uncheck all to disable filter).</i><BR>".$categories."</td></tr>");

	print("<TR><TD align=right class=table_col1><b>" . T_("THEME") . ":</b> </td><TD align=left class=table_col1><select name=stylesheet>\n$stylesheets\n</select></td></tr>");
	print("<TR><TD align=right class=table_col2><b>" . T_("PREFERRED_CLIENT") .":</b> </td><TD align=left class=table_col2><input type=text size=20 maxlength=20 name=client value=\"" . htmlspecialchars($CURUSER["client"]) . "\" /></td></tr>");
	print("<TR><TD align=right class=table_col1><b>" . T_("AGE") . ":</b> </td><TD align=left class=table_col1><input type=text size=3 maxlength=2 name=age value=\"" . htmlspecialchars($CURUSER["age"]) . "\" /></td></tr>");
	print("<TR><TD align=right class=table_col2><b>" . T_("GENDER") . ":</b> </td><TD align=left class=table_col2><select size=1 name=gender>\n$gender\n</select></td></tr>");
	print("<TR><TD align=right class=table_col1><b>" . T_("COUNTRY") . ":</b> </td><TD align=left class=table_col1><select name=country>\n$countries\n</select></td></tr>");

	if ($CURUSER["class"] > 1)
		print("<TR><TD align=right class=table_col1><b>".T_("TEAM").":</b> </td><TD align=left class=table_col1><select name=teams>\n$teams\n</select></td></tr>");

	print("<TR><TD align=right class=table_col2><b>" . T_("AVATAR_URL") . ":</b> </td><TD align=left class=table_col2><input name=avatar size=50 value=\"" . htmlspecialchars($CURUSER["avatar"]) .
	  "\"><br />\n<i>Link to your externally hosted avatar image: 80x80px</i></td></tr>");
	print("<TR><TD align=right class=table_col1><b>" . T_("CUSTOM_TITLE") . ":</b> </td><TD align=left class=table_col1><input name=title size=50 value=\"" . strip_tags($CURUSER["title"]) .
	  "\"><br />\n <I>" . T_("HTML_NOT_ALLOWED") . "</I></td></tr>");
	print("<TR><TD align=right class=table_col2 valign=top><b>" . T_("SIGNATURE") . ":</b> </td><TD align=left class=table_col2><textarea name=signature cols=50 rows=10>" . htmlspecialchars($CURUSER["signature"]) .
	  "</textarea><br />\n <I>".sprintf(T_("MAX_CHARS"), 150).", " . T_("HTML_NOT_ALLOWED") . "</I></td></tr>");

	print("<TR><TD align=right class=table_col1><B>".T_("NONE_SELECTED").":</b> </td><TD align=left class=table_col1><input type=checkbox name=resetpasskey value=1 />&nbsp;<I>".T_("RESET_PASSKEY_MSG").".</I></td></tr>");

	print("<TR><TD align=right class=table_col2><b>" . T_("EMAIL") . ":</b> </td><TD align=left class=table_col2><input type=\"text\" name=\"email\" size=50 value=\"" . htmlspecialchars($CURUSER["email"]) .
	  "\"><br />\n<I>".T_("REPLY_TO_CONFIRM_EMAIL")."</I><br></td></tr>");

	ksort($tzs);
	reset($tzs);
	while (list($key, $val) = each($tzs)) {
	if ($CURUSER["tzoffset"] == $key)
		$tz .= "<option value=\"$key\" selected>$val[0]</option>\n";
	else
		$tz .= "<option value=\"$key\">$val[0]</option>\n";
	}

	print("<TR><TD align=right class=table_col1><b>".T_("TIMEZONE").":</b> </td><TD align=left class=table_col1><select name='tzoffset'>$tz</select></td></tr>");

	?>
	<tr><td colspan="2" align="center"><input type="submit" value="<?php echo T_("SUBMIT");?>" style='height: 25px'> <input type="reset" value="<?php echo T_("REVERT");?>" style='height: 25px'></td></tr>
	</table></form>

	<?php
	end_frame();
	}


	if ($do == "save_settings"){
	begin_frame(T_("EDIT_ACCOUNT_SETTINGS"));

	navmenu();
		$set = array();
		  $updateset = array();
		  $changedemail = $newsecret = 0;

		  if ($email != $CURUSER["email"]) {
				if (!validemail($email))
					$message = T_("NOT_VALID_EMAIL");
				$changedemail = 1;
		  }

		  $acceptpms = $_POST["acceptpms"];
		  $pmnotif = $_POST["pmnotif"];
		  $privacy = $_POST["privacy"];
		  $notifs = ($pmnotif == 'yes' ? "[pm]" : "");
		  $r = mysql_query("SELECT id FROM categories") or die(mysql_error());
		  $rows = mysql_num_rows($r);
		  for ($i = 0; $i < $rows; ++$i) {
				$a = mysql_fetch_assoc($r);
				if ($_POST["cat$a[id]"] == 'yes')
				  $notifs .= "[cat$a[id]]";
		  }

		  if ($_POST['resetpasskey']) $updateset[] = "passkey=''"; 
		  
		  $avatar = strip_tags($_POST["avatar"]);
		  $title = strip_tags($_POST["title"]);
		  $signature = $_POST["signature"];
		  $stylesheet = $_POST["stylesheet"];
		  $language = $_POST["language"];
		  $client = strip_tags($_POST["client"]);
		  $age = $_POST["age"];
		  $gender= $_POST["gender"];
		  $country = $_POST["country"];
		  $teams = $_POST["teams"];
		  $privacy = $_POST["privacy"];
		  $timezone = (int)$_POST['tzoffset'];

		  if (is_valid_id($stylesheet))
			$updateset[] = "stylesheet = '$stylesheet'";
		  if (is_valid_id($language))
			$updateset[] = "language = '$language'";
		  if (is_valid_id($teams))
			$updateset[] = "team = '$teams'";
		  if (is_valid_id($country))
			$updateset[] = "country = $country";
		  if ($acceptpms == "yes")
			$acceptpms = 'yes';
		  else
			$acceptpms = 'no';
		  if (is_valid_id($age))
				$updateset[] = "age = '$age'";
			$updateset[] = "acceptpms = ".sqlesc($acceptpms);
			$updateset[] = "commentpm = " . sqlesc($pmnotif == "yes" ? "yes" : "no");
			$updateset[] = "notifs = ".sqlesc($notifs);
			$updateset[] = "privacy = ".sqlesc($privacy);
			$updateset[] = "gender = ".sqlesc($gender);
			$updateset[] = "client = ".sqlesc($client);
			$updateset[] = "avatar = " . sqlesc($avatar);
			$updateset[] = "signature = ".sqlesc($signature);
			$updateset[] = "title = ".sqlesc($title);
			$updateset[] = "tzoffset = $timezone";



		  /* ****** */

		  if (!$message) {

			if ($changedemail) {
				$sec = mksecret();
				$hash = md5($sec . $email . $sec);
				$obemail = rawurlencode($email);
				$updateset[] = "editsecret = " . sqlesc($sec);
				$thishost = $_SERVER["HTTP_HOST"];
				$thisdomain = preg_replace('/^www\./is', "", $thishost);
$body = <<<EOD
You have requested that your user profile (username {$CURUSER["username"]})
on {$site_config["SITEURL"]} should be updated with this email address ($email) as
user contact.

If you did not do this, please ignore this email. The person who entered your
email address had the IP address {$_SERVER["REMOTE_ADDR"]}. Please do not reply.

To complete the update of your user profile, please follow this link:

{$site_config["SITEURL"]}/account-ce.php?id={$CURUSER["id"]}&secret=$hash&email=$obemail

Your new email address will appear in your profile after you do this. Otherwise
your profile will remain unchanged.
EOD;

				sendmail($email, "$site_config[SITENAME] profile update confirmation", $body, "From: $site_config[SITEEMAIL]", "-f$site_config[SITEEMAIL]");
				$mailsent = 1;
			} //changedemail

			mysql_query("UPDATE users SET " . implode(",", $updateset) . " WHERE id = " . $CURUSER["id"]."") or die(mysql_error());
			$edited=1;
			echo "<br><br><center><b><font color=red>Updated OK</font></b></center><BR><BR>";
			if ($changedemail) {
				echo "<br><center><b>".T_("EMAIL_CHANGE_SEND")."</b></center><BR><BR>";
			}
		  }else{
			echo "<br><br><center><b><font color=red>".T_("ERROR").": ".$message."</font></b></center><BR><BR>";
		  }// message


		end_frame();
	}// end do

}//end action

if ($action=="changepw"){

	if ($do=="newpassword"){
		if ($chpassword != "") {
					$res = mysql_query("SELECT id, password, secret, enabled FROM users WHERE id = " . $CURUSER["id"] . " AND status = 'confirmed'");
					$row = mysql_fetch_array($res);

					if (strlen($chpassword) < 6)
						$message = T_("PASS_TOO_SHORT");
					if ($chpassword != $passagain)
						$message = T_("PASSWORDS_NOT_MATCH");
					$chpassword = md5($chpassword);
		}

		if ((!$chpassword) || (!$passagain))
			$message = "You must enter something!";

		begin_frame();
		navmenu();

		if (!$message){
			mysql_query("UPDATE users SET password = " . sqlesc($chpassword) . "  WHERE id = " . $CURUSER["id"]."") or die(mysql_error());
			echo "<br><br><center><b>".T_("PASSWORD_CHANGED_OK")."</b></center>";
			logoutcookie();
		}else{
			echo "<br><br><b><CENTER>".$message."</CENTER></B><br><br>";
		}

		
		end_frame();
		stdfoot();
		die();
	}//do
	
	begin_frame(T_("CHANGE_YOUR_PASS")); 
	navmenu();
	?>
	<form method=post action=account.php>
	<input type='hidden' name='action' value='changepw'>
	<input type='hidden' name='do' value='newpassword'>
	<table border="0" cellspacing=0 cellpadding="5" width="100%">
	<?php
	tr("<b>" .T_("NEW_PASSWORD"). ":</B>", "<input type=\"password\" name=\"chpassword\" size=\"50\" />", 1);
	tr("<b>" .T_("REPEAT"). ":</B>", "<input type=\"password\" name=\"passagain\" size=\"50\" />", 1);
	?>
	<tr><td colspan="2" align="center"><input type="submit" value="<?php echo T_("SUBMIT");?>" style='height: 25px'> <input type="reset" value="<?php echo T_("REVERT");?>" style='height: 25px'></td></tr>
	</table></form>
	<?php
	end_frame();
}



stdfoot();
?>
