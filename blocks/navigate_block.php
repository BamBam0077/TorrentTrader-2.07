<?php
begin_block(T_("NAVIGATION"));
echo "- <a href=index.php>".T_("HOME")."</a><BR>";

if ($CURUSER["view_torrents"]=="yes" || !$site_config["MEMBERSONLY"]) echo "- <a href=torrents.php>".T_("BROWSE_TORRENTS")."</a><BR>";    
if ($CURUSER["view_torrents"]=="yes" || !$site_config["MEMBERSONLY"]) echo "- <a href=torrents-today.php>".T_("TODAYS_TORRENTS")."</a><BR>";    
if ($CURUSER["view_torrents"]=="yes" || !$site_config["MEMBERSONLY"]) echo "- <a href=torrents-search.php>".T_("SEARCH")."</a><BR>";    
if ($CURUSER["view_torrents"]=="yes" || !$site_config["MEMBERSONLY"]) echo "- <a href=torrents-needseed.php>".T_("TORRENT_NEED_SEED")."</a><BR>";
if ($CURUSER["edit_torrents"]=="yes") echo "- <a href=torrents-import.php>".T_("MASS_TORRENT_IMPORT")."</a><BR>";
if ($CURUSER) echo "- <a href=teams-view.php>".T_("TEAMS")."</a><BR>";
echo "- <a href=rules.php>".T_("SITE_RULES")."</a><BR>";	
echo "- <a href=faq.php>".T_("FAQ")."</a><BR>";
end_block();
?>
