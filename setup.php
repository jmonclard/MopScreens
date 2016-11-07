<?php
  /*
  Copyright 2013 Melin Software HB
  Copyright 2014-2016 Metraware
  
  Licensed under the Apache License, Version 2.0 (the "License");
  you may not use this file except in compliance with the License.
  You may obtain a copy of the License at
  
      http://www.apache.org/licenses/LICENSE-2.0
  
  Unless required by applicable law or agreed to in writing, software
  distributed under the License is distributed on an "AS IS" BASIS,
  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  See the License for the specific language governing permissions and
  limitations under the License.
  */

	include_once("functions.php");
	ConnectToDB();


function setupIddBase() {
  return " cid INT NOT NULL, id INT NOT NULL, PRIMARY KEY (cid, id),";
}

function setupBaseCompetitor() {
  return " name VARCHAR(64) NOT NULL DEFAULT '',".
         " org INT NOT NULL DEFAULT 0,".
         " cls INT NOT NULL DEFAULT 0,".
         " stat TINYINT NOT NULL DEFAULT 0,".
         " st INT NOT NULL DEFAULT 0,".
         " rt INT NOT NULL DEFAULT 0,".
         " INDEX(org), INDEX(cls),  INDEX(stat, rt), INDEX(st)";
}

function setup() {  

  $sql = "CREATE TABLE IF NOT EXISTS mopclass (".
   			setupIddBase().
   			" name VARCHAR(64) NOT NULL DEFAULT '',".
   			" ord INT NOT NULL DEFAULT 0, INDEX(ord)".
   			") ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
  query($sql);
  
  $sql = "CREATE TABLE IF NOT EXISTS mopclasscontrol (".
         " cid INT NOT NULL, id INT NOT NULL,".
         " leg TINYINT NOT NULL, ord TINYINT NOT NULL,".
         " PRIMARY KEY(cid, id, leg, ord), ".
         " ctrl INT NOT NULL DEFAULT 0".
         ") ENGINE = MyISAM";
  query($sql);

  $sql = "CREATE TABLE IF NOT EXISTS mopcompetition (".
   			setupIddBase().
   			" name VARCHAR(64) NOT NULL DEFAULT '',".
   			" date DATE NOT NULL DEFAULT '2013-11-04',".
   			" organizer VARCHAR(64) NOT NULL DEFAULT '',".
   			" homepage VARCHAR(128) NOT NULL DEFAULT ''".
   			") ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
  query($sql);
 
  
  $sql = "CREATE TABLE IF NOT EXISTS mopcompetitor (".
   			 setupIddBase().
   			 setupBaseCompetitor().
         ", tstat TINYINT NOT NULL DEFAULT 0,". // Total status
         " it INT NOT NULL DEFAULT 0,". // Input time
         " timestamp INT NOT NULL DEFAULT 0". // Last refresh
   			") ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
  query($sql);
 
  $sql = "CREATE TABLE IF NOT EXISTS mopcontrol (".
   			setupIddBase().
   			" name VARCHAR(64) NOT NULL DEFAULT ''".
   			") ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
  query($sql);
  
  $sql = "CREATE TABLE IF NOT EXISTS moporganization (".
   			 setupIddBase().
   			" name VARCHAR(64) NOT NULL DEFAULT ''".
   			") ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
  query($sql);
 
  $sql = "CREATE TABLE IF NOT EXISTS mopradio (".
         " cid INT NOT NULL, id INT NOT NULL,".
         " ctrl INT NOT NULL,".
         " PRIMARY KEY(cid, id, ctrl), ".
         " rt INT NOT NULL DEFAULT 0,".
         " timestamp INT NOT NULL DEFAULT 0".
         ") ENGINE = MyISAM";
  query($sql);
  
  $sql = "CREATE TABLE IF NOT EXISTS mopteam (".
   			 setupIddBase().
   			 setupBaseCompetitor().
   			") ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";

  query($sql);
   
  $sql = "CREATE TABLE IF NOT EXISTS mopteammember (".
         " cid INT NOT NULL, id INT NOT NULL,".
         " leg TINYINT NOT NULL, ord TINYINT NOT NULL,".
         " PRIMARY KEY(cid, id, leg, ord), ".
         " rid INT NOT NULL DEFAULT 0".
         ") ENGINE = MyISAM";


  query($sql);
  
  $sql = "CREATE TABLE IF NOT EXISTS resultblog (".
        " rcid int(11) NOT NULL,".
        " timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,".
        " text varchar(256) CHARACTER SET utf8 NOT NULL".
        ") ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
  query($sql);
        
  
  $sql = "CREATE TABLE `resultclass` (
      `rcid` int(11) NOT NULL,
      `cid` int(11) NOT NULL default '0',
      `id` int(11) NOT NULL,
      `sid` int(11) NOT NULL,
      `panel` tinyint(2) NOT NULL default '1',
      KEY `rcid` (`rcid`),
      KEY `cid` (`cid`),
      KEY `id` (`id`),
      KEY `sid` (`sid`)
      ) ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
  query($sql);

  $sql = "CREATE TABLE `resultconfig` (
      `rcid` int(11) NOT NULL,
      `name` varchar(64) character set utf8 NOT NULL,
      `active` tinyint(1) NOT NULL default '0' COMMENT '1=active',
      PRIMARY KEY  (`rcid`)
      ) ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
  query($sql);

  $sql = "CREATE TABLE `resultradio` (
      `idsender` tinyint(3) unsigned NOT NULL,
      `idreceiver` tinyint(3) unsigned NOT NULL,
      `senderbattery` smallint(6) unsigned NOT NULL,
      `rxlevel` int(10) NOT NULL,
      `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
      ) ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
  query($sql);
  
  $sql = "CREATE TABLE `resultradioconfig` (
      `srcid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'screen radio competition id',
      `srcname` varchar(30) NOT NULL,
      `srcmap` varchar(120) NOT NULL,
      `srcx0` double NOT NULL DEFAULT '0',
      `srcy0` double NOT NULL DEFAULT '0',
      `srcx1` double NOT NULL DEFAULT '100',
      `srcy1` double NOT NULL DEFAULT '100',
      PRIMARY KEY (`srcid`)
      ) ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1 ;";
  query($sql);

  $sql = "CREATE TABLE `resultradioposition` (
      `srcid` int(10) unsigned NOT NULL,
      `radioid` int(10) unsigned NOT NULL,
      `radiox` double NOT NULL,
      `radioy` double NOT NULL
      ) ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
  query($sql);

  $sql = "CREATE TABLE `resultscreen` (
      `rcid` int(11) NOT NULL,
      `sid` int(11) NOT NULL,
      `cid` int(11) NOT NULL DEFAULT '0',
      `style` varchar(128) DEFAULT 'co2016-04-01s.css',
      `title` varchar(128) DEFAULT 'Title',
      `titlesize` tinyint(4) DEFAULT '24',
      `titlecolor` varchar(6) DEFAULT '000000',
      `subtitle` varchar(128) DEFAULT NULL,
      `subtitlesize` tinyint(4) DEFAULT '16',
      `subtitlecolor` varchar(6) DEFAULT '000000',
      `titleleftpict` varchar(128) DEFAULT NULL,
      `titlerightpict` varchar(128) DEFAULT NULL,
      `panelscount` tinyint(2) DEFAULT '2' COMMENT 'Number of panels (1 to 4)',
      `panel1content` tinyint(4) DEFAULT '2' COMMENT '1=picture, 2=text, 3=html, 4=start, 5=result, 6=summary, 7=blog, 8=slides',
      `panel1mode` tinyint(2) DEFAULT '1',
      `panel1tm_count` int(10) unsigned NOT NULL DEFAULT '2',
      `panel1alternate` tinyint(2) DEFAULT '0',
      `panel1pict` varchar(128) DEFAULT NULL,
      `panel1txt` varchar(512) DEFAULT NULL,
      `panel1txtsize` tinyint(4) DEFAULT '12',
      `panel1txtcolor` varchar(6) DEFAULT '000000',
      `panel1html` varchar(128) DEFAULT NULL,
      `panel1firstline` int(10) unsigned NOT NULL DEFAULT '1',
      `panel1fixedlines` tinyint(4) NOT NULL DEFAULT '3',
      `panel1scrolledlines` tinyint(4) NOT NULL DEFAULT '17',
      `panel1scrolltime` tinyint(4) NOT NULL DEFAULT '3',
      `panel1scrollbeforetime` tinyint(4) NOT NULL DEFAULT '50',
      `panel1scrollaftertime` tinyint(4) NOT NULL DEFAULT '50',
      `panel1updateduration` int(11) NOT NULL DEFAULT '10',
      `panel1lastrefresh` int(11) NOT NULL DEFAULT '0',
      `panel1lastredraw` int(11) NOT NULL DEFAULT '0',
      `panel2content` tinyint(2) DEFAULT '5' COMMENT '1=picture, 2=text, 3=html, 4=start, 5=result, 6=summary, 7=blog, 8=slides',
      `panel2mode` tinyint(2) DEFAULT '1',
      `panel2tm_count` int(10) unsigned NOT NULL DEFAULT '2',
      `panel2alternate` tinyint(2) DEFAULT '0',
      `panel2pict` varchar(128) DEFAULT NULL,
      `panel2txt` varchar(512) DEFAULT NULL,
      `panel2txtsize` tinyint(4) DEFAULT '12',
      `panel2txtcolor` varchar(6) DEFAULT '000000',
      `panel2html` varchar(128) DEFAULT NULL,
      `panel2firstline` int(10) unsigned NOT NULL DEFAULT '1',
      `panel2fixedlines` tinyint(4) NOT NULL DEFAULT '3',
      `panel2scrolledlines` tinyint(4) NOT NULL DEFAULT '17',
      `panel2scrolltime` tinyint(4) NOT NULL DEFAULT '3' COMMENT 'en 1/10s',
      `panel2scrollbeforetime` tinyint(4) NOT NULL DEFAULT '50' COMMENT 'en 0.1s',
      `panel2scrollaftertime` tinyint(4) NOT NULL DEFAULT '50' COMMENT 'en 0.1s',
      `panel2updateduration` int(11) NOT NULL DEFAULT '10',
      `panel2lastrefresh` int(11) NOT NULL DEFAULT '0',
      `panel2lastredraw` int(11) NOT NULL DEFAULT '0',
      `panel3content` tinyint(2) DEFAULT '5' COMMENT '1=picture, 2=text, 3=html, 4=start, 5=result, 6=summary, 7=blog, 8=slides',
      `panel3mode` tinyint(2) DEFAULT '1',
      `panel3tm_count` int(10) unsigned NOT NULL DEFAULT '2',
      `panel3alternate` tinyint(2) DEFAULT '0',
      `panel3pict` varchar(128) DEFAULT NULL,
      `panel3txt` varchar(512) DEFAULT NULL,
      `panel3txtsize` tinyint(4) DEFAULT '12',
      `panel3txtcolor` varchar(6) DEFAULT '000000',
      `panel3html` varchar(128) DEFAULT NULL,
      `panel3firstline` int(10) unsigned NOT NULL DEFAULT '1',
      `panel3fixedlines` tinyint(4) NOT NULL DEFAULT '3',
      `panel3scrolledlines` tinyint(4) NOT NULL DEFAULT '17',
      `panel3scrolltime` tinyint(4) NOT NULL DEFAULT '3' COMMENT 'en 1/10s',
      `panel3scrollbeforetime` tinyint(4) NOT NULL DEFAULT '50' COMMENT 'en 0.1s',
      `panel3scrollaftertime` tinyint(4) NOT NULL DEFAULT '50' COMMENT 'en 0.1s',
      `panel3updateduration` int(11) NOT NULL DEFAULT '10',
      `panel3lastrefresh` int(11) NOT NULL DEFAULT '0',
      `panel3lastredraw` int(11) NOT NULL DEFAULT '0',
      `panel4content` tinyint(4) DEFAULT '5' COMMENT '1=picture, 2=text, 3=html, 4=start, 5=result, 6=summary, 7=blog, 8=slides',
      `panel4mode` tinyint(2) DEFAULT '1',
      `panel4tm_count` int(10) unsigned NOT NULL DEFAULT '2',
      `panel4alternate` tinyint(2) DEFAULT '0',
      `panel4pict` varchar(128) DEFAULT NULL,
      `panel4txt` varchar(512) DEFAULT NULL,
      `panel4txtsize` tinyint(4) DEFAULT '12',
      `panel4txtcolor` varchar(6) DEFAULT '000000',
      `panel4html` varchar(128) DEFAULT NULL,
      `panel4firstline` int(10) unsigned NOT NULL DEFAULT '1',
      `panel4fixedlines` tinyint(4) NOT NULL DEFAULT '3',
      `panel4scrolledlines` tinyint(4) NOT NULL DEFAULT '17',
      `panel4scrolltime` tinyint(4) NOT NULL DEFAULT '10',
      `panel4scrollbeforetime` tinyint(4) NOT NULL DEFAULT '50',
      `panel4scrollaftertime` tinyint(4) NOT NULL DEFAULT '80',
      `panel4updateduration` int(11) NOT NULL DEFAULT '3',
      `panel4lastrefresh` int(11) NOT NULL DEFAULT '0',
      `panel4lastredraw` int(11) NOT NULL DEFAULT '0',
      `refresh` int(11) NOT NULL DEFAULT '0',
      PRIMARY KEY  (`rcid`,`sid`),
      KEY `cid` (`cid`)
      ) ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
  query($sql);

}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>MeOS Online Results</title>
</head>
<body>

<?php

setup();

print '<h1>MeOS Online Results</h1><p>Configuration seems to be correct.</p>';

?>

</body></html>
