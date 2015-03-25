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
loggedinonly;

if (!$site_config["INVITEONLY"] && !$site_config["ENABLEINVITES"]) {
	show_error_msg(T_("INVITES_DISABLED"), T_("INVITES_DISABLED_MSG"), 1);
}


$users = get_row_count("users", "WHERE enabled = 'yes'");

if ($users >= $site_config["maxusers_invites"]) {
	print("Sorry, The current user account limit (" . number_format($site_config["maxusersinvites"]) . ") has been reached. Inactive accounts are pruned all the time, please check back again later...");
	end_frame();
	exit;
}

if ($CURUSER["invites"] == 0) {
	show_error_msg(T_("YOU_HAVE_NO_INVITES"), T_("YOU_HAVE_NO_INVITES_MSG"), 1);
}

if ($_GET["take"]) {
	if (!validemail($email))
		show_error_msg(T_("ERROR"), T_("INVALID_EMAIL_ADDRESS"), 1);
		
	//check email isnt banned
	$maildomain = (substr($email, strpos($email, "@") + 1));
	$a = (@mysql_fetch_row(@mysql_query("select count(*) from email_bans where mail_domain='$email'"))) or die(mysql_error());
	if ($a[0] != 0)
		$message = sprintf(T_("EMAIL_ADDRESS_BANNED"), $email);

	$a = (@mysql_fetch_row(@mysql_query("select count(*) from email_bans where mail_domain='$maildomain'"))) or die(mysql_error());
	if ($a[0] != 0)
		$message = sprintf(T_("EMAIL_ADDRESS_BANNED"), $email);

	// check if email addy is already in use
	if (get_row_count("users", "WHERE email='$email'"))
		$message = sprintf(T_("EMAIL_ADDRESS_INUSE"), $email);
		
	if ($message)
		show_error_msg(T_("ERROR"), $message, 1);

	$secret = mksecret();
	$username = mksecret(40);
	$ret = mysql_query("INSERT INTO users (username, secret, email, status, invited_by, added, stylesheet, language) VALUES (".
	implode(",", array_map("sqlesc", array($username, $secret, $email, 'pending', $CURUSER["id"]))) . ",'" . get_date_time() . "', $site_config[default_theme], $site_config[default_language])");

	if (!$ret) {
		// If username is somehow taken, keep trying
		while (mysql_errno() == 1062) {
			$username = mksecret(40);
			$ret = mysql_query("INSERT INTO users (username, secret, email, status, invited_by, added, stylesheet, language) VALUES (".
			implode(",", array_map("sqlesc", array($username, $secret, $email, 'pending', $CURUSER["id"]))) . ",'" . get_date_time() . "', $site_config[default_theme], $site_config[default_language])");
		}
		show_error_msg(T_("ERROR"), T_("DATABASE_ERROR"), 1);
	}

	$id = mysql_insert_id();
	$invitees = "$id $CURUSER[invitees]";
	mysql_query("UPDATE users SET invites = invites - 1, invitees='$invitees' WHERE id = $CURUSER[id]");

	$psecret = md5($secret);

	$mess = strip_tags($mess);

	$body = <<<EOD
You have been invited to $site_config[SITENAME] by $CURUSER[username]. They have specified this address ($email) as your email.
If you do not know this person, please ignore this email. Please do not reply.

Message:
-------------------------------------------------------------------------------
$mess
-------------------------------------------------------------------------------

This is a private site and you must agree to the rules before you can enter:

$site_config[SITEURL]/rules.php
$site_config[SITEURL]/faq.php


To confirm your invitation, you have to follow this link:

$site_config[SITEURL]/account-signup.php?invite=$id&secret=$psecret

After you do this, you will be able to use your new account. If you fail to
do this, your account will be deleted within a few days. We urge you to read
the RULES and FAQ before you start using $site_config[SITENAME].
EOD;
	sendmail($email, "$site_config[SITENAME] user registration confirmation", $body, "From: $site_config[SITEEMAIL]", "-f$site_config[SITEEMAIL]");

	header("Refresh: 0; url=account-confirm-ok.php?type=invite&email=" . urlencode($email));
	die;
}

stdhead("Invite");
begin_frame("Invite");
?>

<p>
<form method="post" action="invite.php?take=1">
<table border="0" cellspacing=0 cellpadding="3">
<tr valign=top><td align="right" class="heading"><B><?php echo T_("EMAIL_ADDRESS");?>:</B></td><td align=left><input type="text" size="40" name="email" />
<table width=250 border=0 cellspacing=0 cellpadding=0><tr><td class=embedded><font class=small><?php echo T_("EMAIL_ADDRESS_VALID_MSG");?></td></tr>
</font></td></tr></table>
<tr><td align="right" class="heading"><B><?php echo T_("MESSAGE");?>:</B></td><td align=left><textarea name="mess" rows="10" cols="80"></textarea>
</td></tr>
<tr><td colspan="2" align="center"><input type=submit value="<?php echo T_("SEND_AN_INVITE");?> (PRESS ONLY ONCE)" style='height: 25px'></td></tr>
</table>
</form>
<?php
end_frame();
stdfoot();

?>