<?php
//
//  TorrentTrader v2.x
//      $LastChangedDate: 2011-06-04 15:19:54 +0100 (Sat, 04 Jun 2011) $
//      $LastChangedBy: torrentialstorm $
//
//      http://www.torrenttrader.org
//
//
// Logout of site, clear cookie and return to index
require_once("backend/functions.php");
dbconn();
logoutcookie();
Header("Location: index.php");
?>