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

stdhead(T_("TEAMS")); 
begin_frame(T_("TEAMS")); 

echo "<br><b><CENTER>Please <a href=staff.php>contact</a> a member of staff if you would like a new team creating</CENTER></B><BR><BR>";


$query = "SELECT * FROM teams"; 
$sql = mysql_query($query); 

$numres = mysql_num_rows($sql);
if($numres == 0) {
	echo "<BR><b>No ".T_("TEAMS")."</b><BR>\n";
}else{
	while ($row = mysql_fetch_array($sql)) { 
		$image = $row['image']; 

		$OWNERNAME1 = mysql_query("SELECT id,username FROM users WHERE id=$row[owner]");
		$OWNERNAME2 = mysql_fetch_array($OWNERNAME1);

		echo("<table cellspacing=0 cellpadding=3 width=100% class=table_table>"); 
		echo("<td width=10% class=table_head><b>Group:</b></td><td width=90% class=table_head><b>Owner:</b> <a href=account-details.php?id=$OWNERNAME2[id]>$OWNERNAME2[username]</a>&nbsp;-&nbsp;<b>Info:</b></td>"); 
		echo "<tr><td width=10% class=table_col1><b><img src='".htmlspecialchars($image)."' border='0'></b></td><td valign=top class=table_col1 align=left width=90%>".format_comment($row["info"])."</td></tr><tr><td width=100% colspan=2 class=table_col2><b>Members:&nbsp;&nbsp;</b>";

		$res2 = mysql_query("SELECT id, username FROM users WHERE team = ".$row["id"]."") or die(mysql_error());
		while ($row2 = mysql_fetch_array($res2)) { 
			echo "<a href=account-details.php?id=".$row2["id"].">".$row2["username"]."</a>, ";
		}
		echo "</td></tr>"; 
		echo "</table><BR>";
	}
}
end_frame(); 
stdfoot();
?>