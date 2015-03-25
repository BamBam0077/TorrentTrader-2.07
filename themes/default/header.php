<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<script language="JavaScript">
<!--
function Smilies(Smilie) {
document.Form.body.value+=Smilie+" ";
document.Form.body.focus();
}
//-->
</script>
<title><?php echo $title; ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $site_config["CHARSET"]; ?>">
<link rel="stylesheet" type="text/css" href="<?php echo $site_config["SITEURL"]; ?>/themes/default/theme.css">
<script type="text/javascript" src="<?php echo $site_config["SITEURL"]; ?>/backend/java_klappe.js"></script>
</head>

<BODY LEFTMARGIN="0" TOPMARGIN="0" MARGINWIDTH="0" MARGINHEIGHT="0" align="center">
<TABLE WIDTH="100%" BORDER="0" CELLPADDING="0" CELLSPACING="0">
	<!-- THIS IS THE TOP LOGO AREA 3 CELLS -->
	<TR>
	<TD WIDTH="500" align="left"><img src="<?php echo $site_config["SITEURL"]; ?>/themes/default/images/logo.jpg"><BR></TD>
	<TD>&nbsp;</TD>
	<TD WIDTH="500" align="right">
	<?php
	if (!$CURUSER){
		echo "[<a href=\"account-login.php\">".T_("LOGIN")."</a>]<B> ".T_("OR")." </B>[<a href=\"account-signup.php\">".T_("SIGNUP")."</a>]&nbsp;&nbsp;";
	}else{
		print (T_("LOGGED_IN_AS").": ".$CURUSER["username"].""); 
		echo " [<a href=\"account-logout.php\">".T_("LOGOUT")."</a>]&nbsp;&nbsp;<BR>";
		if ($CURUSER["control_panel"]=="yes") {
			print("[<a href=admincp.php>".T_("STAFFCP")."</a>]&nbsp;&nbsp;");
		}

		//check for new pm's
		$res = mysql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " and unread='yes' AND location IN ('in','both')") or print(mysql_error());
		$arr = mysql_fetch_row($res);
		$unreadmail = $arr[0];
		if ($unreadmail){
			print("<font color=#FF0000><B>".T_("NEW_MESSAGE")."(<a href=mailbox.php?inbox>$unreadmail</a>)</b></a></font>&nbsp;&nbsp;");
		}else{
			print("[<a href=mailbox.php>".T_("YOUR_MESSAGES")."</a>]&nbsp;&nbsp;");
		}
		//end check for pm's
	}
	?>
	</TD>
	</TR><!-- END TOP LOGO AREA -->	
</TABLE>

<table width="100%">
<TR>
<td align="middle" class="subnav" height="34">
<a href="index.php"><B><?php echo T_("HOME");?></B></a>  | <a href=forums.php ><B><?php echo T_("FORUMS");?></B></a> | <a href=torrents-upload.php ><B><?php echo T_("UPLOAD_TORRENT");?></B></a> | <a href=torrents.php ><B><?php echo T_("BROWSE_TORRENTS");?></B></a> | <a href=torrents-today.php ><B><?php echo T_("TODAYS_TORRENTS");?></B></a> | <a href=torrents-search.php ><B><?php echo T_("SEARCH_TORRENTS");?></B></a>
</td>
</TR>
</table>

<!-- End Page Head -->

<TABLE height="100%" cellSpacing="0" cellPadding="0" width="100%" border="0" align="center">
<TBODY>
<TR><TD vAlign="top" height="100%">
	<TABLE cellSpacing="4" cellPadding="0" width="100%" border="0" >
	<TBODY>
	<TR>

	<?php if ($site_config["LEFTNAV"]){?>
	<TD vAlign="top" width="170">
	<?php leftblocks();?>
	</TD>
	<?php } //LEFTNAV ON/OFF END?>

	<TD vAlign="top"><!-- MAIN CENTER CONTENT START -->

	<?php
	if ($site_config["MIDDLENAV"]){
		middleblocks();
	} //MIDDLENAV ON/OFF END
	?>
