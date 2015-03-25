<?php
begin_block(T_("ONLINE_USERS"));

$expires = 600; // Cache time in seconds

if (($rows = $TTCache->Get("usersonline_block", $expires)) === false) {
	$res = mysql_query("SELECT id, username FROM users WHERE privacy !='strong' AND UNIX_TIMESTAMP('".get_date_time()."') - UNIX_TIMESTAMP(users.last_access) <= 900");

	$rows = array();
	while ($row = mysql_fetch_array($res)) {
		$rows[] = $row;
	}

	$TTCache->Set("usersonline_block", $rows, $expires);
}

if (!$rows) {
	echo T_("NO_USERS_ONLINE");
} else {
	for ($i = 0, $cnt = count($rows), $n = $cnt - 1; $i < $cnt; $i++) {
		$row = &$rows[$i];
		echo "<a href='account-details.php?id=$row[id]'>$row[username]</a>".($i < $n ? ", " : "")."\n";;
	}
}

end_block();
?>
