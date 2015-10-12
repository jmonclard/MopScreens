<?php

// MySQL server configuration
define("MYSQL_HOSTNAME", "localhost");
define("MYSQL_USERNAME", "root");
define("MYSQL_DBNAME", "cfcobases");
define("MYSQL_PASSWORD", "");

define("MEOS_PASSWORD", "resultcfco");

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



define("CST_SCREENMODE_FULL", 1);
define("CST_SCREENMODE_DIVISE", 2);

define("CST_CONTENT_PICTURE", 1);
define("CST_CONTENT_TEXT", 2);
define("CST_CONTENT_HTML", 3);
define("CST_CONTENT_START", 4);
define("CST_CONTENT_RESULT", 5);

define("CST_CONTENT_RELAIS", 4);
?>