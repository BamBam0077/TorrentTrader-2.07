<?php
//
//  TorrentTrader v2.x
//	This file was last updated: 3/Sept/2007
//	
//	http://www.torrenttrader.org
//
//
require "backend/functions.php";
dbconn(false);

stdhead("" .T_("FAQ"). "");

$res = mysql_query("SELECT `id`, `question`, `flag` FROM `faq` WHERE `type`='categ' ORDER BY `order` ASC");
while ($arr = mysql_fetch_array($res, MYSQL_BOTH)) {
 $faq_categ[$arr[id]][title] = $arr[question];
 $faq_categ[$arr[id]][flag] = $arr[flag];
}

$res = mysql_query("SELECT `id`, `question`, `answer`, `flag`, `categ` FROM `faq` WHERE `type`='item' ORDER BY `order` ASC");
while ($arr = mysql_fetch_array($res, MYSQL_BOTH)) {
 $faq_categ[$arr[categ]][items][$arr[id]][question] = $arr[question];
 $faq_categ[$arr[categ]][items][$arr[id]][answer] = $arr[answer];
 $faq_categ[$arr[categ]][items][$arr[id]][flag] = $arr[flag];
}

if (isset($faq_categ)) {
// gather orphaned items
 foreach ($faq_categ as $id => $temp) {
  if (!array_key_exists("title", $faq_categ[$id])) {
   foreach ($faq_categ[$id][items] as $id2 => $temp) {
    $faq_orphaned[$id2][question] = $faq_categ[$id][items][$id2][question];
	$faq_orphaned[$id2][answer] = $faq_categ[$id][items][$id2][answer];
    $faq_orphaned[$id2][flag] = $faq_categ[$id][items][$id2][flag];
    unset($faq_categ[$id]);
   }
  }
 }

 begin_frame("" .T_("CONTENTS"). "");
 foreach ($faq_categ as $id => $temp) {
  if ($faq_categ[$id][flag] == "1") {
   //print("<ul>\n<li><a href=\"#". $id ."\"><b>". $faq_categ[$id][title] ."</b></a>\n<ul>\n");
   print("<ul>\n<li><a href=\"#". $id ."\"><b>". stripslashes($faq_categ[$id][title]) ."</b></a>\n<ul>\n");
   if (array_key_exists("items", $faq_categ[$id])) {
    foreach ($faq_categ[$id][items] as $id2 => $temp) {
	 if ($faq_categ[$id][items][$id2][flag] == "1") print("<li><a href=\"#". $id2 ."\" class=\"altlink\">". stripslashes($faq_categ[$id][items][$id2][question]) ."</a></li>\n");
	 elseif ($faq_categ[$id][items][$id2][flag] == "2") print("<li><a href=\"#". $id2 ."\" class=\"altlink\">". stripslashes($faq_categ[$id][items][$id2][question]) ."</a> <img src=\"".$site_config["SITEURL"]."/images/updated.png\" alt=\"Updated\" width=\"46\" height=\"13\" align=\"absbottom\"></li>\n");
	 elseif ($faq_categ[$id][items][$id2][flag] == "3") print("<li><a href=\"#". $id2 ."\" class=\"altlink\">". stripslashes($faq_categ[$id][items][$id2][question]) ."</a> <img src=\"".$site_config["SITEURL"]."/images/new.png\" alt=\"New\" width=\"25\" height=\"12\" align=\"absbottom\"></li>\n");
    }
   }
   print("</ul>\n</li>\n</ul>\n<br />\n");
  }
 }
 end_frame();

 foreach ($faq_categ as $id => $temp) {
  if ($faq_categ[$id][flag] == "1") {
   $frame = $faq_categ[$id][title] ." - <a href=\"#top\">Top</a>";
   begin_frame($frame);
   print("<a name=\"#". $id ."\" id=\"". $id ."\"></a>\n");
   if (array_key_exists("items", $faq_categ[$id])) {
    foreach ($faq_categ[$id][items] as $id2 => $temp) {
	 if ($faq_categ[$id][items][$id2][flag] != "0") {
      print("<br />\n<b>". stripslashes($faq_categ[$id][items][$id2][question]) ."</b><a name=\"#". $id2 ."\" id=\"". $id2 ."\"></a>\n<br />\n");
      print("<br />\n". stripslashes($faq_categ[$id][items][$id2][answer]) ."\n<br /><br />\n");
	 }
    }
   }
   end_frame();
  }
 }

}


stdfoot();
?>