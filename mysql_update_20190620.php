<?php


/*
20/06/2019
*/

include_once('config.php');

$link = ConnectToDB();

$sql = "ALTER TABLE mopcompetition ADD visible TINYINT(1) NOT NULL DEFAULT '1' AFTER homepage";
mysqli_query($link, $sql);

$sql = "CREATE TABLE mopcourse (cid int(11) NOT NULL, id int(11) NOT NULL, name varchar(64) NOT NULL, ord int(11) NOT NULL DEFAULT '0') ENGINE=MyISAM DEFAULT CHARSET=utf8";
mysqli_query($link, $sql);

$sql = "CREATE TABLE resultcourse (rcid int(11) NOT NULL, cid int(11) NOT NULL DEFAULT '0', id int(11) NOT NULL, sid int(11) NOT NULL, panel int(2) NOT NULL DEFAULT '1') ENGINE=MyISAM DEFAULT CHARSET=utf8";
mysqli_query($link, $sql);


$sql = "ALTER TABLE mopcompetitor ADD crs INT(11) NOT NULL DEFAULT '0' AFTER timestamp, ADD rogpoints INT(11) NOT NULL DEFAULT '0' AFTER crs, ADD rogpointsgross INT(11) NOT NULL DEFAULT '0' AFTER rogpoints, ADD rogreduction INT(11) NOT NULL DEFAULT '0' AFTER rogpointsgross, ADD rogovertime INT(11) NOT NULL DEFAULT '0' AFTER rogreduction, ADD country VARCHAR(3) NOT NULL DEFAULT 'FRA' AFTER rogovertime";
mysqli_query($link, $sql);

$sql = "ALTER TABLE resultscreen  ADD panel1displaynomprenom TINYINT(1) NOT NULL DEFAULT '0'  AFTER panel1radioctrl";
mysqli_query($link, $sql);
$sql = "ALTER TABLE resultscreen  ADD panel2displaynomprenom TINYINT(1) NOT NULL DEFAULT '0'  AFTER panel2radioctrl";
mysqli_query($link, $sql);
$sql = "ALTER TABLE resultscreen  ADD panel3displaynomprenom TINYINT(1) NOT NULL DEFAULT '0'  AFTER panel3radioctrl";
mysqli_query($link, $sql);
$sql = "ALTER TABLE resultscreen  ADD panel4displaynomprenom TINYINT(1) NOT NULL DEFAULT '0'  AFTER panel4radioctrl";
mysqli_query($link, $sql);

