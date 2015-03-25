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
require_once("mailbox-functions.php");
dbconn(false);
loggedinonly();

$readme = add_get('read').'=';
$unread = false;

if (isset($_REQUEST['compose'])); // This blocks everything until done...

if (isset($_GET['inbox']))
{
 $pagename = T_("INBOX");
 $tablefmt = "&nbsp;,Sender,Subject,Date";
 $where = "`receiver` = $CURUSER[id] AND `location` IN ('in','both')";
 $type = "Mail";
}
elseif (isset($_GET['outbox']))
{
 $pagename = "Outbox";
 $tablefmt = "&nbsp;,Sent_to,Subject,Date";
 $where = "`sender` = $CURUSER[id] AND `location` IN ('out','both')";
 $type = "Mail";
}
elseif (isset($_GET['draft']))
{
 $pagename = "Draft";
 $tablefmt = "&nbsp;,Sent_to,Subject,Date";
 $where = "`sender` = $CURUSER[id] AND `location` = 'draft'";
 $type = "Mail";
}
elseif (isset($_GET['templates']))
{
 $pagename = "Templates";
 $tablefmt = "&nbsp;,Subject,Date";
 $where = "`sender` = $CURUSER[id] AND `location` = 'template'";
 $type = "Mail";
}
else
{
 $pagename = "Mail Overview";
 $type = "Overview";
}

//****** Send a message, or save after editing ******
if (isset($_POST['send']) || isset($_POST['draft']) || isset($_POST['template']))
{
 if (!isset($_POST['template']) && !isset($_POST['change']) && (!isset($_POST['userid']) || !is_valid_id($_POST['userid']))) $error = "Unknown recipient";
 else
 {
   $sendto = (@$_POST['template'] ? $CURUSER['id'] : @$_POST['userid']);
   if (isset($_POST['usetemplate']) && is_valid_id($_POST['usetemplate']))
   {
     $res = mysql_query("SELECT * FROM messages WHERE `id` = $_POST[usetemplate] AND `location` = 'template' LIMIT 1") or die(mysql_error());
     $arr = mysql_fetch_array($res);
     $subject = $arr['subject'].(@$_POST['oldsubject'] ? " (was ".$_POST['oldsubject'].")" : "");
     $msg = sqlesc($arr['msg']);
   } else {
     $subject = @$_POST['subject'];
     $msg = sqlesc(@$_POST['msg']);
   }
   if ($msg)
   {
     $subject = sqlesc($subject);
     if ((isset($_POST['draft']) || isset($_POST['template'])) && isset($_POST['msgid'])) mysql_query("UPDATE messages SET `subject` = $subject, `msg` = $msg WHERE `id` = $_POST[msgid] AND `sender` = $CURUSER[id]") or die("arghh");
     else
     {
       $to = (@$_POST['draft'] ? 'draft' : (@$_POST['template'] ? 'template' : (@$_POST['save'] ? 'both' : 'in')));
       $status = (@$_POST['send'] ? 'yes' : 'no');
       mysql_query("INSERT INTO `messages` (`sender`, `receiver`, `added`, `subject`, `msg`, `unread`, `location`) VALUES ('$CURUSER[id]', '$sendto', '".get_date_time()."', $subject, $msg, '$status', '$to')") or die("Aargh!");

	   // email notif
		$res = mysql_query("SELECT id, acceptpms, notifs, email FROM users WHERE id='$sendto'");
		$user = mysql_fetch_assoc($res);

		if (strpos($user['notifs'], '[pm]') !== false) {
			$cusername = $CURUSER["username"];

			$body = "You have received a PM from ".$cusername."\n\nYou can use the URL below to view the message (you may have to login).\n\n	".$site_config['SITEURL']."/mailbox.php\n\n".$site_config['SITENAME']."";
		
			sendmail($user["email"], "You have received a PM from $cusername", $body, "From: $site_config[SITEEMAIL]", "-f$site_config[SITEEMAIL]");
		}
	   //end email notif

       if (isset($_POST['msgid'])) mysql_query("DELETE FROM messages WHERE `location` = 'draft' AND `sender` = $CURUSER[id] AND `id` = $_POST[msgid]") or die("arghh");
     }
     if (isset($_POST['send'])) $info = "Message sent successfully".(@$_POST['save'] ? ", a copy has been saved in your Outbox" : "");
     else $info = "Message saved successfully";
   }
   else $error = "Unable to send message";
 }
}

//****** Delete a message ******
if (isset($_POST['remove']) && (isset($_POST['msgs']) || is_array($_POST['remove'])))
{
 if (is_array($_POST['remove'])) $tmp[] = key($_POST['remove']);
 else foreach($_POST['msgs'] as $key => $value) if (is_valid_id($key)) $tmp[] = $key;
 $msgs = implode(', ', $tmp);
 if ($msgs)
 {
   if (isset($_GET['inbox']))
   {
     mysql_query("DELETE FROM messages WHERE `location` = 'in' AND `receiver` = $CURUSER[id] AND `id` IN ($msgs)") or die("arghh");
     mysql_query("UPDATE messages SET `location` = 'out' WHERE `location` = 'both' AND `receiver` = $CURUSER[id] AND `id` IN ($msgs)") or die("arghh");
   } else {
     if (isset($_GET['outbox'])) mysql_query("UPDATE messages SET `location` = 'in' WHERE `location` = 'both' AND `sender` = $CURUSER[id] AND `id` IN ($msgs)") or die("arghh");
     mysql_query("DELETE FROM messages WHERE `location` IN ('out', 'draft', 'template') AND `sender` = $CURUSER[id] AND `id` IN ($msgs)") or die("arghh");
   }
   $info = count($tmp)." ".P_("message", count($tmp))." deleted";
 }
 else $error = "No messages to delete";
}

//****** Mark a message as read - only if you're the recipient ******
if (isset($_POST['mark']) && (isset($_POST['msgs']) || is_array($_POST['mark'])))
{
 if (is_array($_POST['mark'])) $tmp[] = key($_POST['mark']);
 else foreach($_POST['msgs'] as $key => $value) if (is_valid_id($key)) $tmp[] = $key;
 $msgs = implode(', ', $tmp);
 if ($msgs)
 {
   mysql_query("UPDATE messages SET `unread` = 'no' WHERE `id` IN ($msgs) AND `receiver` = $CURUSER[id]") or die("arghh");
   $info = count($tmp)." ".P_("message",  count($tmp))." marked as read";
 }
 else $error = "No messages marked as read";
}


stdhead($pagename, false);

?>

<script type="text/javascript">
function toggleChecked(state)
{
 var x=document.getElementsByTagName('input');
 for(var i=0;i<x.length;i++){
   if(x[i].type=='checkbox'){
     x[i].checked=state;
   }
 }
}

function toggleDisplay(id)
{
 var x=document.getElementById(id);
 if(x.style.display=='')x.style.display='none';
 else x.style.display='';
}

function toggleTemplate(x)
{
var y=true;
if(x.form.usetemplate.selectedIndex==0)y=false;
x.form.subject.disabled=y;
x.form.msg.disabled=y;
x.form.draft.disabled=y;
x.form.template.disabled=y;
}

function read(id)
{
var x=document.getElementById('msg_'+id);
var y=document.getElementById('img_'+id);
if(x.style.display==''){
 x.style.display='none';
 y.src='images/plus.gif';
}else{
 x.style.display='';
 y.src='images/minus.gif';
}
}

</script>
<?php

if (isset($_REQUEST['compose']))
{
 begin_frame("Compose");
 $userid = @$_REQUEST['id'];
 $subject = ''; $msg = ''; $to = ''; $hidden = ''; $output = ''; $reply = false;
 if (is_array($_REQUEST['compose'])) // In reply or followup to another msg
 {
   $msgid = key($_REQUEST['compose']);
   if (is_valid_id($msgid))
   {
     $res = mysql_query("SELECT * FROM `messages` WHERE `id` = $msgid AND '$CURUSER[id]' IN (`sender`,`receiver`) LIMIT 1") or die(mysql_error());
     if ($arr = mysql_fetch_assoc($res))
     {
       $subject = htmlspecialchars($arr['subject']);
       $msg .= htmlspecialchars($arr['msg']);
       if (current($_REQUEST['compose']) == 'Reply')
       {
         if ($arr['unread'] == 'yes' && $arr['receiver'] == $CURUSER['id']) mysql_query("UPDATE messages SET `unread` = 'no' WHERE `id` = $arr[id]") or die("arghh");
         $reply = true;
         $userid = $arr['sender'];
         if (substr($arr['subject'],0,4) != 'Re: ') $subject = "Re: $subject";
       }
       else $userid = $arr['receiver'];
       $hidden .= "<input type=\"hidden\" name=\"msgid\" value=\"$msgid\">";
     }
   }
 }
 if (isset($_GET['templates'])) $to = 'who cares';
 elseif (is_valid_id($userid))
 {
   $res = mysql_query("SELECT `username` FROM `users` WHERE `id` = $userid") or die(mysql_error());
   if (mysql_num_rows($res))
   {
     $to = mysql_result($res, 0);
     if ($reply) $msg = "\n\n-------- $to wrote: --------\n$msg";
     $hidden .= "<input type=\"hidden\" name=\"userid\" value=\"$userid\">";
     $to = "<b>$to</b>";
   }
 }
 else
 {
   $res = mysql_query("SELECT users.id, users.username FROM users WHERE users.privacy!='strong' AND users.class<'2' ORDER BY users.username");
   if (mysql_num_rows($res))
   {
     $to = "<select name=\"userid\">\n";
     while ($arr = mysql_fetch_assoc($res)) $to .= "<option value=\"$arr[id]\">$arr[username]</option>\n";
     $to .= "</select>\n";
   }
 }
 if (isset($_GET['id']) && !$to) print("".T_("INVALID_USER_ID")."");
 elseif (!isset($_GET['id']) && !$to) print("".T_("NO_FRIENDS")."");
 else
 {
	 /******** compose frame ********/

   begin_form(rem_get('compose'),'name=compose');
   if ($subject) $hidden .= "<input type=\"hidden\" name=\"oldsubject\" value=\"$subject\">";
		if ($hidden) print($hidden);
	echo "<table width=90% border=0>";
   if (!isset($_GET['templates'])){
     tr2("To:", $to, 1);
     $res = mysql_query("SELECT * FROM `messages` WHERE `sender` = $CURUSER[id] AND `location` = 'template' ORDER BY `subject`") or die(mysql_error());
     if (mysql_num_rows($res))
     {
       $tmp = "<select name=\"usetemplate\" onChange=\"toggleTemplate(this);\">\n<option name=\"0\">---</option>\n";
       while ($arr = mysql_fetch_assoc($res)) $tmp .= "<option value=\"$arr[id]\">$arr[subject]</option>\n";
       $tmp .= "</select><br>\n";
       tr2("Template:", $tmp, 1);
     }
   }
   tr2("Subject:", "<input name=\"subject\" type=\"text\" size=\"60\" value=\"$subject\">", 1);
//
//   tr2("Message","<textarea name=\"msg\" cols=\"50\" rows=\"15\">$msg</textarea>", 1);
require_once("backend/bbcode.php");
echo "</table>";
print ("".textbbcode("compose","msg","$msg")."");
echo "<table width=90% border=0>";

if (!isset($_GET['templates'])) $output .= "<input type=\"submit\" name=\"send\" value=\"Send\">&nbsp;<label><input type=\"checkbox\" name=\"save\" checked>Save Copy In Outbox</label>&nbsp;&nbsp;&nbsp;&nbsp;<input type=\"submit\" name=\"draft\" value=\"Save Draft\">&nbsp;";
   tr2($output."<input type=\"submit\" name=\"template\" value=\"Save Template\">");
   echo "</table>";
   end_form();
   end_frame();
   stdfoot();
   die;
 }
 end_frame();
}

begin_frame($pagename);

echo "<center>";
print(submenu('overview,inbox,outbox,compose,draft,templates','overview'));
echo "<hr><br>";


if ($type == "Overview")
{
 begin_table();
 $res = mysql_query("SELECT COUNT(*), COUNT(`unread` = 'yes') FROM messages WHERE `receiver` = $CURUSER[id] AND `location` IN ('in','both')") or die("barf!");
$res = mysql_query("SELECT COUNT(*) FROM messages WHERE receiver=" . $CURUSER["id"] . " AND `location` IN ('in','both')") or print(mysql_error());
 $inbox = mysql_result($res, 0);
   $res = mysql_query("SELECT COUNT(*) FROM messages WHERE `receiver` = " . $CURUSER["id"] . " AND `location` IN ('in','both') AND `unread` = 'yes'") or die("barf!");
   $unread = mysql_result($res, 0);
 $res = mysql_query("SELECT COUNT(*) FROM messages WHERE `sender` = " . $CURUSER["id"] . " AND `location` IN ('out','both')") or die("barf!");
 $outbox = mysql_result($res, 0);
 $res = mysql_query("SELECT COUNT(*) FROM messages WHERE `sender` = " . $CURUSER["id"] . " AND `location` = 'draft'") or die("barf!");
 $draft = mysql_result($res, 0);
 $res = mysql_query("SELECT COUNT(*) FROM messages WHERE `sender` = " . $CURUSER["id"] . " AND `location` = 'template'") or die("barf!");
 $template = mysql_result($res, 0);
 tr2('<a href="mailbox.php?inbox">'.T_("INBOX").' </a> ', " $inbox ".P_("message", $inbox)." ($unread ".T_("unread").")");
 tr2('<a href="mailbox.php?outbox">'.T_("OUTBOX").' </a> ', " $outbox ".P_("message", $outbox));
 tr2('<a href="mailbox.php?draft">'.T_("DRAFT").' </a> ', " $draft ".P_("message", $draft));
 tr2('<a href="mailbox.php?templates">'.T_("TEMPLATES").' </a> ', " $template ".P_("message", $template));
end_table();
echo"<br><BR>";
}
elseif ($type == "Mail")
{
 $order = order("added,sender,sendto,subject", "added", true);
 $res = mysql_query("SELECT COUNT(*) FROM messages WHERE $where") or sqlerr();
 $count = mysql_result($res, 0);
 list($pagertop, $pagerbottom, $limit) = pager2(20, $count);

 print($pagertop);
 begin_form();
 begin_table(0,"list");
 $table['&nbsp;']  = th("<input type=\"checkbox\" onClick=\"toggleChecked(this.checked);this.form.remove.disabled=true;\">", 1);
 $table['Sender']  = th_left("Sender",'sender');
 $table['Sent_to'] = th_left("Sent To",'sendto');
 $table['Subject'] = th_left("Subject",'subject');
 $table['Date']    = th_left("Date",'added');
 table($table, $tablefmt);
 
 $res = mysql_query("SELECT * FROM messages WHERE $where $order $limit") or sqlerr();
 while ($arr = mysql_fetch_assoc($res))
 {
   unset($table);
   $userid = 0;
   $format = '';
   $reading = false;

   if ($arr["sender"] == $CURUSER['id']) $sender = "Yourself";
   elseif (is_valid_id($arr["sender"]))
   {
     $res2 = mysql_query("SELECT username FROM users WHERE `id` = $arr[sender]") or die(mysql_error());
     $arr2 = mysql_fetch_assoc($res2);
     $sender = "<a href=\"account-details.php?id=$arr[sender]\">".($arr2["username"] ? $arr2["username"] : "[Deleted]")."</a>";
   }
   else $sender = "System";
//    $sender = $arr['sendername'];

   if ($arr["receiver"] == $CURUSER['id']) $sentto = "Yourself";
   elseif (is_valid_id($arr["receiver"]))
   {
     $res2 = mysql_query("SELECT username FROM users WHERE `id` = $arr[receiver]") or die(mysql_error());
     $arr2 = mysql_fetch_assoc($res2);
     $sentto = "<a href=\"account-details.php?id=$arr[receiver]\">".($arr2["username"] ? $arr2["username"] : "[Deleted]")."</a>";
   }
   else $sentto = "System";
 
   $subject = ($arr['subject'] ? htmlspecialchars($arr['subject']) : "no subject");
 
   if (@$_GET['read'] == $arr['id'])
   {
     $reading = true;
     if (isset($_GET['inbox']) && $arr["unread"] == "yes") mysql_query("UPDATE messages SET `unread` = 'no' WHERE `id` = $arr[id] AND `receiver` = $CURUSER[id]") or die("arghh");
   }
   if ($arr["unread"] == "yes")
   {
     $format = "font-weight:bold;";
     $unread = true;
   }
 
   $table['&nbsp;']  = td("<input type=\"checkbox\" name=\"msgs[$arr[id]]\" ".($reading ? "checked" : "")." onClick=\"this.form.remove.disabled=true;\">", 1);
   $table['Sender']  = td_left("$sender", 1, $format);
   $table['Sent_to'] = td_left("$sentto", 1, $format);
   $table['Subject'] = td_left("<a href=\"javascript:read($arr[id]);\"><img src=\"".$site_config["SITEURL"]."/images/plus.gif\" id=\"img_$arr[id]\" class=\"read\" border=0></a>&nbsp;<a href=\"javascript:read($arr[id]);\">$subject</span>", 1, $format);
   $table['Date']    = td_left(utc_to_tz($arr['added']), 1, $format);
 
   table($table, $tablefmt);
 
   $display = "<div>".format_comment($arr['msg'])."<br><br>";
   if (isset($_GET['inbox']) && is_valid_id($arr["sender"]))   $display .= "<input type=\"submit\" name=\"compose[$arr[id]]\" value=\"Reply\">&nbsp;\n";
   elseif (isset($_GET['draft']) || isset($_GET['templates'])) $display .= "<input type=\"submit\" name=\"compose[$arr[id]]\" value=\"Edit\">&nbsp;";
   if (isset($_GET['inbox']) && $arr['unread'] == 'yes') $display .= "<input type=\"submit\" name=\"mark[$arr[id]]\" value=\"Mark as Read\">&nbsp;\n";
   $display .= "<input type=\"submit\" name=\"remove[$arr[id]]\" value=\"Delete\">&nbsp;\n";
   $display .= "</div>";
   table(td_left($display, 1, "padding:0 6px 6px 6px"), $tablefmt, "id=\"msg_$arr[id]\" style=\"display:none;\"");
 }
 
// if ($count)
 //{
   $buttons = "<input type=\"button\" value=\"Delete Selected\" onClick=\"this.form.remove.disabled=!this.form.remove.disabled;\">";
   $buttons .= "<input type=\"submit\" name=\"remove\" value=\"...confirm\" disabled>";
   if (isset($_GET['inbox']) && $unread) $buttons .= "&nbsp;<input type=\"button\" value=\"Mark Selected as Read\" onClick=\"this.form.mark.disabled=!this.form.mark.disabled;\"><input type=\"submit\" name=\"mark\" value=\"...confirm\" disabled>";
   if (isset($_GET['templates'])) $buttons .= "&nbsp;<input type=\"submit\" name=\"compose\" value=\"Create New Template\">";
   table(td_left($buttons, 1, "border:0"), $tablefmt);
 //}
 end_table();
 end_form();
 print($pagerbottom);
}
end_frame();

stdfoot();
?>