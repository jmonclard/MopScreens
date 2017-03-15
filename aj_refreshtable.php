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
  include_once('lang.php');
  session_start();
  
  $_SESSION['CurrentLanguage'] = isset($_SESSION['CurrentLanguage']) ? $_SESSION['CurrentLanguage'] : autoSelectLanguage(array('fr','en','sv'),'en');
  
  header('Content-type: text/html;charset=utf-8');

  $PHP_SELF = $_SERVER['PHP_SELF'];
  $link = ConnectToDB();

  $cls = ((isset($_GET['cls'])) ? $_GET['cls'] : "0");
  $cmpId = ((isset($_GET['cmpId'])) ? $_GET['cmpId'] : "0");
  $leg = ((isset($_GET['leg'])) ? $_GET['leg'] : "0");
  $ord = ((isset($_GET['ord'])) ? $_GET['ord'] : "0");
  $radio = ((isset($_GET['radio'])) ? $_GET['radio'] : "finish");
  $limit = ((isset($_GET['limit'])) ? $_GET['limit'] : "9999");
  
  $nb_radio = ((isset($_GET['nbradio'])) ? intval($_GET['nbradio']) : 15);
  
  
  $rcid = ((isset($_GET['rcid'])) ? $_GET['rcid'] : "0");
  $sid = ((isset($_GET['sid'])) ? $_GET['sid'] : "0");
  $sql = 'UPDATE resultscreen SET panel1lastrefresh='.time().' WHERE rcid='.$rcid.' AND sid='.$sid;
  mysqli_query($link, $sql);

  
  $arr_radio = array();
  $sql = 'SELECT * FROM mopclasscontrol WHERE cid ='.$cmpId.' AND id='.$cls.' AND leg='.$leg.' ORDER BY ord ASC';
  $res = mysqli_query($link, $sql);
  if(mysqli_num_rows($res) > 0)
  {
    while($r = mysqli_fetch_array($res))
    {
        $arr_radio[] = $r['ctrl'];
    }
  }
  
  if($arr_radio != null)
  {
    if(count($arr_radio) > $nb_radio)
    {
        $arr_radio = array_slice($arr_radio, 0, $nb_radio);
    }
  }
  $numlegs = null;
  

  if ($numlegs > 1) {
    for ($k = 1; $k <= $numlegs; $k++) {
      $sql = "SELECT max(ord) FROM mopteammember tm, mopteam t WHERE t.cls = '$cls' AND tm.leg=$k AND ".
              "tm.cid = '$cmpId' AND t.cid = '$cmpId' AND tm.id = t.id";
      $res = mysqli_query($link, $sql);
      $r = mysqli_fetch_array($res);
      $numparallel = $r[0];
      
      if ($numparallel == 0) {
        //selectLegRadio($cls, $k, 0); // cant display links here
      }
    }
    
    if ($radio!='') {
      if ($radio == 'finish') {
        $sql = "SELECT t.id AS id, cmp.name AS name, t.name AS team, cmp.rt AS time, cmp.timestamp, cmp.stat AS status, ".
               "cmp.it+cmp.rt AS tottime, cmp.tstat AS totstat ".
               "FROM mopteammember tm, mopcompetitor cmp, mopteam t ".
               "WHERE t.cls = '$cls' AND t.id = tm.id AND tm.rid = cmp.id ".
               "AND t.cid = '$cmpId' AND tm.cid = '$cmpId' AND cmp.cid = '$cmpId' ".
               "AND ((t.stat>0) OR ((t.stat=0) AND ((SELECT COUNT(*) FROM mopradio AS mr WHERE mr.cid='$cmpId' AND tm.rid=mr.id) > 0)))".
               "AND tm.leg='$leg' AND tm.ord='$ord' ORDER BY t.stat ASC, cmp.rt ASC, t.id";
        $rname = "Finish";
        $res = mysqli_query($link, $sql);
        $results = newCalculateResult($res, $nb_radio);
      }
      /*else*/
        if($arr_radio != null)
        {
            $rid = implode(', ', $arr_radio);
   
            $sql = "SELECT team.id AS id, cmp.name AS name, team.name AS team, radio.rt AS time, radio.timestamp, 1 AS status, ".
                 "cmp.it+radio.rt AS tottime, cmp.tstat AS totstat ".
                 "FROM mopradio AS radio, mopteammember AS m, mopteam AS team, mopcompetitor AS cmp ".
                 "WHERE radio.ctrl IN(".$rid.") ".
                 "AND radio.id=cmp.id ".
                 "AND m.rid = radio.id ".
                 "AND m.id = team.id ".
                 /*"AND cmp.stat<=1 ".*/
                 "AND m.leg='$leg' AND m.ord='$ord' ".
                 "AND cmp.cls='$cls' ".
                 "AND team.cid = '$cmpId' AND m.cid = '$cmpId' AND cmp.cid = '$cmpId' ".
                 "ORDER BY radio.rt ASC ";
            $res = mysqli_query($link, $sql);
            $results = addRadioResult($res, $results);
      }
      formatResultScreen($results, $limit);
    }      
  }
  else {
    if (is_null($numlegs)) {
      //No teams;        
      if ($radio!='') {
        if ($radio == 'finish') {
            $sql = "SELECT cmp.id AS id, cmp.timestamp, cmp.name AS name, org.name AS team, cmp.rt AS time, cmp.stat AS status ".
                 "FROM mopcompetitor cmp LEFT JOIN moporganization AS org ON cmp.org = org.id AND cmp.cid = org.cid ".
                 "WHERE cmp.cls = '$cls' ".
                 "AND cmp.cid = '$cmpId' ".
                 "AND ((cmp.stat>0) OR ((cmp.stat=0) AND ((SELECT COUNT(*) FROM mopradio AS mr WHERE mr.cid='$cmpId' AND cmp.id=mr.id) > 0)))".
                 "ORDER BY FIELD(cmp.stat, 1) DESC, cmp.stat ASC, cmp.rt ASC, cmp.id";
            $rname = "Finish";
          
            $res = mysqli_query($link, $sql);
            $results = newCalculateResult($res, $nb_radio);
            
        }
        /*else*/
        if($arr_radio != null)
        {
            $rid = implode(', ', $arr_radio);
            
            $sql = "SELECT cmp.id AS id, cmp.name AS name, org.name AS team, radio.ctrl, radio.timestamp, radio.rt AS time, 1 AS status ".
                 "FROM mopradio AS radio, mopcompetitor AS cmp ".
                 "LEFT JOIN moporganization AS org ON cmp.org = org.id AND cmp.cid = org.cid ".
                 "WHERE radio.ctrl IN(".$rid.") ".
                 "AND radio.id=cmp.id ".
                 /*"AND cmp.stat<=1 ".*/
                 "AND cmp.cls='$cls' ".
                 "AND cmp.cid = '$cmpId' AND radio.cid = '$cmpId' ".
                 "ORDER BY radio.id ASC, radio.rt ASC ";
            $res = mysqli_query($link, $sql);
            $results = addRadioResult($res, $results);
        }
        formatResultScreen($results, $limit); 
      }
    }
    else {
      // Single leg (patrol etc)        
    
     if ($radio!='') {
       if ($radio == 'finish') {
           $sql = "SELECT t.id AS id, cmp.name AS name, cmp.timestamp, t.name AS team, t.rt AS time, t.stat AS status ".
                  "FROM mopteammember tm, mopcompetitor cmp, mopteam t ".
                  "WHERE t.cls = '$cls' AND t.id = tm.id AND tm.rid = cmp.id AND tm.leg=1 ".
                  "AND t.cid = '$cmpId' AND tm.cid = '$cmpId' AND cmp.cid = '$cmpId' ".
                  "AND ((t.stat>0) OR ((t.stat=0) AND ((SELECT COUNT(*) FROM mopradio AS mr WHERE mr.cid='$cmpId' AND tm.rid=mr.id) > 0)))".
                  "ORDER BY t.stat ASC, t.rt ASC, t.id";
            $rname = "Finish";
           
            $res = mysqli_query($link, $sql);
            $results = newCalculateResult($res, $nb_radio);
         }
       /*else*/
        if($arr_radio != null)
        {
            $rid = implode(', ', $arr_radio);
            $sql = "SELECT team.id AS id, cmp.name AS name, team.name AS team, radio.rt AS time, radio.timestamp, 1 AS status ".
                 "FROM mopradio AS radio, mopteammember AS m, mopteam AS team, mopcompetitor AS cmp ".
                 "WHERE radio.ctrl IN(".$rid.") ".
                 "AND radio.id=cmp.id ".
                 "AND m.rid = radio.id ".
                 "AND m.id = team.id ".
                 "AND m.leg=1 ".
                 "AND cmp.cls='$cls' ".
                 "AND radio.cid = '$cmpId' AND m.cid = '$cmpId' AND team.cid = '$cmpId' AND cmp.cid = '$cmpId' ".
                 "ORDER BY radio.id ASC, radio.rt ASC ";
            $res = mysqli_query($link, $sql);
            $results = addRadioResult($res, $results);
       }
       
       formatResultScreen($results, $limit);
      }
    }
  }
?>
