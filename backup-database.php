<?php
//
//  TorrentTrader v2.x
//	This file was last updated: 22/Aug/2007
//	
//	http://www.torrenttrader.org
//
//

$backupdir = '../backups'; //Ensure this folder exists and is chmod 777



require_once("backend/mysql.php");
$host= $mysql_host;       
$user= $mysql_user;             
$pass= $mysql_pass;
$db= $mysql_db;

 $today = getdate();
 $day = $today[mday];
 if ($day < 10) {
    $day = "0$day";
 }
 $month = $today[mon];
 if ($month < 10) {
    $month = "0$month";
 }
 $year = $today[year];
 $hour = $today[hours];
 $min = $today[minutes];
 $sec = "00";

 // Execute mysqldump command.
 // It will produce a file named $db-$year$month$day-$hour$min.sql.gz
 // under $backupdir
 system(sprintf(
 //'mysqldump --opt -h %s -u %s -p%s %s > %s/%s/%s-%s-%s-%s.sql',    
 'mysqldump --opt -h %s -u %s -p%s %s | gzip > %s/%s-%s-%s-%s.sql.gz',                                    
  
  $host,
  $user,
  $pass,
  $db,
  $backupdir,
  $db,
  $day,
  $month,
  $year
 )); 


$name = $db."-".$day."-".$month."-".$year.".sql.gz";
$date = date("Y-m-d");
$day = date("d");
?>
