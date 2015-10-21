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
  
  $arr_radio = array();
  $sql = 'SELECT * FROM mopclasscontrol WHERE cid ='.$cmpId.' AND id='.$cls.' AND leg='.$leg.' ORDER BY ord ASC';
  $res = mysql_query($sql);
  if(mysql_num_rows($res) > 0)
  {
    while($r = mysql_fetch_array($res))
    {
        $arr_radio[] = $r['ctrl'];
    }
  }
  
  $nb_radio = 3;
  if($arr_radio != null)
  {
    if(count($arr_radio) > $nb_radio)
    {
        $arr_radio = array_slice($arr_radio, 0, $nb_radio);
    }
  }
  
  $numlegs = 3;
  $sql = "SELECT COUNT(leg) AS nblegs FROM mopTeamMember tm, mopTeam t ".
            "WHERE t.cls = '$cls' ".
            "AND t.cid = '$cmpId' ".
            "AND tm.cid = '$cmpId' ".
            "AND tm.id = t.id ".
            " GROUP BY tm.id";
  $res = mysql_query($sql);
  $r = mysql_fetch_array($res);
  $numlegs = $r['nblegs'];
  

  if ($numlegs > 1)
  {
    
    if ($radio!='') {
      if ($radio == 'finish') {
        $results = array();
        $arr_tempsrelais = array('radio0' => '', 'radio1' => '', 'radio2' => '', 'finish' => '', 'cumul' => '', 'place' => '', 'stat' => '', 'tstat' => '');
        $sql = "SELECT id AS team_id, cls, name AS team_name, stat AS team_stat ".
               "FROM mopTeam ".
               "WHERE cls = '$cls' ".
               "AND cid = '$cmpId' ".
               "ORDER BY id ASC";
        $rname = $lang["finish"];
        $res = mysql_query($sql);
        if(mysql_num_rows($res))
        {
            $relais_out = array();
            while($d = mysql_fetch_array($res))
            {
                $relais_out[$d['team_id']] = array("team_id" => $d['team_id'], "team_name" => $d['team_name'], "team_stat" => $d['team_stat'], "team_place" => '', "timestamp" => 0, 
                    "relais1" => $arr_tempsrelais, "relais2" => $arr_tempsrelais, "relais3" => $arr_tempsrelais);
            }
            
            $sql = "SELECT tm.id AS team_id, tm.leg, r.rt AS radio_time, r.timestamp AS radio_timestamp, cc.ord, c.timestamp, c.stat, c.rt, c.it, c.tstat ".
                        "FROM mopTeamMember AS tm, mopradio AS r, mopclasscontrol AS cc, mopcompetitor AS c ".
                        "WHERE tm.cid='$cmpId' ".
                        "AND tm.id IN (".implode(', ', array_keys($relais_out)).") ".
                        "AND r.cid='$cmpId' ".
                        "AND r.id=tm.rid ".
                        "AND cc.cid='$cmpId' ".
                        "AND cc.id='$cls' ".
                        "AND cc.leg=tm.leg ".
                        "AND cc.ctrl=r.ctrl ".
                        "AND c.cid='$cmpId' ".
                        "AND c.id=tm.rid ".
                        "AND c.cls='$cls' ".
                        "ORDER BY tm.leg ASC";
            $res = mysql_query($sql);
            if(mysql_num_rows($res))
            {
                while($d = mysql_fetch_array($res))
                {
                    $relais_out[$d['team_id']]['relais'.$d['leg']]['radio'.$d['ord']] = $d['radio_time'];
                    $relais_out[$d['team_id']]['relais'.$d['leg']]['finish'] = $d['rt'];
                    $relais_out[$d['team_id']]['relais'.$d['leg']]['cumul'] = $d['rt'] + $d['it'];
                    $relais_out[$d['team_id']]['relais'.$d['leg']]['stat'] = $d['stat'];
                    $relais_out[$d['team_id']]['relais'.$d['leg']]['tstat'] = $d['tstat'];
                    $relais_out[$d['team_id']]['timestamp'] = max($relais_out[$d['team_id']]['timestamp'], $d['timestamp'], $d['radio_timestamp']);
                }
                
                $relais_out = ordonner_relais($relais_out, $numlegs);
                formatRelaisResult($relais_out);
            }
        }
      }
    }      
  }
?>