<?php
if (!$site_config["MEMBERSONLY"] || $CURUSER) {
	begin_block(T_("SEEDERS_WANTED"));

	$external = "external = 'no'";
	// Uncomment below to include external torrents
	$external = 1;

	$expires = 600; // Cache time in seconds
	if (($rows = $TTCache->Get("seedwanted_block", $expires)) === false) {
		$res = mysql_query("SELECT id,name,seeders,leechers FROM torrents WHERE seeders = 0 AND leechers > 0 AND $external ORDER BY leechers DESC LIMIT 5");
		$rows = array();

		while ($row = mysql_fetch_array($res)) {
			$rows[] = $row;
		}

		$TTCache->Set("seedwanted_block", $rows, $expires);
	}


	if (!$rows) {
		echo "<BR>".T_("NOTHING_FOUND")."<BR>";
	} else {
		foreach ($rows as $row) { 
			$char1 = 18; //cut length 
			$smallname = htmlspecialchars(CutName($row["name"], $char1));
			echo "<a href='torrents-details.php?id=$row[id]' title='".htmlspecialchars($row["name"])."'>$smallname</A><BR> - [".T_("LEECHERS").": $row[leechers]]<BR><BR>\n";
		}
	}
	end_block();
}
?>