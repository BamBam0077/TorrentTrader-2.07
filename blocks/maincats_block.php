<?php
if (!$site_config["MEMBERSONLY"] || $CURUSER) {
begin_block(T_("BROWSE_TORRENTS"));
	$catsquery = mysql_query("SELECT distinct parent_cat FROM categories ORDER BY parent_cat")or die(mysql_error());
	echo "- <a href=torrents.php>".T_("SHOW_ALL")."</a><BR>\n";
	while($catsrow = MYSQL_FETCH_ARRAY($catsquery)){
		echo "- <a href=torrents.php?parent_cat=".urlencode($catsrow['parent_cat']).">$catsrow[parent_cat]</a><BR>\n";
	}

end_block();
}
?>
