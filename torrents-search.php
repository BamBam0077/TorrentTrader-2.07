<?php
//
//  TorrentTrader v2.x
//      $LastChangedDate: 2011-06-04 15:19:54 +0100 (Sat, 04 Jun 2011) $
//      $LastChangedBy: torrentialstorm $
//
//      http://www.torrenttrader.org
//
//
require_once("backend/functions.php");
dbconn();

//check permissions
if ($site_config["MEMBERSONLY"]){
	loggedinonly();

	if($CURUSER["view_torrents"]=="no")
		show_error_msg(T_("ERROR"), T_("NO_TORRENT_VIEW"), 1);
}

function sqlwildcardesc($x){
    return str_replace(array("%","_"), array("\\%","\\_"), mysql_real_escape_string($x));
}

//GET SEARCH STRING
$searchstr = trim($_GET["search"]);
$cleansearchstr = searchfield($searchstr);
if (empty($cleansearchstr))
unset($cleansearchstr);

$thisurl = "torrents-search.php?";


$addparam = "";
$wherea = array();
$wherecatina = array();
$wherea[] = "banned = 'no'";

$wherecatina = array();
$wherecatin = "";
$res = mysql_query("SELECT id FROM categories");
while($row = mysql_fetch_assoc($res)){
    if ($_GET["c$row[id]"]) {
        $wherecatina[] = $row[id];
        $addparam .= "c$row[id]=1&amp;";
        $addparam .= "c$row[id]=1&amp;";
        $thisurl .= "c$row[id]=1&amp;";
    }
    $wherecatin = implode(", ", $wherecatina);
}
if ($wherecatin)
    $wherea[] = "category IN ($wherecatin)";


//include dead
if ($_GET["incldead"] == 1) {
	$addparam .= "incldead=1&amp;";
	$thisurl .= "incldead=1&";
}elseif ($_GET["incldead"] == 2){
	$wherea[] = "visible = 'no'";
	$addparam .= "incldead=2&amp;";
	$thisurl .= "incldead=2&";
}else
	$wherea[] = "visible = 'yes'";

// Include freeleech
if ($_GET["freeleech"] == 1) {
	$addparam .= "freeleech=1&amp;";
	$thisurl .= "freeleech=1&amp;";
	$wherea[] = "freeleech = '0'";
} elseif ($_GET["freeleech"] == 2) {
	$addparam .= "freeleech=2&amp;";
	$thisurl .= "freeleech=2&amp;";
	$wherea[] = "freeleech = '1'";
}



//include external
if ($_GET["inclexternal"] == 1) {
	$addparam .= "inclexternal=1&amp;";
	$wherea[] = "external = 'no'";
}

if ($_GET["inclexternal"] == 2) {
	$addparam .= "inclexternal=2&amp;";
	$wherea[] = "external = 'yes'";
}

//cat
if ($_GET["cat"]) { 
        $wherea[] = "category = " . sqlesc($_GET["cat"]);
		$wherecatina[] = sqlesc($_GET["cat"]);
        $addparam .= "cat=" . urlencode($_GET["cat"]) . "&amp;";
	$thisurl .= "cat=".urlencode($_GET["cat"])."&";
}

//language
if ($_GET["lang"]) {
    $wherea[] = "torrentlang = " . sqlesc($_GET["lang"]);
    $addparam .= "lang=" . urlencode($_GET["lang"]) . "&amp;";
    $thisurl .= "lang=".urlencode($_GET["lang"])."&";
}

//parent cat
if ($_GET["parent_cat"]) {
	$addparam .= "parent_cat=" . urlencode($_GET["parent_cat"]) . "&amp;";
	$thisurl .= "parent_cat=".urlencode($_GET["parent_cat"])."&";
}

$parent_cat = $_GET["parent_cat"];

$wherebase = $wherea;

if (isset($cleansearchstr)) {
	$wherea[] = "MATCH (torrents.name) AGAINST ('".mysql_real_escape_string($searchstr)."' IN BOOLEAN MODE)";

	$addparam .= "search=" . urlencode($searchstr) . "&amp;";
	$thisurl .= "search=".urlencode($searchstr)."&";
}

//order by
if ($_GET['sort'] && $_GET['order']) {
	$column = '';
	$ascdesc = '';
	switch($_GET['sort']) {
		case 'id': $column = "id"; break;
		case 'name': $column = "name"; break;
		case 'comments': $column = "comments"; break;
		case 'size': $column = "size"; break;
		case 'times_completed': $column = "times_completed"; break;
		case 'seeders': $column = "seeders"; break;
		case 'leechers': $column = "leechers"; break;
		case 'category': $column = "category"; break;
		default: $column = "id"; break;
	}

	switch($_GET['order']) {
		case 'asc': $ascdesc = "ASC"; break;
		case 'desc': $ascdesc = "DESC"; break;
		default: $ascdesc = "DESC"; break;
	}
} else {
	$_GET["sort"] = "id";
	$_GET["order"] = "desc";
	$column = "id";
	$ascdesc = "DESC";
}

	$orderby = "ORDER BY torrents." . $column . " " . $ascdesc;
	$pagerlink = "sort=" . $_GET['sort'] . "&order=" . $_GET['order'] . "&";

if (is_valid_id($_GET["page"]))
	$thisurl .= "page=$_GET[page]&";


$where = implode(" AND ", $wherea);

if ($where != "")
	$where = "WHERE $where";

$parent_check = "";
if ($parent_cat){
	$parent_check = " AND categories.parent_cat=".sqlesc($parent_cat);
}


//GET NUMBER FOUND FOR PAGER
$res = mysql_query("SELECT COUNT(*) FROM torrents $where $parent_check") or die(mysql_error());
$row = mysql_fetch_array($res);
$count = $row[0];


if (!$count && isset($cleansearchstr)) {
	$wherea = $wherebase;
	$searcha = explode(" ", $cleansearchstr);
	$sc = 0;
	foreach ($searcha as $searchss) {
		if (strlen($searchss) <= 1)
		continue;
		$sc++;
		if ($sc > 5)
		break;
		$ssa = array();
		foreach (array("torrents.name") as $sss)
		$ssa[] = "$sss LIKE '%" . sqlwildcardesc($searchss) . "%'";
		$wherea[] = "(" . implode(" OR ", $ssa) . ")";
	}
	if ($sc) {
		$where = implode(" AND ", $wherea);
		if ($where != "")
		$where = "WHERE $where";
		$res = mysql_query("SELECT COUNT(*) FROM torrents $where $parent_check");
		$row = mysql_fetch_array($res);
		$count = $row[0];
	}
}

//Sort by
if ($addparam != "") { 
	if ($pagerlink != "") {
		if ($addparam{strlen($addparam)-1} != ";") { // & = &amp;
			$addparam = $addparam . "&" . $pagerlink;
		} else {
			$addparam = $addparam . $pagerlink;
		}
	}
} else {
	$addparam = $pagerlink;
}



if ($count) {

	//SEARCH QUERIES! 
	list($pagertop, $pagerbottom, $limit) = pager(20, $count, "torrents-search.php?" . $addparam);
	$query = "SELECT torrents.id, torrents.anon, torrents.announce, torrents.category, torrents.leechers, torrents.nfo, torrents.seeders, torrents.name, torrents.times_completed, torrents.size, torrents.added, torrents.comments, torrents.numfiles, torrents.filename, torrents.owner, torrents.external, torrents.freeleech, categories.name AS cat_name, categories.parent_cat AS cat_parent, categories.image AS cat_pic, users.username, users.privacy, IF(torrents.numratings < 2, NULL, ROUND(torrents.ratingsum / torrents.numratings, 1)) AS rating FROM torrents LEFT JOIN categories ON category = categories.id LEFT JOIN users ON torrents.owner = users.id $where $parent_check $orderby $limit";
	$res = mysql_query($query) or die(mysql_error());

	}else{
		unset($res);
}

if (isset($cleansearchstr))
	stdhead(T_("SEARCH_RESULTS_FOR")." \"" . htmlspecialchars($searchstr) . "\"");
else
	stdhead(T_("BROWSE_TORRENTS"));

begin_frame(T_("SEARCH_TORRENTS"));

// get all parent cats
echo "<CENTER><b>".T_("CATEGORIES").":</B> ";
$catsquery = mysql_query("SELECT distinct parent_cat FROM categories ORDER BY parent_cat")or die(mysql_error());
echo " - <a href=torrents.php>".T_("SHOWALL")."</a>";
while($catsrow = MYSQL_FETCH_ARRAY($catsquery)){
		echo " - <a href=torrents.php?parent_cat=".urlencode($catsrow['parent_cat']).">$catsrow[parent_cat]</a>";
}

?>
<BR><BR>
<form method="get" action="torrents-search.php">
<table class=bottom align="center">
<tr align='right'>
<?php
$i = 0;
$cats = mysql_query("SELECT * FROM categories ORDER BY parent_cat, name");
while ($cat = mysql_fetch_assoc($cats)) {
    $catsperrow = 5;
    print(($i && $i % $catsperrow == 0) ? "</tr><tr align='right'>" : "");
    print("<td style=\"padding-bottom: 2px;padding-left: 2px\"><a class=catlink href=torrents.php?cat={$cat["id"]}>".htmlspecialchars($cat["parent_cat"])." - " . htmlspecialchars($cat["name"]) . "</a><input name=c{$cat["id"]} type=\"checkbox\" " . (in_array($cat["id"], $wherecatina) ? "checked " : "") . "value=1></td>\n");
    $i++;
}
echo "</tr></table>";

//if we are browsing, display all subcats that are in same cat
if ($parent_cat){
	echo "<BR><BR><b>You are in:</b> <a href=torrents.php?parent_cat=$parent_cat>$parent_cat</a><BR><b>Sub Categories:</B> ";
	$subcatsquery = mysql_query("SELECT id, name, parent_cat FROM categories WHERE parent_cat='$parent_cat' ORDER BY name")or die(mysql_error());
	while($subcatsrow = MYSQL_FETCH_ARRAY($subcatsquery)){
		$name = $subcatsrow['name'];
		echo " - <a href=torrents.php?cat=$subcatsrow[id]>$name</a>";
	}
}	

echo "</CENTER><BR><BR>";//some spacing

?>
	<CENTER>
	<?php print(T_("SEARCH")); ?>
	<input type="text" name="search" size="40" value="<?php echo  stripslashes(htmlspecialchars($searchstr)) ?>" />
	<?php print(T_("IN")); ?>
	<select name="cat">
	<option value="0"><?php echo "(".T_("ALL")." ".T_("TYPES").")";?></option>
	<?php


	$cats = genrelist();
	$catdropdown = "";
	foreach ($cats as $cat) {
		$catdropdown .= "<option value=\"" . $cat["id"] . "\"";
		if ($cat["id"] == $_GET["cat"])
			$catdropdown .= " selected=\"selected\"";
		$catdropdown .= ">" . htmlspecialchars($cat["parent_cat"]) . ": " . htmlspecialchars($cat["name"]) . "</option>\n";
	}	
	?>
	<?php echo  $catdropdown ?>
	</select>

	<BR><BR>
	<select name="incldead">
 	<option value="0"><?php echo T_("ACTIVE_TRANSFERS"); ?></option>
	<option value="1" <?php if ($_GET["incldead"] == 1) echo "selected"; ?>><?php echo T_("INC_DEAD");?></option>
	<option value="2" <?php if ($_GET["incldead"] == 2) echo "selected"; ?>><?php echo T_("ONLY_DEAD");?></option>
	</select>
	<select name="freeleech">
	<option value="0">Any</option>
	<option value="1" <?php if ($_GET["freeleech"] == 1) echo "selected"; ?>><?php echo T_("NOT_FREELEECH");?></option>
	<option value="2" <?php if ($_GET["freeleech"] == 2) echo "selected"; ?>><?php echo T_("ONLY_FREELEECH");?></option>
 	</select>

	<?php if ($site_config["ALLOWEXTERNAL"]){?>
		<select name="inclexternal">
 		<option value="0"><?php echo T_("LOCAL_EXTERNAL"); ?></option>
		<option value="1" <?php if ($_GET["inclexternal"] == 1) echo "selected"; ?>><?php echo T_("LOCAL_ONLY");?></option>
		<option value="2" <?php if ($_GET["inclexternal"] == 2) echo "selected"; ?>><?php echo T_("EXTERNAL_ONLY");?></option>
 		</select>
	<?php } ?>

	<select name="lang">
	<option value="0"><?php echo "(".T_("ALL").")";?></option>
	<?php
	$lang = langlist();
	$langdropdown = "";
	foreach ($lang as $lang) {
		$langdropdown .= "<option value=\"" . $lang["id"] . "\"";
		if ($lang["id"] == $_GET["lang"])
			$langdropdown .= " selected=\"selected\"";
		$langdropdown .= ">" . htmlspecialchars($lang["name"]) . "</option>\n";
	}
	
	?>
	<?php echo  $langdropdown ?>
	</select>
	<input type="submit" value="<?php print T_("SEARCH"); ?>" />
	<br>
	</form>
	</CENTER><CENTER><?php print T_("SEARCH_RULES"); ?></CENTER><BR>
<?php

if ($count) {
// New code (TorrentialStorm)
	echo "<div align=right><form id='sort'>".T_("SORT_BY").": <select name='sort' onChange='window.location=\"{$thisurl}sort=\"+this.options[this.selectedIndex].value+\"&order=\"+document.forms[\"sort\"].order.options[document.forms[\"sort\"].order.selectedIndex].value' style=\"font-family: Verdana; font-size: 8pt; border: 1px solid #000000; background-color: #CCCCCC\" size=\"1\">";
	echo "<option value='id'" . ($_GET["sort"] == "id" ? "selected" : "") . ">".T_("ADDED")."</option>";
	echo "<option value='name'" . ($_GET["sort"] == "name" ? "selected" : "") . ">".T_("NAME")."</option>";
	echo "<option value='comments'" . ($_GET["sort"] == "comments" ? "selected" : "") . ">".T_("COMMENTS")."</option>";
	echo "<option value='size'" . ($_GET["sort"] == "size" ? "selected" : "") . ">".T_("SIZE")."</option>";
	echo "<option value='times_completed'" . ($_GET["sort"] == "times_completed" ? "selected" : "") . ">".T_("COMPLETED")."</option>";
	echo "<option value='seeders'" . ($_GET["sort"] == "seeders" ? "selected" : "") . ">".T_("SEEDERS")."</option>";
	echo "<option value='leechers'" . ($_GET["sort"] == "leechers" ? "selected" : "") . ">".T_("LEECHERS")."</option>";
	echo "</select>&nbsp;";
	echo "<select name='order' onChange='window.location=\"{$thisurl}order=\"+this.options[this.selectedIndex].value+\"&sort=\"+document.forms[\"sort\"].sort.options[document.forms[\"sort\"].sort.selectedIndex].value' style=\"font-family: Verdana; font-size: 8pt; border: 1px solid #000000; background-color: #CCCCCC\" size=\"1\">";
	echo "<option selected value='asc'" . ($_GET["order"] == "asc" ? "selected" : "") . ">".T_("ASCEND")."</option>";
	echo "<option value='desc'" . ($_GET["order"] == "desc" ? "selected" : "") . ">".T_("DESCEND")."</option>";
	echo "</select>";
	echo "</form></div>";

// End

	torrenttable($res);
	print($pagerbottom);
}else {
	show_error_msg(T_("NOTHING_FOUND"), T_("NO_RESULTS"), 0);
}

if ($CURUSER)
	mysql_query("UPDATE users SET last_browse=".gmtime()." WHERE id=$CURUSER[id]");


end_frame();
stdfoot();

?>
