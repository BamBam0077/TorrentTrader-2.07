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


$kind = "0";

if (is_valid_id($_POST["id"]) && strlen($_POST["secret"]) == 32) {
    $password = $_POST["password"];
    $password1 = $_POST["password1"];
    if (empty($password) || empty($password1)) {
        $kind = T_("ERROR");
        $msg = "You must fill out the form.";
    } elseif ($password != $password1) {
        $kind = T_("ERROR");
        $msg = "Password doesn't match.";
    } else {
	$n = get_row_count("users", "WHERE `id`=".intval($_POST["id"])." AND MD5(`secret`) = ".sqlesc($_POST["secret"]));
	if ($n != 1)
		show_error_msg(T_("ERROR"), T_("NO_SUCH_USER"));
        $newsec = sqlesc(mksecret());
        mysql_query("UPDATE `users` SET `password` = '".md5($password)."', `secret` = $newsec WHERE `id`=".intval($_POST['id'])." AND MD5(`secret`) = ".sqlesc($_POST["secret"])) or die(mysql_error());
        $kind = "Success";
        $msg = "Password changed.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_GET["take"] == 1) {
    $email = trim($_POST["email"]);

    if (!validemail($email)) {
        $msg = T_("EMAIL_ADDRESS_NOT_VAILD");
        $kind = T_("ERROR");
    }else{
        $res = mysql_query("SELECT id, username, email FROM users WHERE email=" . sqlesc($email) . " LIMIT 1");
        $arr = mysql_fetch_assoc($res);

        if (!$arr) {
            $msg = T_("EMAIL_ADDRESS_NOT_FOUND");
            $kind = T_("ERROR");
        }

        if ($arr) {
              $sec = mksecret();
            $secmd5 = md5($sec);
            $id = $arr['id'];

              $body = "Someone from " . $_SERVER["REMOTE_ADDR"] . ", hopefully you, requested that the account details for the account associated with this email address ($email) be mailed back. \r\n\r\n Here is the information we have on file for this account: \r\n\r\n User name: ".$arr["username"]." \r\n To change your password, you have to follow this link:\n\n$site_config[SITEURL]/account-recover.php?id=$id&secret=$secmd5\n\n\n".$site_config["SITENAME"]."\r\n";

            @sendmail($arr["email"], "Your account details", $body, "From: ".$site_config['SITEEMAIL'], "-f".$site_config['SITEEMAIL']);

              $res2 = mysql_query("UPDATE `users` SET `secret` = ".sqlesc($sec)." WHERE `email`= ". sqlesc($email) ." LIMIT 1");

              $msg = "The account details have been mailed to <b>". htmlspecialchars($email) ."</b>.<br />Please allow a few minutes for the mail to arrive.";

              $kind = "Success";
        }
    }
}

stdhead();

begin_frame(T_("RECOVER_ACCOUNT"), center);
if ($kind != "0") {
    show_error_msg("Notice", "$kind: $msg", 0);
}

if (is_valid_id($_GET["id"]) && strlen($_GET["secret"]) == 32) {?>
<form method=post action=account-recover.php>
<table border=0 cellspacing=0 cellpadding=5>
    <tr>
        <td class=rowhead>
            <b><?php echo T_("NEW_PASSWORD"); ?></B>:
        </td>
        <td>
            <input type=hidden name='secret' value='<?php echo $_GET['secret']; ?>'>
            <input type=hidden name='id' value='<?php echo $_GET['id']; ?>'>
            <input type=password size=40 name='password'>
        </td>
    </tr>
    <tr>
        <td class=rowhead>
            <b><?php echo T_("REPEAT"); ?></B>:
        </td>
        <td>
            <input type=password size=40 name='password1'>
        </td>
    </tr>
    <tr>
        <td class=rowhead>&nbsp;</td>
        <td><input type='submit' value='<?php echo T_("SUBMIT"); ?>'></td>
    </tr>
</table>
</form>
<?php } else { echo T_("USE_FORM_FOR_ACCOUNT_DETAILS"); ?>

<form method=post action='account-recover.php?take=1'>
    <table border=0 cellspacing=0 cellpadding=5>
        <tr>
            <td class=rowhead><b><?php echo "" .T_("EMAIL_ADDRESS"). "";?>:</B> </td>
            <td><input type=text size=40 name=email>&nbsp;<input type=submit value='<?php echo T_("SUBMIT");?>' class=btn></td>
        </tr>
    </table>
</form>

<?php
}
end_frame();
stdfoot();
?>