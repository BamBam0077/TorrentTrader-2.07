<?php
if ($CURUSER){
	begin_block(T_("THEME")." / ".T_("LANGUAGE"));

	$ss_r = mysql_query("SELECT * from stylesheets") or die;
	$ss_sa = array();

	while ($ss_a = mysql_fetch_array($ss_r)){
		$ss_id = $ss_a["id"];
		$ss_name = $ss_a["name"];
		$ss_sa[$ss_name] = $ss_id;
	}

	ksort($ss_sa);
	reset($ss_sa);

	while (list($ss_name, $ss_id) = each($ss_sa)){
		if ($ss_id == $CURUSER["stylesheet"]) $ss = " selected"; else $ss = "";
		$stylesheets .= "<option value=$ss_id$ss>$ss_name</option>\n";
	}

	$lang_r = mysql_query("SELECT * from languages") or die;
	$lang_sa = array();

	while ($lang_a = mysql_fetch_array($lang_r)){
		$lang_id = $lang_a["id"];
		$lang_name = $lang_a["name"];
		$lang_sa[$lang_name] = $lang_id;
	}

	ksort($lang_sa);
	reset($lang_sa);

	while (list($lang_name, $lang_id) = each($lang_sa)){
		if ($lang_id == $CURUSER["language"]) $lang = " selected"; else $lang = "";
		$languages .= "<option value=$lang_id$lang>$lang_name</option>\n";
	}

?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
<td>
<table width="100%" border="0" cellspacing="0" cellpadding="5">
<form method="post" action="take-theme.php">
  <tr>
<td align="center" valign="middle"><nobr><b><?php echo T_("THEME"); ?> </B>
<select name=stylesheet style="font-family: Verdana; font-size: 8pt; color: #000000; border: 1px solid #808080; background-color: #C0C0C0" size="1"><?php echo $stylesheets?></select></nobr></td>
  </tr>
  <tr>
<td align="center" valign="middle"><nobr><b><?php echo T_("LANGUAGE"); ?> </B>
<select name=language style="font-family: Verdana; font-size: 8pt; color: #000000; border: 1px solid #808080; background-color: #C0C0C0" size="1"><?php echo $languages?></select></nobr></td>
  </tr>
  <tr>
<td align="center" valign="middle"><input type="submit" value="<?php echo T_("APPLY"); ?>" style="font-family: Verdana; font-size: 8pt; color: #000000; border: 1px solid #808080; background-color: #C0C0C0"></td>
  </form>
  </tr>
</table>
</td>
  </tr>
</table>
<?php
end_block();
}
?>
