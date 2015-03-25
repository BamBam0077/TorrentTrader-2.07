<?php
//BEGIN FRAME
function begin_frame($caption = "-", $align = "justify"){
    global $THEME;
    global $site_config;
    print("<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tbody><tr><td class=\"captionleft2\"><img src=\"".$site_config['SITEURL']."/themes/default/images/blank.gif\" style=\"display: block;\" height=\"34\" width=\"22\"></td><td class=\"captionbar2\" style=\"white-space: nowrap;\"><b>$caption</b></td><td class=\"captionend2\"><img src=\"".$site_config['SITEURL']."/themes/default/images/blank.gif\" alt=\"\" style=\"display: block;\" height=\"34\" width=\"2\"></td><td class=\"captionmain2\"><img src=\"".$site_config['SITEURL']."/themes/default/images/blank.gif\" alt=\"\" style=\"display: block;\" height=\"34\" width=\"1\"></td><td class=\"captionright2\"><img src=\"".$site_config['SITEURL']."/themes/default/images/blank.gif\" alt=\"\" style=\"display: block;\" height=\"34\" width=\"22\"></td></tr></tbody></table><table cellpadding=\"0\" cellspacing=\"0\"><tbody><tr><td class=\"bodyleft2\"><img src=\"".$site_config['SITEURL']."/themes/default/images/blank.gif\" alt=\"\" style=\"display: block;\" height=\"1\" width=\"11\"></td><td class=\"bodymain2\" align=\"$align\">");
}


//END FRAME
function end_frame() {
    global $THEME;
    global $site_config;
    print("</td><td class=\"bodyright2\"><img src=\"".$site_config['SITEURL']."/themes/default/images/blank.gif\" alt=\"\" style=\"display: block;\" height=\"1\" width=\"12\"></td></tr></tbody></table><table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tbody><tr><td class=\"bottomleft\"><img src=\"".$site_config['SITEURL']."/themes/default/images/blank.gif\" alt=\"\" style=\"display: block;\" height=\"20\" width=\"22\"></td><td class=\"bottommain\"><img src=\"".$site_config['SITEURL']."/themes/default/images/blank.gif\" alt=\"\" style=\"display: block;\" height=\"20\" width=\"2\"></td><td class=\"bottomright\"><img src=\"".$site_config['SITEURL']."/themes/default/images/blank.gif\" alt=\"\" style=\"display: block;\" height=\"20\" width=\"21\"></td></tr></tbody></table><BR>");
}

//BEGIN BLOCK
function begin_block($caption = "-", $align = "justify"){
    global $THEME;
    global $site_config;
    print("<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tbody><tr><td class=\"captionleft\"><img src=\"".$site_config['SITEURL']."/themes/default/images/blank.gif\" style=\"display: block;\" height=\"34\" width=\"22\"></td><td class=\"captionbar\" style=\"white-space: nowrap;\"><b>$caption</b></td><td class=\"captionend\"><img src=\"".$site_config['SITEURL']."/themes/default/images/blank.gif\" alt=\"\" style=\"display: block;\" height=\"34\" width=\"2\"></td><td class=\"captionmain\"><img src=\"".$site_config['SITEURL']."/themes/default/images/blank.gif\" alt=\"\" style=\"display: block;\" height=\"34\" width=\"1\"></td><td class=\"captionright\"><img src=\"".$site_config['SITEURL']."/themes/default/images/blank.gif\" alt=\"\" style=\"display: block;\" height=\"34\" width=\"22\"></td></tr></tbody></table><table cellpadding=\"0\" cellspacing=\"0\"><tbody><tr><td class=\"bodyleft2\"><img src=\"".$site_config['SITEURL']."/themes/default/images/blank.gif\" alt=\"\" style=\"display: block;\" height=\"1\" width=\"11\"></td><td class=\"bodymain2\" align=\"$align\">");
}

//END BLOCK
function end_block(){
    global $THEME;
    global $site_config;
    print("</td><td class=\"bodyright2\"><img src=\"".$site_config['SITEURL']."/themes/default/images/blank.gif\" alt=\"\" style=\"display: block;\" height=\"1\" width=\"12\"></td></tr></tbody></table><table cellpadding=\"0\" cellspacing=\"0\"><tbody><tr><td class=\"bottomleft\"><img src=\"".$site_config['SITEURL']."/themes/default/images/blank.gif\" alt=\"\" style=\"display: block;\" height=\"20\" width=\"22\"></td><td class=\"bottommain\"><img src=\"".$site_config['SITEURL']."/themes/default/images/blank.gif\" alt=\"\" style=\"display: block;\" height=\"20\" width=\"2\"></td><td class=\"bottomright\"><img src=\"".$site_config['SITEURL']."/themes/default/images/blank.gif\" alt=\"\" style=\"display: block;\" height=\"20\" width=\"21\"></td></tr></tbody></table><BR>");
}

function begin_table(){
    print("<table align=center cellpadding=\"0\" cellspacing=\"0\" class=\"ttable_headouter\" width=100%><tr><td><table align=center cellpadding=\"0\" cellspacing=\"0\" class=\"ttable_headinner\" width=100%>\n");
}

function end_table()  {
    print("</table></td></tr></table>\n");
}

function tr($x,$y,$noesc=0) {
    if ($noesc)
        $a = $y;
    else {
        $a = htmlspecialchars($y);
        $a = str_replace("\n", "<br />\n", $a);
    }
    print("<tr><td class=\"heading\" valign=\"top\" align=\"right\">$x</td><td valign=\"top\" align=left>$a</td></tr>\n");
}
?>