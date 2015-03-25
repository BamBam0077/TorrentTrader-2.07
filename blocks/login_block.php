<?php
if (!$CURUSER) {
	begin_block(T_("LOGIN"));
?>
<table border=0 width=100% cellspacing=0 cellpadding=0>
	<tr><td>
		<form method="post" action="account-login.php">
		<div align="center">
		<table border="0" cellpadding="1">
			<tr>
			<td align="center"><font face="Verdana" size="1"><b><?php echo T_("USERNAME"); ?>:</b></font></td>
			</tr><tr>
			<td align="center"><input type="text" size="12" name="username" style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-width: 1px; background-color: #C0C0C0" /></td>
			</tr><tr>
			<td align="center"><font face="Verdana" size="1"><b><?php echo T_("PASSWORD"); ?>:</b></font></td>
			</tr><tr>
			<td align="center"><input type="password" size="12" name="password" style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-width: 1px; background-color: #C0C0C0" /></td>
			</tr><tr>
			<td align="center"><input type="submit" value="<?php echo T_("LOGIN"); ?>" style="font-family: Verdana; font-size: 8pt; font-weight: bold; border-collapse: collapse; border-width: 1px"></td>
			</tr>
		</table>
		</td>
		</form>
		</tr>
	<tr>
<td align="center">[<a href="account-signup.php"><?php echo T_("SIGNUP");?></a>]<br>[<a href="account-recover.php"><?php echo T_("RECOVER_ACCOUNT");?></a>]</td> </tr>
	</table>
<?php
end_block();

} else {

begin_block("$CURUSER[username]");

	$avatar = htmlspecialchars($CURUSER["avatar"]);
	if (!$avatar)
		$avatar = "".$site_config["SITEURL"]."/images/default_avatar.gif";

	$userdownloaded = mksize($CURUSER["downloaded"]);
	$useruploaded = mksize($CURUSER["uploaded"]);
	$privacylevel = T_($CURUSER["privacy"]);

	if ($CURUSER["uploaded"] > 0 && $CURUSER["downloaded"] == 0)
		$userratio = "Inf.";
	elseif ($CURUSER["downloaded"] > 0)
		$userratio = number_format($CURUSER["uploaded"] / $CURUSER["downloaded"], 2);
	else
		$userratio = "---";

	print ("<center><img width=80 height=80 src=$avatar></center><br>" . T_("DOWNLOADED") . ": $userdownloaded<br>" . T_("UPLOADED") . ": $useruploaded<BR>".T_("CLASS").": ".T_($CURUSER["level"])."<BR>" . T_("ACCOUNT_PRIVACY_LVL") . ": $privacylevel<BR>". T_("RATIO") .": $userratio");

?>


<CENTER><a href="account.php"><?php echo T_("ACCOUNT"); ?></a> <br> 
<?php if ($CURUSER["control_panel"]=="yes") {print("<a href=\"admincp.php\">".T_("STAFFCP")."</a>");}?>
</CENTER>
<?php
end_block();
}
?>