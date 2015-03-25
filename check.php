<?php
//
//  TorrentTrader v2.x
//      $LastChangedDate: 2011-06-04 15:19:54 +0100 (Sat, 04 Jun 2011) $
//      $LastChangedBy: torrentialstorm $
//
//      http://www.torrenttrader.org
//
//
error_reporting(E_ALL ^ E_NOTICE);

if ($_GET["phpinfo"] == 1){
    echo "<BR><CENTER><a href=check.php>Back To Check</a></CENTER><BR><BR>";
    phpinfo();
    die();
}
    
function get_php_setting($val) {
    $r =  (ini_get($val) == '1' ? 1 : 0);
    return $r ? 'ON' : 'OFF';
}

function writableCell( $folder, $relative=1, $text='' ) {
    $writeable         = '<b><font color="green">Writeable</font></b>';
    $unwriteable     = '<b><font color="red">Unwriteable</font></b>';
    
    echo '<tr>';
    echo '<td>' . $folder . '/</td>';
    echo '<td align="right">';
    if ( $relative ) {
        echo is_writable( "./$folder" )     ? $writeable : $unwriteable;
    } else {
        echo is_writable( "$folder" )         ? $writeable : $unwriteable;
    }
    echo '</tr>';
}


view();


function view() {    
?>
<html><head><title>TorrentTrader Check</title></head>
<body>
<font face="arial">
<CENTER><BR><font face=arial size=2><b>TorrentTrader v2 Config Check<br>v1.7 - TorrentialStorm<br><br></b></CENTER>

<CENTER><input type="button" class="button" value="Check Again" onclick="window.location=window.location" />

</CENTER><BR>
<CENTER><a href=check.php?phpinfo=1>PHPInfo</a></CENTER><BR>

<b>Required Settings Check:</B><BR>
If any of these items are highlighted in red then please take actions to correct them. <BR>
Failure to do so could lead to your TorrentTrader! installation not functioning correctly.<BR>
<BR>
This system check is designed for unix based servers, windows based servers may not give desired results<BR>
<BR>
<BR>    
<table cellpadding=3 cellspacing=1 style='border-collapse: collapse' border=1>
<tr>
<td>PHP version >= 4.3.0</td>
<td align="left">
                                <?php
                  define(_PHP_VERSION, phpversion());
                    echo phpversion() < '4.3' ? '<b><font color="red">No</font> 4.3 or above required</b>' : '<b><font color="green">Yes</font></b>';
                    echo " - Your PHP version is " . _PHP_VERSION ."";                    
                    ?>
</td>
</tr><tr>
    <td>&nbsp; - zlib compression support</td>
    <td align="left"><?php echo extension_loaded('zlib') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>'; ?></td>
</tr><tr>
    <td>&nbsp; - XML support</td>
    <td align="left"><?php echo extension_loaded('xml') ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>'; ?></td>
</tr><tr>
<td>&nbsp; - MySQL support</td>
    <td align="left"><?php echo function_exists( 'mysql_connect' ) ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>'; ?></td>
</tr><tr>
<td>&nbsp; - curl support (Not required but external torrents may scrape faster)</td>
    <td align="left"><?php echo function_exists( 'curl_init' ) ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>'; ?></td>
</tr><tr>
<td>&nbsp; - gmp support (Required for IPv6)</td>
    <td align="left"><?php echo extension_loaded( 'gmp' ) ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>'; ?></td>
</tr><tr>
</tr><tr>
<td>&nbsp; - bcmath support (Required for IPv6)</td>
    <td align="left"><?php echo extension_loaded( 'bcmath' ) ? '<b><font color="green">Available</font></b>' : '<b><font color="red">Unavailable</font></b>'; ?></td>
</tr><tr>
<td valign="top">backend/config.php</td>
    <td align="left">
                                    <?php
                                    if (@file_exists('backend/config.php') &&  @is_writable( 'backend/config.php' )){
                                        echo '<b><font color="red">Writeable</font></b><BR>Warning leaving backend/config.php writeable is a security risk';
                                    } else {
                                        echo '<b><font color="green">Unwriteable</font></b>';
                                    }
                                    ?>
</td>
</tr><tr>
<td>Document Root<br><I><font size=1>(Use this for your PATHS in config.php)</font></I></td>
    <td align="left" valign="top"><?php echo str_replace('\\', '/', getcwd()) ?></td>
</tr>

</table>

            
<p>These settings are recommended for PHP in order to ensure full compatibility with TorrentTrader!.</p>                    
<p>However, TorrentTrader! will still operate if your settings do not quite match the recommended</p>

<table cellpadding=3 cellspacing=1 style='border-collapse: collapse' border=1 >
<tr><td width="500px">Directive</td><td>Recommended</td><td>Actual</td></tr>

<?php
$php_recommended_settings = array(array ('Safe Mode','safe_mode','OFF'),
                            array ('Display Errors (Can be off, but does make debugging difficult.)','display_errors','ON'),
                            array ('File Uploads','file_uploads','ON'),
                            array ('Magic Quotes Runtime','magic_quotes_runtime','OFF'),
                            array ('Register Globals - If OFF, TorrentTrader will emulate them as ON','register_globals','ON'),
                            array ('Output Buffering','output_buffering','OFF'),
                            array ('Session auto start','session.auto_start','OFF'),
                            array ('allow_url_fopen (Required for external torrents)', 'allow_url_fopen', 'ON')
);
                        
foreach ($php_recommended_settings as $phprec) {
?>
<tr>
<td><?php echo $phprec[0]; ?>:</td>
<td><?php echo $phprec[2]; ?>:</td>
<td><b>
        <?php
            if ( get_php_setting($phprec[1]) == $phprec[2] ) {
            ?>
                <font color="green">
            <?php
                } else {
            ?>
                <font color="red">
            <?php
                }
                echo get_php_setting($phprec[1]);
            ?>
            </font></b>
</td></tr>
    <?php
    }
    ?>
</table>
                
<BR><b>Directory and File Permissions Check:</b><BR>
<BR>            
In order for TorrentTrader! to function correctly it needs to be able to access or write to certain files or directories.<BR>
<BR>
If you see "Unwriteable" you need to change the permissions on the file or directory to 777 (directories) or 666 (files) so that  TorrentTrader to write to it.<BR>
<BR>

<table cellpadding=3 cellspacing=1 style='border-collapse: collapse' border=1 >
<?php
    writableCell( 'backups' );
    writableCell( 'uploads' );
    writableCell( 'uploads/images' );
    writableCell( 'cache' );
    writableCell( 'import' );
?>
</table>
<BR>
<?php
require_once("backend/mysql.php");
echo "<b>Table Status Check:</b><br><br>";
if (!@mysql_connect($mysql_host, $mysql_user, $mysql_pass))
    printf("<font color='red'><b>Failed to connect to database:</b></font> (%d) %s<br>", mysql_errno(), mysql_error());
else {

    if (!$r=mysql_list_tables($mysql_db))
        printf("<font color='red'><b>Failed to list tables:</b></font> (%d) %s<br>", mysql_errno(), mysql_error());
    else {
        $tables = array();
        while($rr=mysql_fetch_row($r))
            $tables[] = $rr[0];

        $arr[] = "announce";
        $arr[] = "bans";
        $arr[] = "blocks";
        $arr[] = "categories";
        $arr[] = "censor";
        $arr[] = "comments";
        $arr[] = "completed";
        $arr[] = "countries";
        $arr[] = "email_bans";
        $arr[] = "faq";
        $arr[] = "groups";
        $arr[] = "guests";
        $arr[] = "languages";
        $arr[] = "log";
        $arr[] = "messages";
        $arr[] = "news";
        $arr[] = "peers";
        $arr[] = "pollanswers";
        $arr[] = "polls";
        $arr[] = "ratings";
        $arr[] = "reports";
        $arr[] = "rules";
        $arr[] = "shoutbox";
        $arr[] = "stylesheets";
        $arr[] = "tasks";
        $arr[] = "teams";
        $arr[] = "torrentlang";
        $arr[] = "torrents";
        $arr[] = "users";
        $arr[] = "warnings";

        echo "<table cellpadding=3 cellspacing=1 style='border-collapse: collapse' border=1>";
        echo "<tr><th>Table</th><th>Status</th></tr>";
        foreach ($arr as $t)
            if (!in_array($t, $tables))
                echo "<tr><td>$t</td><td align='right'><font color='red'><b>MISSING</b></td></tr>";
            else
                echo "<tr><td>$t</td><td align='right'><font color='green'><b>OK</b></td></tr>";
        echo "</table>";
        require("backend/config.php");
        echo "<BR><BR><b>Default Theme:</B> ";
        if (!is_numeric($site_config["default_theme"]))
                echo "<font color='red'><b>Invalid.</B></font> (Not a number)";
        else {
                $res = mysql_query("SELECT uri FROM stylesheets WHERE id=$site_config[default_theme]");
                if ($row = mysql_fetch_row($res)) {
                        if (file_exists("themes/$row[0]/header.php"))
                                echo "<font color='green'><b>Valid.</B></font> (ID: $site_config[default_theme], Path: themes/$row[0]/)";
                        else
                                echo "<font color='red'><b>Invalid.</B></font> (No header.php found)";
                } else
                        echo "<font color='red'><b>Invalid.</B></font> (No theme found with ID $site_config[default_theme])";
        }

        echo "<BR><b>Default Language:</B> ";
        if (!is_numeric($site_config["default_language"]))
                echo "<font color='red'><b>Invalid.</B></font> (Not a number)";
        else {
                $res = mysql_query("SELECT uri FROM languages WHERE id=$site_config[default_language]");
                if ($row = mysql_fetch_row($res)) {
                        if (file_exists("languages/$row[0]"))
                                echo "<font color='green'><b>Valid.</B></font> (ID: $site_config[default_language], Path: languages/$row[0])";
                        else
                                echo "<font color='red'><b>Invalid.</B></font> (File languages/$row[0] missing)";
                } else
                        echo "<font color='red'><b>Invalid.</B></font> (No language found with ID $site_config[default_language])";
        }
    }
}
?>
</body>
</html>
    <?php
}//end func

?>