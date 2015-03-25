<?php
if (!$site_config["MEMBERSONLY"] || $CURUSER) {
	begin_block(T_("LATEST_TORRENTS"));

	$expire = 900; // time in seconds

	if (($latestuploadsrecords = $GLOBALS["TTCache"]->Get("latestuploadsblock", $expire)) === false) {
		$latestuploadsquery = mysql_query("SELECT id, name, size, seeders, leechers FROM torrents WHERE banned='no' ORDER BY id DESC LIMIT 5");

		$latestuploadsrecords = array();
		while ($latestuploadsrecord = mysql_fetch_array($latestuploadsquery))
			$latestuploadsrecords[] = $latestuploadsrecord;
		$GLOBALS["TTCache"]->set("latestuploadsblock", $latestuploadsrecords, $expire);
	}

	if ($latestuploadsrecords) {
		foreach ($latestuploadsrecords as $row) { 
			$char1 = 18; //cut length 
			$smallname = htmlspecialchars(CutName($row["name"], $char1));
			echo "<a href='torrents-details.php?id=$row[id]' title='".htmlspecialchars($row["name"])."'>$smallname</a><BR>\n";
			echo "- [".T_("SIZE").": ".mksize($row["size"])."]<BR><BR>\n";
		}
	} else {
		print("<CENTER>".T_("NOTHING_FOUND")."</CENTER>\n");
	}
	end_block();
}
?>