<?php
//USERS ONLINE
begin_block(T_("NEWEST_MEMBERS"), "left");

$expire = 600; // time in seconds
if (($rows = $TTCache->Get("newestmember_block", $expire)) === false) {
	$res = mysql_query("SELECT id, username FROM users WHERE status='confirmed' ORDER BY id DESC LIMIT 5") or die(mysql_error());
	$rows = array();

	while ($row = mysql_fetch_array($res))
		$rows[] = $row;

	$TTCache->Set("newestmember_block", $rows, $expire);
}

if (!$rows) {
	echo T_("NOTHING_FOUND");
} else {
	foreach ($rows as $row) {
		echo "<a href='account-details.php?id=$row[id]'>$row[username]</a><br>\n";
	}
}

end_block();
?>