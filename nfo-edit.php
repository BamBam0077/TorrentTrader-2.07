<?php
//
//  TorrentTrader v2.x
//      $LastChangedDate: 2011-06-04 15:19:54 +0100 (Sat, 04 Jun 2011) $
//      $LastChangedBy: torrentialstorm $
//
//      http://www.torrenttrader.org
//
//
error_reporting(0);

require_once("backend/functions.php");
dbconn();
loggedinonly();

// check access and rights
if($CURUSER["edit_torrents"]=="no")
	show_error_msg(T_("ERROR"), "You do not have permission to Edit nfo's", 1);

$nfolocation = $site_config["nfo_dir"]."/$id.nfo";


if($do == "save_nfo"){
    $nfo = fopen("$nfolocation", "w");
    $nfoupdated = fwrite($nfo,$nfocontents);
    fclose($nfo);
    if($nfoupdated){
        show_error_msg("Success", "NFO Updated OK", 1);
        write_log("NFO of $type $id was edited by $CURUSER[username]");
    }
}

if($do == "del_nfo"){      
    $queryCheck = mysql_query("SELECT nfo FROM torrents WHERE nfo='yes' AND id='$id' LIMIT 1");
    $resultCheck = mysql_num_rows($queryCheck);
    if ($resultCheck == 0)
        $message = "There is no NFO available to delete for ID $id."; 

    if(!$message){
        @unlink($nfolocation);
        @mysql_query("UPDATE torrents SET nfo='no' WHERE id='$id' LIMIT 1");
        show_error_msg("Success", "NFO Deleted OK", 1);
        write_log("NFO $id was deleted by $CURUSER[username] ($reason)");
    }
}

if(!$do){

	$id = (int)$_GET["id"];

	if (!$id)
		show_error_msg(T_("ID_NOT_FOUND"), T_("ID_NOT_FOUND_MSG_EDIT"), 1);

	$filegetcontents = file_get_contents($nfolocation);
	$nfo = htmlspecialchars($filegetcontents);

	if (!$nfo){
		show_error_msg(T_("ERROR"), "No NFO!",1);
	}

    stdhead(T_("NFO_EDITOR"));  
    begin_frame(T_("NFO_EDITOR"));
    echo "<br><CENTER><form action='$PHP_SELF' method='post'>\n";
	echo "<input type='hidden' name='id' value='$id'>\n";
	echo "<input type='hidden' name='do' value='save_nfo'>\n";
	echo "<textarea name='nfocontents' cols='80' rows='20' style='border:1px black solid;background:#eeeeee;font-family:verdana,arial; font-size: 12px; color:#000000;'>\n";
	echo "".stripslashes($nfo)."";
	echo "</textarea>\n<p>\n";
	echo "<input style='background:#eeeeee' type='submit' value='   Save   '>\n";
	echo "<input style='background:#eeeeee' type='reset' value='  Reset   '>\n";
	echo "</form></CENTER>\n";
    end_frame();
    
    begin_frame(T_("NFO_DELETE"));
	echo "<CENTER><form action='$PHP_SELF' method='post'>\n";
	echo "<input type='hidden' name='id' value='$id'>\n";
    echo "<input type='hidden' name='do' value='del_nfo'>\n";
  	echo "Reason for deletion: <input type=text size=40 name=reason> <input type=submit value='Delete it!' style='height: 25px'>\n";
	echo "</form></CENTER>\n";
    end_frame();
	
}

stdfoot();
?>