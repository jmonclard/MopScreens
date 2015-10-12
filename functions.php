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
  
include_once("config.php");

/** Connecto to MySQL */
function ConnectToDB() {
  $link = mysql_connect(MYSQL_HOSTNAME, MYSQL_USERNAME, MYSQL_PASSWORD);
  if (!$link) {
    die('Not connected : ' . mysql_error());
  }

  $db_selected = mysql_select_db(MYSQL_DBNAME, $link);
  if (!$db_selected) {
    die ("Can't use ". MYSQL_HOSTNAME. ' : ' . mysql_error());
  }    
  return $link;
}

function query($sql) {
 $result = mysql_query($sql);
 if (!$result) {
   die('Invalid query: ' . mysql_error());
 } 
 return $result;
}

function getStatusString($status) {
  switch($status) {
    case 0: 
      return "&ndash;"; //Unknown, running?
    case 1:
      return "OK";
    case 20:
      return "DNS"; // Did not start;
    case 3:
      return "MP"; // Missing punch
    case 4:
      return "DNF"; //Did not finish
    case 5:
      return "DQ"; // Disqualified
    case 6:      
      return "OT"; // Overtime
    case 99:
      return "NP"; //Not participating;
  }
}

function calculateResult($res) {
  $out = array();  
  
  $place = 0;
  $count = 0;
  $lastTime = -1;
  $bestTime = -1;
  $lastTeam = -1;
  $totalResult = array();
  $hasTotal = false;
  while ($r = mysql_fetch_array($res)) {
    if ($lastTeam == $r['id']) {
      $out[$count]['name'] .= " / " . $r['name'];
      continue; 
    }
    else
      $lastTeam = $r['id'];
      
    $count++;
    $t = $r['time']/10;
    if ($bestTime == -1)
      $bestTime = $t;
    if ($lastTime != $t) {
      $place = $count;
      $lastTime = $t;
    }        
    $row = array();
    
    if ($r['status'] == 1) {
      $row['place'] = $place.".";
      $row['name'] = $r['name'];      
      $row['team'] = $r['team'];
    
      if ($t > 0)
        $row['time'] = sprintf("%d:%02d:%02d", $t/3600, ($t/60)%60, $t%60);
      else
        $row['time'] = "OK"; // No timing
        
      $after = $t - $bestTime;
      if ($after > 3600)
        $row['after'] = sprintf("+%d:%02d:%02d", $after/3600, ($after/60)%60, $after%60);
      elseif ($after > 0)
        $row['after'] = sprintf("+%d:%02d", ($after/60)%60, $after%60);        
      else
        $row['after'] = "";
    }
    else {
      $row['place'] = "";
      $row['name'] = $r['name'];      
      $row['team'] = $r['team'];

      $row['time'] = getStatusString($r['status']);
      $row['after'] = "";
    }
    
          
    if (isset($r['tottime'])) {
      $hasTotal = true;
      if ($r['totstat'] == 1) {
        $tt = $r['tottime']/10;          
        if ($tt > 0)
          $row['tottime'] = sprintf("%d:%02d:%02d", $tt/3600, ($tt/60)%60, $tt%60);
        else
          $row['tottime'] = "OK"; // No timing
      }
      else {
        $row['tottime'] = getStatusString($r['totstat']); 
      }
      
      if ($r['totstat'] > 0)
        $totalResult[$count] = ($r['totstat']-1) * 10000000 + $r['tottime'];
      else
        $totalResult[$count] = 10000000 * 100;
    }
          
    $out[$count] = $row;
  }
  
  if ($hasTotal) {
    array_multisort($totalResult, $out);
    $place = 0;
    $lastTime = -1;
    $bestTime = -1;
    
    for($k = 0; $k<$count; $k++) {
      if ($totalResult[$k] < 10000000) {
        $t = $totalResult[$k];
        if ($bestTime == -1)
          $bestTime = $t;
        if ($lastTime != $t) {
          $place = $k+1;
          $lastTime = $t;
        }
        if ($out[$k]['place'] > 0)
          $out[$k]['time'].=" (".substr($out[$k]['place'], 0, -1).")";
        
        $out[$k]['place'] = $place.".";
        
        $after = ($t - $bestTime)/10;
        if ($after > 3600)
          $out[$k]['totafter'] = sprintf("+%d:%02d:%02d", $after/3600, ($after/60)%60, $after%60);
        elseif ($after > 0)
          $out[$k]['totafter'] = sprintf("+%d:%02d", ($after/60)%60, $after%60);        
        else
          $out[$k]['totafter'] = '';
      }
      else {
        $out[$k]['place'] = '';
        $out[$k]['aftertot'] = '';
      }
    }
  }
  
  return $out;
}

/** Format a result array as a table.*/
function formatResult($result) {
  global $lang;
  $head = false;
  print "<table>";
  foreach($result as $row) {            
    if ($head == false) {
      print "<tr>";
      foreach($row as $key => $cell) {
        print "<th>".$lang[$key]."</th>\n";  
      }
      print "</tr>";
      $head = true; 
    }      
    print "<tr>";
    foreach($row as $cell) {
      print "<td>$cell</td>";  
    }
    print "</tr>";
  }
  print "</table>";
}

function selectRadio($cls) {
  global $cmpId;
  $radio = '';
  $sql = "SELECT leg, ctrl, mopControl.name FROM mopClassControl, mopControl ".
         "WHERE mopControl.cid='$cmpId' AND mopClassControl.cid='$cmpId' ".
         "AND mopClassControl.id='$cls' AND mopClassControl.ctrl=mopControl.id ORDER BY leg ASC, ord ASC";
         
  
  $res = mysql_query($sql);
  $radios = mysql_num_rows($res);
  
  if ($radios > 0) {
    if (isset($_GET['radio'])) {
      $radio = $_GET['radio'];
    }

    while ($r = mysql_fetch_array($res)) {
      print '<a href="'."$PHP_SELF?cls=$cls&radio=$r[ctrl]".'">'.$r['name']."</a><br/>\n";      
    } 
    print '<a href="'."$PHP_SELF?cls=$cls&radio=finish".'">'.'Finish'."</a><br/>\n";      
  }
  else {
    // Only finish   
    $radio = 'finish';
  }
  return $radio; 
}

function selectLegRadio($cls, $leg, $ord) {
  global $cmpId;
  $radio = '';
  $sql = "SELECT ctrl, mopControl.name FROM mopClassControl, mopControl ".
         "WHERE mopControl.cid='$cmpId' AND mopClassControl.cid='$cmpId' ".
         "AND mopClassControl.id='$cls' AND mopClassControl.ctrl=mopControl.id AND leg='$leg' AND ord='$ord'";
         
  
  $res = mysql_query($sql);
  $radios = mysql_num_rows($res);
  //print $sql;
  if ($radios > 0) {
    
    while ($r = mysql_fetch_array($res)) {
      print '<a href="'."$PHP_SELF?cls=$cls&leg=$leg&ord=$ord&radio=$r[ctrl]".'">'.$r['name']."</a>; \n";      
    } 
     
  }
  else {
    // Only finish   
    //$radio = 'finish';
  }
  print '<a href="'."$PHP_SELF?cls=$cls&leg=$leg&ord=$ord&radio=finish".'">'.'Finish'."</a><br/>\n";
  return $radio; 
}

/** Update or add a record to a table. */
function updateTable($table, $cid, $id, $sqlupdate) {
  $ifc = "cid='$cid' AND id='$id'";
  $res = mysql_query("SELECT id FROM `$table` WHERE $ifc");
  
  if (mysql_num_rows($res) > 0) {
    $sql = "UPDATE `$table` SET $sqlupdate WHERE $ifc";
  }
  else {
    $sql = "INSERT INTO `$table` SET cid='$cid', id='$id', $sqlupdate";  
  }
  
  //print "$sql\n";
  mysql_query($sql);
}

/** Update a link with outer level over legs and other level over fieldName (controls, team members etc)*/
function updateLinkTable($table, $cid, $id, $fieldName, $encoded) {
  $sql = "DELETE FROM $table WHERE cid='$cid' AND id='$id'";  
  mysql_query($sql);
  $legNumber = 1;  
  $legs = explode(";", $encoded);
  foreach($legs as $leg) {
    $runners = explode(",", $leg);
    foreach($runners as $key => $runner) {
      $sql = "INSERT INTO $table SET cid='$cid', id='$id', leg=$legNumber, ord=$key, $fieldName=$runner"; 
      //print "$sql \n";
      mysql_query($sql);
    }
    $legNumber++;
  }  
}

/** Remove all data from a table related to an event. */
function clearCompetition($cid) {
   $tables = array(0=>"mopControl", "mopClass", "mopOrganization", "mopCompetitor",
                      "mopTeam", "mopTeamMember", "mopClassControl", "mopRadio");
                      
   foreach($tables as $table) {
     $sql = "DELETE FROM $table WHERE cid=$cid";
     mysql_query($sql);
   } 
}

/** Update control table */
function processCompetition($cid, $cmp) {
  $name = mysql_real_escape_string($cmp);
  $date = mysql_real_escape_string($cmp['date']);
  $organizer = mysql_real_escape_string($cmp['organizer']);
  $homepage = mysql_real_escape_string($cmp['homepage']);
  
  $sqlupdate = "name='$name', date='$date', organizer='$organizer', homepage='$homepage'";
  updateTable("mopCompetition", $cid, 1, $sqlupdate);
}

/** Update control table */
function processControl($cid, $ctrl) {
  $id = mysql_real_escape_string($ctrl['id']);
  $name = mysql_real_escape_string($ctrl);
  $sqlupdate = "name='$name'";
  updateTable("mopControl", $cid, $id, $sqlupdate);
}

/** Update class table */
function processClass($cid, $cls) {
  $id = mysql_real_escape_string($cls['id']);
  $ord = mysql_real_escape_string($cls['ord']);
  $name = mysql_real_escape_string($cls);
  $sqlupdate = "name='$name', ord='$ord'";
  updateTable("mopClass", $cid, $id, $sqlupdate);
    
  if (isset($cls['radio'])) {
    $radio = mysql_real_escape_string($cls['radio']);
    updateLinkTable("mopClassControl", $cid, $id, "ctrl", $radio);    
  }
}

/** Update organization table */
function processOrganization($cid, $org) {
  $id = mysql_real_escape_string($org['id']);
  $name = mysql_real_escape_string($org);
  $sqlupdate = "name='$name'";
  updateTable("mopOrganization", $cid, $id, $sqlupdate);
}

/** Update competitor table */
function processCompetitor($cid, $cmp) {
  $base = $cmp->base;
  $id = mysql_real_escape_string($cmp['id']);
  
  $name = mysql_real_escape_string($base);
  $org = (int)$base['org'];
  $cls = (int)$base['cls'];
  $stat = (int)$base['stat'];
  $st = (int)$base['st'];
  $rt = (int)$base['rt'];
  
  
  $sqlupdate = "name='$name', org=$org, cls=$cls, stat=$stat, st=$st, rt=$rt";

  if (isset($cmp->input)) {
    $input = $cmp->input;
    $it = (int)$input['it'];
    $tstat = (int)$input['tstat'];
    $sqlupdate.=", it=$it, tstat=$tstat";
  }

  updateTable("mopCompetitor", $cid, $id, $sqlupdate);  
  if (isset($cmp->radio)) {
    $sql = "DELETE FROM mopRadio WHERE cid='$cid' AND id='$id'";
    mysql_query($sql);
    $radios = explode(";", $cmp->radio);
    foreach($radios as $radio) {
      $tmp = explode(",", $radio);
      $radioId = (int)$tmp[0];
      $radioTime = (int)$tmp[1];
      $sql = "REPLACE INTO mopRadio SET cid='$cid', id='$id', ctrl='$radioId', rt='$radioTime'";
      mysql_query($sql);
    }
  }  
}

/** Update team table */
function processTeam($cid, $team) {
  $base = $team->base;
  $id = mysql_real_escape_string($team['id']);  
  
  $name = mysql_real_escape_string($base);
  $org = (int)$base['org'];
  $cls = (int)$base['cls'];
  $stat = (int)$base['stat'];
  $st = (int)$base['st'];
  $rt = (int)$base['rt'];
  
  $sqlupdate = "name='$name', org=$org, cls=$cls, stat=$stat, st=$st, rt=$rt";
  updateTable("mopTeam", $cid, $id, $sqlupdate);
  
  if (isset($team->r)) {
    updateLinkTable("mopTeamMember", $cid, $id, "rid", $team->r);
  }
}

/** MOP return code. */
function returnStatus($stat) {
  die('<?xml version="1.0"?><MOPStatus status="'.$stat.'"></MOPStatus>');
}

?>