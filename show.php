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

  //================================================================
  $rogainingmode = true; // TODO remplacer true par false
  $nbjours = 200;        // TODO remplacer par 7
  //================================================================

	include_once('functions.php');
	session_start();
  header('Content-type: text/html;charset=utf-8');
  
  $PHP_SELF = $_SERVER['PHP_SELF'];
  $link = ConnectToDB();

  if (isset($_GET['cmp']))
    $_SESSION['competition'] = 1 * (int)$_GET['cmp'];

  $cmpId = 0;
  if(isset($_SESSION['competition']))
    $cmpId = $_SESSION['competition'];

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>MeOS Online Results</title>

<style type="text/css">
body {
  font-family: verdana, arial, sans-serif;
  font-size: 9pt;
  background-color: #FFFFFF;
}

a.button {
  border-style: ridge;
  border-color: #b0c4de;
  background-color:#b0c4de;
  color: #900000;
  text-decoration: none;
  padding: 0.1em 0.3em;
  margin: 1em;
}

h1 {text-shadow: 3px 3px 3px #AAAAAA;}
th {text-align:left;}
td {padding-right:1em;}
</style>
</head>
<body>

<?php
  $select = (isset($_GET['select']) ? intval($_GET['select']) : 0);
  if ($select == 1 || $cmpId == 0) {
    print "<h1>$lang[selectcmp]</h1>";
    $date_now = time();
    $date_lastweek = $date_now - 20*24*3600; // TODO replace 20 par 7
    $sql_lastweek = date('Y-m-d', $date_lastweek);
    $sql = "SELECT name, date, cid FROM mopcompetition WHERE date>='".$sql_lastweek."' ORDER BY date DESC";
    $res = mysqli_query($link, $sql);
    
    while ($r = mysqli_fetch_array($res)) {
      print '<a href="'."$PHP_SELF?cmp=$r[cid]".'">'."$r[name] ($r[date])</a><br/>\n";      
    }
    die('</body></html>');    
  }

  $sql = "SELECT * FROM mopcompetition WHERE cid = '$cmpId'"; 
  $res = mysqli_query($link, $sql);

  if ($r = mysqli_fetch_array($res)) {
    print "<h1>$r[name] &ndash; $r[date]</h1>\n";
    
    if (strlen($r['organizer']) > 0) {
      if (strlen($r['homepage'])>0)
        print '<a href="'.$r['homepage'].'">'.$r['organizer'].'</a><br>';
      else
        print $r['organizer'].'<br>';      
    }     
  }

  print '<br><div style="clear:both;"><a href="'.$PHP_SELF.'?select=1" class="button">'.$lang['selectcmp'].'</a></div>';

  print '<div style="float:left;margin:2em;">';  
  $sql = "SELECT name, id FROM mopclass WHERE cid = '$cmpId' ORDER BY ord";     
  $res = mysqli_query($link, $sql);
  
  while ($r = mysqli_fetch_array($res)) {
    print '<a href="'."$PHP_SELF?cls=$r[id]".'">'.$r['name']."</a><br/>\n";      
  }

  print '</div><div style="float:left;">';
  
  if (isset($_GET['cls'])) {
    $cls = (int)$_GET['cls']; 
    $sql = "SELECT name FROM mopclass WHERE cid='$cmpId' AND id='$cls'";
    $res = mysqli_query($link, $sql);
    $cinfo = mysqli_fetch_array($res);
    $cname = $cinfo['name'];
    
    $sql = "SELECT max(leg) FROM mopteammember tm, mopteam t WHERE tm.cid = '$cmpId' AND t.cid = '$cmpId' AND tm.id = t.id AND t.cls = $cls";
    $res = mysqli_query($link, $sql);
    $r = mysqli_fetch_array($res);
    $numlegs = $r[0];
    print "<h2>$cname</h2>\n";

    if ($numlegs > 1) {
      //Multiple legs, relay etc. 
      if (isset($_GET['leg'])) {
        $leg = (int)$_GET['leg'];
      }
      if (isset($_GET['ord'])) {
        $ord = (int)$_GET['ord'];
      }
      if (isset($_GET['radio'])) {
        $radio = $_GET['radio'];
      }
      for ($k = 1; $k <= $numlegs; $k++) {
        $sql = "SELECT max(ord) FROM mopteammember tm, mopteam t WHERE t.cls = '$cls' AND tm.leg=$k AND ".
                "tm.cid = '$cmpId' AND t.cid = '$cmpId' AND tm.id = t.id";
        $res = mysqli_query($link, $sql);
        $r = mysqli_fetch_array($res);
        $numparallel = $r[0];
        
        if ($numparallel == 0) {
          print "$k: ";
          selectLegRadio($cls, $k, 0);
        }
      }
      
      if ($radio!='') {
        if ($radio == 'finish') {
          $sql = "SELECT t.id AS id, cmp.name AS name, t.name AS team, cmp.rt AS time, cmp.stat AS status, ".
                 "cmp.it+cmp.rt AS tottime, cmp.tstat AS totstat ".
                 "FROM mopteammember tm, mopcompetitor cmp, mopteam t ".
                 "WHERE t.cls = '$cls' AND t.id = tm.id AND tm.rid = cmp.id ".
                 "AND t.cid = '$cmpId' AND tm.cid = '$cmpId' AND cmp.cid = '$cmpId' AND t.stat>0 ".
                 "AND tm.leg='$leg' AND tm.ord='$ord' ORDER BY cmp.stat, cmp.rt ASC, t.id";
          $rname = $lang["finish"];
        }
        else {
          $rid = (int)$radio;
          $sql = "SELECT name FROM mopcontrol WHERE cid='$cmpId' AND id='$rid'";
          $res = mysqli_query($link, $sql);
          $rinfo = mysqli_fetch_array($res);
          $rname = $rinfo['name'];
     
          $sql = "SELECT team.id AS id, cmp.name AS name, team.name AS team, radio.rt AS time, 1 AS status, ".
                   "cmp.it+radio.rt AS tottime, cmp.tstat AS totstat ".
                   "FROM mopradio AS radio, mopteammember AS m, mopteam AS team, mopcompetitor AS cmp ".
                   "WHERE radio.ctrl='$rid' ".
                   "AND radio.id=cmp.id ".
                   "AND m.rid = radio.id ".
                   "AND m.id = team.id ".
                   "AND cmp.stat<=1 ".
                   "AND m.leg='$leg' AND m.ord='$ord' ".
                   "AND cmp.cls='$cls' ".
                   "AND team.cid = '$cmpId' AND m.cid = '$cmpId' AND cmp.cid = '$cmpId' ".
                   "ORDER BY radio.rt ASC ";
        }

        $res = mysqli_query($link, $sql);
        $results = calculateResult($res);
        print "<h3>Leg $leg, $rname</h3>\n";
        formatResult($results);
      }      
    }
    else {

      if (is_null($numlegs)) {
        //No teams;        
        $radio = selectRadio($cls);              
        if ($radio!='') {
          if ($radio == 'finish') {
	    if ($rogainingmode ) {
	      $sql = "SELECT cmp.id AS id, cmp.timestamp, cmp.name AS name, cmp.country, org.name AS team, cmp.rt AS time, cmp.stat AS status, cmp.rogpoints AS pts, cmp.rogpointsgross AS ptsgross ".
                     "FROM mopcompetitor cmp LEFT JOIN moporganization AS org ON cmp.org = org.id AND cmp.cid = org.cid ".
		     "WHERE cmp.cls = '$cls' ".
		     "AND cmp.cid = '$cmpId' ".
		     "AND ((cmp.stat>0) OR ((cmp.stat=0) AND ((SELECT COUNT(*) FROM mopradio AS mr WHERE mr.cid='$cmpId' AND cmp.id=mr.id) > 0)))".
		     "ORDER BY FIELD(cmp.stat,1) DESC, cmp.stat ASC, cmp.rogpoints DESC, cmp.rt ASC, cmp.id";
	     $rname = $lang["finish"];
	    }
	    else {
            $sql = "SELECT cmp.id AS id, cmp.name AS name, org.name AS team, cmp.rt AS time, cmp.stat AS status ".
                   "FROM mopcompetitor cmp LEFT JOIN moporganization AS org ON cmp.org = org.id AND cmp.cid = org.cid ".
                   "WHERE cmp.cls = '$cls' ".
                   "AND cmp.cid = '$cmpId' AND cmp.stat>0 ORDER BY cmp.stat, cmp.rt ASC, cmp.id";
            $rname = $lang["finish"];
          }
          }
          else {
            $rid = (int)$radio;
            $sql = "SELECT name FROM mopcontrol WHERE cid='$cmpId' AND id='$rid'";
            $res = mysqli_query($link, $sql);
            $rinfo = mysqli_fetch_array($res);
            $rname = $rinfo['name'];
                      
            $sql = "SELECT cmp.id AS id, cmp.name AS name, org.name AS team, radio.rt AS time, 1 AS status ".
                   "FROM mopradio AS radio, mopcompetitor AS cmp ".
                   "LEFT JOIN moporganization AS org ON cmp.org = org.id AND cmp.cid = org.cid ".
                   "WHERE radio.ctrl='$rid' ".
                   "AND radio.id=cmp.id ".
                   "AND cmp.stat<=1 ".
                   "AND cmp.cls='$cls' ".
                   "AND cmp.cid = '$cmpId' AND radio.cid = '$cmpId' ".
                   "ORDER BY radio.rt ASC ";
          }
          $res = mysqli_query($link, $sql);
	        if ($rogainingmode) {
            $results = CalculateWiFiRogainingResult($res);
          }
	        else {
          $results = calculateResult($res);
          }
          print "<h3>$rname</h3>\n";
          formatResult($results); 
        }
      }
      else {
        // Single leg (patrol etc)        
        $radio = selectRadio($cls);
      
       if ($radio!='') {
         if ($radio == 'finish') {
             $sql = "SELECT t.id AS id, cmp.name AS name, t.name AS team, t.rt AS time, t.stat AS status ".
                    "FROM mopteammember tm, mopcompetitor cmp, mopteam t ".
                    "WHERE t.cls = '$cls' AND t.id = tm.id AND tm.rid = cmp.id AND tm.leg=1 ".
                    "AND t.cid = '$cmpId' AND tm.cid = '$cmpId' AND cmp.cid = '$cmpId' AND t.stat>0 ORDER BY t.stat, t.rt ASC, t.id";
             $rname = $lang["finish"];
           }
         else {
           $rid = (int)$radio;
           $sql = "SELECT name FROM mopcontrol WHERE cid='$cmpId' AND id='$rid'";
           $res = mysqli_query($link, $sql);
           $rinfo = mysqli_fetch_array($res);
           $rname = $rinfo['name'];
                      
           $sql = "SELECT team.id AS id, cmp.name AS name, team.name AS team, radio.rt AS time, 1 AS status ".
                   "FROM mopradio AS radio, mopteammember AS m, mopteam AS team, mopcompetitor AS cmp ".
                   "WHERE radio.ctrl='$rid' ".
                   "AND radio.id=cmp.id ".
                   "AND m.rid = radio.id ".
                   "AND m.id = team.id ".
                   "AND cmp.stat<=1 ".
                   "AND m.leg=1 ".
                   "AND cmp.cls='$cls' ".
                   "AND radio.cid = '$cmpId' AND m.cid = '$cmpId' AND team.cid = '$cmpId' AND cmp.cid = '$cmpId' ".
                   "ORDER BY radio.rt ASC ";
         }
                  
         $res = mysqli_query($link, $sql);
         $results = calculateResult($res);         
         print "<h3>$rname</h3>\n";
         formatResult($results);
        }
      }
    }   
  }
  print '</div>';
?> 
<!--<div style="clear:both;padding-top:3em;color: grey;">
 Results provided by <a href="http://www.melin.nu/meos" target="_blank">MeOS Online Results</a>.
</div>-->
</body></html>
