<?php
begin_block(T_("SEARCH"));
?>
	<CENTER>
	<form method="get" action="torrents-search.php"><br />
	<input type="text" name="search" size="15" value="<?php echo htmlspecialchars($searchstr); ?>">
	<BR><BR>
	<input type="submit" value="<?php echo T_("SEARCH"); ?>" />
	</form>
	</CENTER>
	<?php
end_block();
?>
