<?php

// MySQL server configuration
define("MYSQL_HOSTNAME", "localhost");
define("MYSQL_USERNAME", "<ToBeDefined>");
define("MYSQL_DBNAME", "mopscreens");
define("MYSQL_PASSWORD", "<ToBeDefined>");

define("MEOS_PASSWORD", "<ToBeDefined>");

//Localization. NOTE: UTF-8 encoding required for non-latin characters
$lang = array();
$lang['selectcmp'] = "Select Competition";
$lang['place'] = "Place";
$lang['team'] = "Team";
$lang['name'] = "Name";
$lang['after'] = "&nbsp;";
$lang['time'] = "Time";
$lang['finish'] = "Finish";
$lang['tottime'] = "Total Time";
$lang['totafter'] = "&nbsp;";
$lang['pts'] = "Pts";

define("CST_CONTENT_PICTURE", 1);
define("CST_CONTENT_TEXT", 2);
define("CST_CONTENT_HTML", 3);
define("CST_CONTENT_START", 4);
define("CST_CONTENT_RESULT", 5);
define("CST_CONTENT_SUMMARY",6);
define("CST_CONTENT_BLOG",7);
define("CST_CONTENT_SLIDES",8);
define("CST_CONTENT_RADIO",9);

define("CST_MODE_INDIVIDUAL",1);
define("CST_MODE_RELAY", 2);
define("CST_MODE_MULTISTAGE",3);
define("CST_MODE_SHOWO",4);
define("CST_MODE_ROGAINING",5);

define("CST_OPTION_NORMAL",0);
define("CST_OPTION_ALTERNATE", 1);

define("CST_PENALTY_S", 30);
define("CST_REFRESHRATE",50); // time in 0.1 s

define("NB_SCREEN",23);
define("NB_PANEL",4);

define("CST_NBRADIO_ONEPANEL", 15);
define("CST_NBRADIO_TWOPANELS", 6);
define("CST_NBRADIO_PANELS", 4);

?>
