<?php
//
//  TorrentTrader v2.x
//  Languages
//  Author: TorrentialStorm
//
//    $LastChangedDate: 2011-06-04 15:19:54 +0100 (Sat, 04 Jun 2011) $
//
//    http://www.torrenttrader.org
//
//


// Plural forms: http://www.gnu.org/software/hello/manual/gettext/Plural-forms.html
// $LANG["PLURAL_FORMS"] is in the plural= format

function T_ ($s) {
	GLOBAL $LANG;
	

	if ($ret = $LANG[$s]) {
		//return "TRANSLATED";
		return $ret;
	}

	if ($ret = $LANG["{$s}[0]"]) {
		//return "TRANSLATED";
		return $ret;
	}


	return $s;
}

function P_ ($s, $num) {
	GLOBAL $LANG;

	$num = (int) $num;

	$plural = str_replace("n", $num, $LANG["PLURAL_FORMS"]);
	$i = eval("return intval($plural);");

	if ($ret = $LANG["{$s}[$i]"]) {
		//return "TRANSLATED";
		return $ret;
	}

	return $s;
}

$LANG = array();

require("languages/english.php");

?>