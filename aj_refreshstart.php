<?php
  /*
  Copyright 2014-2015 Metraware
  
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
  include_once('functions.php');
    session_start();
    header('Content-type: text/html;charset=utf-8');

    $PHP_SELF = $_SERVER['PHP_SELF'];
    ConnectToDB();

    $cls = ((isset($_GET['cls'])) ? $_GET['cls'] : "0");
    $cmpId = ((isset($_GET['cmpId'])) ? $_GET['cmpId'] : "0");
    $leg = ((isset($_GET['leg'])) ? $_GET['leg'] : "0");
    $ord = ((isset($_GET['ord'])) ? $_GET['ord'] : "0");
    $radio = ((isset($_GET['radio'])) ? $_GET['radio'] : "finish");
    
    $rcid = ((isset($_GET['rcid'])) ? $_GET['rcid'] : "0");
    $sid = ((isset($_GET['sid'])) ? $_GET['sid'] : "0");
    $sql = 'UPDATE resultscreen SET fulllastrefresh='.time().' WHERE rcid='.$rcid.' AND sid='.$sid;
    mysql_query($sql);
  
  date_default_timezone_set('UTC');
  if ($numlegs > 1) {
    for ($k = 1; $k <= $numlegs; $k++) {
      $sql = "SELECT max(ord) FROM mopteammember tm, mopteam t WHERE t.cls = '$cls' AND tm.leg=$k AND ".
              "tm.cid = '$cmpId' AND t.cid = '$cmpId' AND tm.id = t.id";
      $res = mysql_query($sql);
      $r = mysql_fetch_array($res);
      $numparallel = $r[0];
      
      if ($numparallel == 0) {
        selectLegRadio($cls, $k, 0);
      }
    }
    
    if ($radio!='') {
      if ($radio == 'finish') {
        $sql = "SELECT t.id AS id, cmp.name AS name, t.name AS team, cmp.rt AS time, cmp.st, cmp.stat AS status, ".
               "cmp.it+cmp.rt AS tottime, cmp.tstat AS totstat ".
               "FROM mopteammember tm, mopcompetitor cmp, mopteam t ".
               "WHERE t.cls = '$cls' AND t.id = tm.id AND tm.rid = cmp.id ".
               "AND t.cid = '$cmpId' AND tm.cid = '$cmpId' AND cmp.cid = '$cmpId' ".
               "AND tm.leg='$leg' AND tm.ord='$ord' ORDER BY cmp.st ASC, cmp.stat, cmp.rt ASC, t.id";
        $sql2 = "SELECT t.id AS id, cmp.name AS name, t.name AS team, cmp.rt AS time, cmp.st, cmp.stat AS status, ".
               "cmp.it+cmp.rt AS tottime, cmp.tstat AS totstat ".
               "FROM mopteammember tm, mopcompetitor cmp, mopteam t ".
               "WHERE t.cls = '$cls' AND t.id = tm.id AND tm.rid = cmp.id ".
               "AND t.cid = '$cmpId' AND tm.cid = '$cmpId' AND cmp.cid = '$cmpId' ".
               "AND tm.leg='$leg' AND tm.ord='$ord' ORDER BY cmp.name ASC, cmp.st ASC, cmp.stat, cmp.rt ASC, t.id";
        $rname = $lang["finish"];
      }
      else {
        $rid = (int)$radio;
        $sql = "SELECT name FROM mopcontrol WHERE cid='$cmpId' AND id='$rid'";
        $res = mysql_query($sql);
        $rinfo = mysql_fetch_array($res);
        $rname = $rinfo['name'];
   
        $sql = "SELECT team.id AS id, cmp.name AS name, team.name AS team, radio.rt AS time, 1 AS status, ".
                 "cmp.it+radio.rt AS tottime, cmp.st, cmp.tstat AS totstat ".
                 "FROM mopradio AS radio, mopteammember AS m, mopteam AS team, mopcompetitor AS cmp ".
                 "WHERE radio.ctrl='$rid' ".
                 "AND radio.id=cmp.id ".
                 "AND m.rid = radio.id ".
                 "AND m.id = team.id ".
                 "AND m.leg='$leg' AND m.ord='$ord' ".
                 "AND cmp.cls='$cls' ".
                 "AND team.cid = '$cmpId' AND m.cid = '$cmpId' AND cmp.cid = '$cmpId' ".
                 "ORDER BY cmp.st ASC, radio.rt ASC ";
		$sql2 = "SELECT team.id AS id, cmp.name AS name, team.name AS team, radio.rt AS time, 1 AS status, ".
                 "cmp.it+radio.rt AS tottime, cmp.st, cmp.tstat AS totstat ".
                 "FROM mopradio AS radio, mopteammember AS m, mopteam AS team, mopcompetitor AS cmp ".
                 "WHERE radio.ctrl='$rid' ".
                 "AND radio.id=cmp.id ".
                 "AND m.rid = radio.id ".
                 "AND m.id = team.id ".
                 "AND m.leg='$leg' AND m.ord='$ord' ".
                 "AND cmp.cls='$cls' ".
                 "AND team.cid = '$cmpId' AND m.cid = '$cmpId' AND cmp.cid = '$cmpId' ".
                 "ORDER BY cmp.name ASC, cmp.st ASC, radio.rt ASC ";
        }

        $res = mysql_query($sql);
        $results = calculeStart($res);
        $res2 = mysql_query($sql2);
        $results2 = calculeStart($res2);
        $results3 = null;
        foreach($results as $i => $v)
        {
            $results3[] = array($results[$i]['start_time'], $results[$i]['name'], $results[$i]['team'], $results2[$i]['name'], $results2[$i]['team'], $results2[$i]['start_time']);
        }
        formatResult($results3);
    }      
  }
  else {
    if (is_null($numlegs)) {
      //No teams;        
      //$radio = selectRadio($cls);              
      if ($radio!='') {
        if ($radio == 'finish') {
          $sql = "SELECT cmp.id AS id, cmp.name AS name, org.name AS team, cmp.st, cmp.rt AS time, cmp.stat AS status ".
                 "FROM mopcompetitor cmp LEFT JOIN moporganization AS org ON cmp.org = org.id AND cmp.cid = org.cid ".
                 "WHERE cmp.cls = '$cls' ".
                 "AND cmp.cid = '$cmpId' ORDER BY cmp.st ASC, cmp.id";
          $sql2 = "SELECT cmp.id AS id, cmp.name AS name, org.name AS team, cmp.st, cmp.rt AS time, cmp.stat AS status ".
                 "FROM mopcompetitor cmp LEFT JOIN moporganization AS org ON cmp.org = org.id AND cmp.cid = org.cid ".
                 "WHERE cmp.cls = '$cls' ".
                 "AND cmp.cid = '$cmpId' ORDER BY cmp.name ASC, cmp.st ASC, cmp.id";
          $rname = $lang["finish"];
        }
        else {
          $rid = (int)$radio;
          $sql = "SELECT name FROM mopcontrol WHERE cid='$cmpId' AND id='$rid'";
          $res = mysql_query($sql);
          $rinfo = mysql_fetch_array($res);
          $rname = $rinfo['name'];
                    
          $sql = "SELECT cmp.id AS id, cmp.name AS name, cmp.st, org.name AS team, radio.rt AS time, 1 AS status ".
                 "FROM mopradio AS radio, mopcompetitor AS cmp ".
                 "LEFT JOIN moporganization AS org ON cmp.org = org.id AND cmp.cid = org.cid ".
                 "WHERE radio.ctrl='$rid' ".
                 "AND radio.id=cmp.id ".
                 "AND cmp.cls='$cls' ".
                 "AND cmp.cid = '$cmpId' AND radio.cid = '$cmpId' ".
                 "ORDER BY cmp.st ASC, radio.st ASC ";
          $sql2 = "SELECT cmp.id AS id, cmp.name AS name, cmp.st, org.name AS team, radio.rt AS time, 1 AS status ".
                 "FROM mopradio AS radio, mopcompetitor AS cmp ".
                 "LEFT JOIN moporganization AS org ON cmp.org = org.id AND cmp.cid = org.cid ".
                 "WHERE radio.ctrl='$rid' ".
                 "AND radio.id=cmp.id ".
                 "AND cmp.cls='$cls' ".
                 "AND cmp.cid = '$cmpId' AND radio.cid = '$cmpId' ".
                 "ORDER BY cmp.name ASC, cmp.st ASC, radio.st ASC ";
        }
        $res = mysql_query($sql);
        $results = calculeStart($res);
        $res2 = mysql_query($sql2);
        $results2 = calculeStart($res2);  
        
        $results3 = null;
        foreach($results as $i => $v)
        {
            $results3[] = array($results[$i]['start_time'], $results[$i]['name'], $results[$i]['team'], $results2[$i]['name'], $results2[$i]['team'], $results2[$i]['start_time']);
        }
        formatResult($results3);
      }
    }
    else {
      // Single leg (patrol etc)        
      //$radio = selectRadio($cls);
    
     if ($radio!='') {
       if ($radio == 'finish') {
           $sql = "SELECT t.id AS id, cmp.name AS name, cmp.st, t.name AS team, t.rt AS time, t.stat AS status ".
                  "FROM mopteammember tm, mopcompetitor cmp, mopteam t ".
                  "WHERE t.cls = '$cls' AND t.id = tm.id AND tm.rid = cmp.id AND tm.leg=1 ".
                  "AND t.cid = '$cmpId' AND tm.cid = '$cmpId' AND cmp.cid = '$cmpId' ORDER BY cmp.st ASC, t.stat, t.rt ASC, t.id";
           $sql2 = "SELECT t.id AS id, cmp.name AS name, cmp.st, t.name AS team, t.rt AS time, t.stat AS status ".
                  "FROM mopteammember tm, mopcompetitor cmp, mopteam t ".
                  "WHERE t.cls = '$cls' AND t.id = tm.id AND tm.rid = cmp.id AND tm.leg=1 ".
                  "AND t.cid = '$cmpId' AND tm.cid = '$cmpId' AND cmp.cid = '$cmpId' ORDER BY cmp.name ASC, cmp.st ASC, t.stat, t.rt ASC, t.id";
           $rname = $lang["finish"];
         }
       else {
         $rid = (int)$radio;
         $sql = "SELECT name FROM mopcontrol WHERE cid='$cmpId' AND id='$rid'";
         $res = mysql_query($sql);
         $rinfo = mysql_fetch_array($res);
         $rname = $rinfo['name'];
                    
         $sql = "SELECT team.id AS id, cmp.name AS name, cmp.st, team.name AS team, radio.rt AS time, 1 AS status ".
                 "FROM mopradio AS radio, mopteammember AS m, mopteam AS team, mopcompetitor AS cmp ".
                 "WHERE radio.ctrl='$rid' ".
                 "AND radio.id=cmp.id ".
                 "AND m.rid = radio.id ".
                 "AND m.id = team.id ".
                 "AND m.leg=1 ".
                 "AND cmp.cls='$cls' ".
                 "AND radio.cid = '$cmpId' AND m.cid = '$cmpId' AND team.cid = '$cmpId' AND cmp.cid = '$cmpId' ".
                 "ORDER BY cmp.st ASC, radio.rt ASC ";
         $sql2 = "SELECT team.id AS id, cmp.name AS name, cmp.st, team.name AS team, radio.rt AS time, 1 AS status ".
                 "FROM mopradio AS radio, mopteammember AS m, mopteam AS team, mopcompetitor AS cmp ".
                 "WHERE radio.ctrl='$rid' ".
                 "AND radio.id=cmp.id ".
                 "AND m.rid = radio.id ".
                 "AND m.id = team.id ".
                 "AND m.leg=1 ".
                 "AND cmp.cls='$cls' ".
                 "AND radio.cid = '$cmpId' AND m.cid = '$cmpId' AND team.cid = '$cmpId' AND cmp.cid = '$cmpId' ".
                 "ORDER BY cmp.name ASC, cmp.st ASC, radio.rt ASC ";
       }
                
       $res = mysql_query($sql);
       $results = calculeStart($res);
	   $res2 = mysql_query($sql2);
       $results2 = calculeStart($res2);         
       $results3 = null;
        foreach($results as $i => $v)
        {
            $results3[] = array($results[$i]['start_time'], $results[$i]['name'], $results[$i]['team'], $results2[$i]['name'], $results2[$i]['team'], $results2[$i]['start_time']);
        }
        formatResult($results3);
      }
    }
  }
?>
