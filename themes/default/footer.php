</TD><!-- END MAIN CONTENT AREA -->

<?php if ($site_config["RIGHTNAV"]){ ?>
<!-- RIGHT COLUMN -->
<TD vAlign="top" width="170">
<?php rightblocks(); ?>
</TD>
<!-- END RIGHT COLUMN -->
<?php } ?>

</TR>
</TABLE>	

<BR><BR><BR>
<?php
//
// *************************************************************************************************************************************
//			PLEASE DO NOT REMOVE THE POWERED BY LINE, SHOW SOME SUPPORT! WE WILL NOT SUPPORT ANYONE WHO HAS THIS LINE EDITED OR REMOVED!
// *************************************************************************************************************************************
printf ("<CENTER><BR>".T_("POWERED_BY_TT")."<br>", $site_config["ttversion"]);
$totaltime = array_sum(explode(" ", microtime())) - $GLOBALS['tstart'];
printf(T_("PAGE_GENERATED_IN"), $totaltime);
print ("<br><a href=\"http://www.torrenttrader.org\" target=\"_blank\">www.torrenttrader.org</a><BR><a href=rss.php><img src=".$site_config["SITEURL"]."/images/icon_rss.gif border=0></a> <a href=rss.php>RSS Feed</a> - <a href=rss.php?custom=1>Feed Info</a></CENTER>");
//
// *************************************************************************************************************************************
//			PLEASE DO NOT REMOVE THE POWERED BY LINE, SHOW SOME SUPPORT! WE WILL NOT SUPPORT ANYONE WHO HAS THIS LINE EDITED OR REMOVED!
// *************************************************************************************************************************************

?>
<BR><BR>
</BODY>
</HTML>
<?php
ob_end_flush();
?>