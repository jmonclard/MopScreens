<?php
  /*
  Copyright 2013 Melin Software HB
  Copyright 2014 Metraware
  
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
 $sql = "CREATE TABLE IF NOT EXISTS mopCompetition (".
   			setupIddBase().
   			" name VARCHAR(64) NOT NULL DEFAULT '',".
   			" date DATE NOT NULL DEFAULT '2013-11-04',".
   			" organizer VARCHAR(64) NOT NULL DEFAULT '',".
   			" homepage VARCHAR(128) NOT NULL DEFAULT ''".
   			") ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
 
  query($sql);
 
  
  $sql = "CREATE TABLE IF NOT EXISTS mopControl (".
   			setupIddBase().
   			" name VARCHAR(64) NOT NULL DEFAULT ''".
   			") ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
 
  query($sql);
  
  $sql = "CREATE TABLE IF NOT EXISTS mopClass (".
   			setupIddBase().
   			" name VARCHAR(64) NOT NULL DEFAULT '',".
   			" ord INT NOT NULL DEFAULT 0, INDEX(ord)".
   			") ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
   			
  query($sql);
  
  $sql = "CREATE TABLE IF NOT EXISTS mopOrganization (".
   			 setupIddBase().
   			" name VARCHAR(64) NOT NULL DEFAULT ''".
   			") ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
   			
  query($sql);
  
  $sql = "CREATE TABLE IF NOT EXISTS mopCompetitor (".
   			 setupIddBase().
   			 setupBaseCompetitor().
         ", tstat TINYINT NOT NULL DEFAULT 0,". // Total status
         " it INT NOT NULL DEFAULT 0,". // Input time
         " timestamp INT NOT NULL DEFAULT 0". // Last refresh
   			") ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";
  			
  query($sql);
 
  $sql = "CREATE TABLE IF NOT EXISTS mopTeam (".
   			 setupIddBase().
   			 setupBaseCompetitor().
   			") ENGINE = MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci";

  query($sql);
   
  $sql = "CREATE TABLE IF NOT EXISTS mopTeamMember (".
         " cid INT NOT NULL, id INT NOT NULL,".
         " leg TINYINT NOT NULL, ord TINYINT NOT NULL,".
         " PRIMARY KEY(cid, id, leg, ord), ".
         " rid INT NOT NULL DEFAULT 0".
         ") ENGINE = MyISAM";

  query($sql);
  
  $sql = "CREATE TABLE IF NOT EXISTS mopClassControl (".
         " cid INT NOT NULL, id INT NOT NULL,".
         " leg TINYINT NOT NULL, ord TINYINT NOT NULL,".
         " PRIMARY KEY(cid, id, leg, ord), ".
         " ctrl INT NOT NULL DEFAULT 0".
         ") ENGINE = MyISAM";

  query($sql);
  
  $sql = "CREATE TABLE IF NOT EXISTS mopRadio (".
         " cid INT NOT NULL, id INT NOT NULL,".
         " ctrl INT NOT NULL,".
         " PRIMARY KEY(cid, id, ctrl), ".
         " rt INT NOT NULL DEFAULT 0,".
         " timestamp INT NOT NULL DEFAULT 0".
         ") ENGINE = MyISAM";

  query($sql);
  
  
  $sql = "CREATE TABLE `resultclass` (
  `rcid` int(11) NOT NULL,
  `cid` int(11) NOT NULL default '0',
  `id` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `panel` tinyint(2) NOT NULL default '1' COMMENT '1=left, 2=right',
  KEY `rcid` (`rcid`),
  KEY `cid` (`cid`),
  KEY `id` (`id`),
  KEY `sid` (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

  query($sql);

$sql = "CREATE TABLE `resultconfig` (
  `rcid` int(11) NOT NULL,
  `name` varchar(64) character set utf8 NOT NULL,
  `active` tinyint(1) NOT NULL default '0' COMMENT '1=actif',
  PRIMARY KEY  (`rcid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Configuration d''écrans';";

  query($sql);

$sql = "CREATE TABLE `resultscreen` (
  `rcid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `cid` int(11) NOT NULL default '0',
  `title` varchar(128) default 'Title',
  `titlesize` tinyint(4) default '24',
  `titlecolor` varchar(6) default '000000',
  `subtitle` varchar(128) default NULL,
  `subtitlesize` tinyint(4) default '16',
  `subtitlecolor` varchar(6) default '000000',
  `titleleftpict` varchar(128) default NULL,
  `titlerightpict` varchar(128) default NULL,
  `screenmode` tinyint(2) default '2' COMMENT '1=full 2=2panels',
  `fullcontent` tinyint(4) default '2' COMMENT '1=picture, 2=text, 3=html, 4=result relais',
  `fullpict` varchar(128) default NULL,
  `fulltxt` varchar(512) default NULL,
  `fulltxtsize` tinyint(4) default '12',
  `fulltxtcolor` varchar(6) default '000000',
  `fullhtml` varchar(128) default NULL,
  `fullfixedlines` tinyint(4) NOT NULL default '3',
  `fullscrolledlines` tinyint(4) NOT NULL default '17',
  `fullscrolltime` tinyint(4) NOT NULL default '3',
  `fullscrollbeforetime` tinyint(4) NOT NULL default '50',
  `fullscrollaftertime` tinyint(4) NOT NULL default '50',
  `fullupdateduration` int(11) NOT NULL default '10',
  `fulllastrefresh` int(11) NOT NULL default '0',
  `fulllastredraw` int(11) NOT NULL default '0',
  `leftcontent` tinyint(2) default '5' COMMENT '1=picture, 2=text,  3=html, 4=start, 5=result',
  `leftpict` varchar(128) default NULL,
  `lefttxt` varchar(512) default NULL,
  `lefttxtsize` tinyint(4) default '12',
  `lefttxtcolor` varchar(6) default '000000',
  `lefthtml` varchar(128) default NULL,
  `leftfixedlines` tinyint(4) NOT NULL default '3',
  `leftscrolledlines` tinyint(4) NOT NULL default '17',
  `leftscrolltime` tinyint(4) NOT NULL default '3' COMMENT 'en 1/10s',
  `leftscrollbeforetime` tinyint(4) NOT NULL default '50' COMMENT 'en 0.1s',
  `leftscrollaftertime` tinyint(4) NOT NULL default '50' COMMENT 'en 0.1s',
  `leftupdateduration` int(11) NOT NULL default '10',
  `leftlastrefresh` int(11) NOT NULL default '0',
  `leftlastredraw` int(11) NOT NULL default '0',
  `rightcontent` tinyint(2) default '5' COMMENT '1=picture, 2=text,  3=html, 4=start, 5=result',
  `rightpict` varchar(128) default NULL,
  `righttxt` varchar(512) default NULL,
  `righttxtsize` tinyint(4) default '12',
  `righttxtcolor` varchar(6) default '000000',
  `righthtml` varchar(128) default NULL,
  `rightfixedlines` tinyint(4) NOT NULL default '3',
  `rightscrolledlines` tinyint(4) NOT NULL default '17',
  `rightscrolltime` tinyint(4) NOT NULL default '3' COMMENT 'en 1/10s',
  `rightscrollbeforetime` tinyint(4) NOT NULL default '50' COMMENT 'en 0.1s',
  `rightscrollaftertime` tinyint(4) NOT NULL default '50' COMMENT 'en 0.1s',
  `rightupdateduration` int(11) NOT NULL default '10',
  `rightlastrefresh` int(11) NOT NULL default '0',
  `rightlastredraw` int(11) NOT NULL default '0',
  `refresh` int(11) NOT NULL default '0',
  PRIMARY KEY  (`rcid`,`sid`),
  KEY `cid` (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

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
