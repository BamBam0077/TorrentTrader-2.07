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

unset($returnto);
if (!empty($_GET["returnto"])) {
	$returnto = $_GET["returnto"];
	if (!$_GET["nowarn"]) {
		$message = T_("MEMBERS_ONLY");
	}
}

if ($_POST["username"] && $_POST["password"]) {
	$password = md5($password);

	if (!empty($username) && !empty($password)) {
		$res = mysql_query("SELECT id, password, secret, status, enabled FROM users WHERE username = " . sqlesc($username) . "");
		$row = mysql_fetch_array($res);
	

		if (!$row)
			$message = T_("USERNAME_INCORRECT");
		elseif ($row["status"] == "pending")
			$message = T_("ACCOUNT_PENDING");
		elseif ($row["password"] != $password)
			$message = "Password Incorrect";
		elseif ($row["enabled"] == "no")
			$message = T_("ACCOUNT_DISABLED");
	} else
		$message = "Don't leave any required field blank.";

	if (!$message){
		logincookie($row["id"], $row["password"], $row["secret"]);
		if (!empty($_POST["returnto"])) {
			header("Refresh: 0; url=" . $_POST["returnto"]);
			die();
		}
		else {
			header("Refresh: 0; url=index.php");
			die();
		}
	}else{
		show_error_msg(T_("ACCESS_DENIED"), $message, 1);
	}
}

logoutcookie();

stdhead(T_("LOGIN"));

begin_frame(T_("LOGIN"));

?>

<form method="post" action="account-login.php">
	<div align="center">
	<table border="0" cellpadding=5>
		<tr><td><b><?php echo T_("USERNAME"); ?>:</B></td><td align=left><input type="text" size=40 name="username" /></td></tr>
		<tr><td><b><?php echo T_("PASSWORD"); ?>:</B></td><td align=left><input type="password" size=40 name="password" /></td></tr>
		<tr><td colspan="2" align="center"><input type="submit" value="<?php echo T_("LOGIN"); ?>" class=btn><BR><BR><i><?php echo T_("COOKIES");?></i></td></tr>
	</table>
	</div>
<?php

if (isset($returnto))
	print("<input type=\"hidden\" name=\"returnto\" value=\"" . htmlspecialchars($returnto) . "\" />\n");

?>

</form>
<p align="center"><a href="account-signup.php"><?php echo T_("SIGNUP"); ?></a> | <a href="account-recover.php"><?php echo T_("RECOVER_ACCOUNT"); ?></a></p>

<?php
end_frame();
stdfoot();
?>