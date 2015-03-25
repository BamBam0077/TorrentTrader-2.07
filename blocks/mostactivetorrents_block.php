<?php
if (!$site_config["MEMBERSONLY"] || $CURUSER) {
	begin_block(T_("MOST_ACTIVE"));

	$where = "";
	//uncomment the following line to exclude external torrents
	//$where = "WHERE external !='yes'"  


	$limit = 10; // # of torrents to show
	$expires = 600; // Cache time in seconds
	if (($rows = $TTCache->Get("mostactivetorrents_block", $expires)) === false) {
		$res = mysql_query("SELECT id,name,seeders,leechers FROM torrents $where ORDER BY seeders + leechers DESC, seeders DESC, added ASC LIMIT $limit");

		$rows = array();
		while ($row = mysql_fetch_array($res))
			$rows[] = $row;

		$TTCache->Set("mostactivetorrents_block", $rows, $expires);
	}

	if ($rows) {
		foreach ($rows as $row) { 
				$char1 = 18; //cut length 
				$smallname = htmlspecialchars(CutName($row["name"], $char1));
				echo "<a href='torrents-details.php?id=$row[id]' title='".htmlspecialchars($row["name"])."'>$smallname</A><BR> - [S: ".$row["seeders"]." - L: ".$row["leechers"]."]<BR><BR>\n";
		}

} else {
	print("<CENTER>".T_("NOTHING_FOUND")."</CENTER>\n");
}
end_block();
}
?>