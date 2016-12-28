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
  
  $nb_radio = 3;
  if($arr_radio != null)
  {
    if(count($arr_radio) > $nb_radio)
    {
        $arr_radio = array_slice($arr_radio, 0, $nb_radio);
    }
  }
  
  $numlegs = 3;
  $sql = "SELECT COUNT(leg) AS nblegs FROM mopteammember tm, mopteam t ".
            "WHERE t.cls = '$cls' ".
            "AND t.cid = '$cmpId' ".
            "AND tm.cid = '$cmpId' ".
            "AND tm.id = t.id ".
            " GROUP BY tm.id";
  $res = mysqli_query($link, $sql);
  $r = mysqli_fetch_array($res);
  $numlegs = $r['nblegs'];
  
  $sql = 'SELECT panel1tm_count FROM resultscreen WHERE rcid='.$rcid.' AND sid='.$sid;
  $res = mysqli_query($link, $sql);
  if(mysqli_num_rows($res) > 0)
  {
    $r = mysqli_fetch_array($res);
    $numlegs = max($numlegs, $r['panel1tm_count']);
  }

  if ($numlegs > 1)
  {
    
    if ($radio!='') {
      if ($radio == 'finish') {
        $results = array();
        $arr_relaytimes = array('radio0' => '', 'radio1' => '', 'radio2' => '', 'finish' => '', 'cumul' => '', 'place' => '', 'stat' => '', 'tstat' => '');
        $sql = "SELECT id AS team_id, cls, name AS team_name, stat AS team_stat ".
               "FROM mopteam ".
               "WHERE cls = '$cls' ".
               "AND cid = '$cmpId' ".
               "ORDER BY id ASC";
        $rname = "Finish";
        $res = mysqli_query($link, $sql);
        if(mysqli_num_rows($res))
        {
            $relay_out = array();
            while($d = mysqli_fetch_array($res))
            {
                $relay_out[$d['team_id']] = array("team_id" => $d['team_id'], "team_name" => $d['team_name'], "team_stat" => $d['team_stat'], "team_place" => '', "timestamp" => 0, 
                    "relay1" => $arr_relaytimes, "relay2" => $arr_relaytimes, "relay3" => $arr_relaytimes, "relay4" => $arr_relaytimes, "relay5" => $arr_relaytimes, 
                    "relay6" => $arr_relaytimes, "relay7" => $arr_relaytimes, "relay8" => $arr_relaytimes, "relay9" => $arr_relaytimes, "relay10" => $arr_relaytimes);
            }
	    
	    $sql = "SELECT 1 ".
                        "FROM mopclasscontrol AS cc ".
                        "WHERE cc.cid='$cmpId' ".
                        "AND cc.id='$cls' ";
            $resexist = mysqli_query($link, $sql);
            if(mysqli_num_rows($resexist))
	    {
		$sql = "SELECT tm.id AS team_id, tm.leg, r.rt AS radio_time, r.timestamp AS radio_timestamp, cc.ord, c.timestamp, c.stat, c.rt, c.it, c.tstat, c.name ".
                        "FROM mopteammember AS tm, mopradio AS r, mopclasscontrol AS cc, mopcompetitor AS c ".
                        "WHERE tm.cid='$cmpId' ".
                        "AND tm.id IN (".implode(', ', array_keys($relay_out)).") ".
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
		    $res = mysqli_query($link, $sql);
		    if(mysqli_num_rows($res))
		    {
			while($d = mysqli_fetch_array($res))
			{
			    $relay_out[$d['team_id']]['relay'.$d['leg']]['radio'.$d['ord']] = $d['radio_time'];
			    $relay_out[$d['team_id']]['relay'.$d['leg']]['finish'] = $d['rt'];
			    if($d['rt'])
          {
            $relay_out[$d['team_id']]['relay'.$d['leg']]['cumul'] = $d['rt'] + $d['it'];
          }
			    $relay_out[$d['team_id']]['relay'.$d['leg']]['stat'] = $d['stat'];
			    $relay_out[$d['team_id']]['relay'.$d['leg']]['tstat'] = $d['tstat'];
			    $relay_out[$d['team_id']]['relay'.$d['leg']]['name'] = addslashes($d['name']);
			    $relay_out[$d['team_id']]['timestamp'] = max($relay_out[$d['team_id']]['timestamp'], $d['timestamp'], $d['radio_timestamp']);
			}
			
			$relay_out = reorder_relay($relay_out, $numlegs);
			formatRelayResults($relay_out, $limit);
		    }
	    }
	    else
	    {
		$sql = "SELECT tm.id AS team_id, tm.leg, c.timestamp, c.stat, c.rt, c.it, c.tstat, c.name ".
                        "FROM mopteammember AS tm, mopcompetitor AS c ".
                        "WHERE tm.cid='$cmpId' ".
                        "AND tm.id IN (".implode(', ', array_keys($relay_out)).") ".
                        "AND c.cid='$cmpId' ".
                        "AND c.id=tm.rid ".
                        "AND c.cls='$cls' ".
                        "ORDER BY tm.leg ASC";
		    $res = mysqli_query($link, $sql);
		    if(mysqli_num_rows($res))
		    {
			while($d = mysqli_fetch_array($res))
			{
			    $relay_out[$d['team_id']]['relay'.$d['leg']]['finish'] = $d['rt'];
          if($d['rt'])
          {
            $relay_out[$d['team_id']]['relay'.$d['leg']]['cumul'] = $d['rt'] + $d['it'];
          }
			    $relay_out[$d['team_id']]['relay'.$d['leg']]['stat'] = $d['stat'];
			    $relay_out[$d['team_id']]['relay'.$d['leg']]['tstat'] = $d['tstat'];
			    $relay_out[$d['team_id']]['relay'.$d['leg']]['name'] = addslashes($d['name']);
			    $relay_out[$d['team_id']]['timestamp'] = max($relay_out[$d['team_id']]['timestamp'], $d['timestamp']);
			}
			
			$relay_out = reorder_relay($relay_out, $numlegs);
			formatRelayResults($relay_out, $limit);
		    }
	    }
            
            
        }
      }
    }      
  }
?>