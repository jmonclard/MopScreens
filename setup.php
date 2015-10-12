<?php
  /*
  Copyright 2013 Melin Software HB
  
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
         " it INT NOT NULL DEFAULT 0". // Input time
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
         " rt INT NOT NULL DEFAULT 0".
         ") ENGINE = MyISAM";

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
