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

function getStatusString($status, $origin = false) {
	if($origin)
	{
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
  else
  {
		  switch($status) {
		    case 0: 
		      return "&ndash;"; //Unknown, running?
		    case 1:
		      return "OK";
		    case 20:
		      return "Non par"; // Did not start;
		    case 3:
		      return "P.M."; // Missing punch
		    case 4:
		      return "Aband."; //Did not finish
		    case 5:
		      return "Disq."; // Disqualified
		    case 6:      
		      return "Hors D."; // Overtime
		    case 99:
		      return "Absent"; //Not participating;
		  }

  }
}

$global_out = array();
$global_out2 = array();
$global_out3 = array();
$global_out4 = array();

function calculateResult($res, $nb_radio = 4)
{

  global $global_out;
  global $global_out2;
  global $global_out3;
  global $global_out4;
    
  $out = array();  
  $global_out = array();
  $global_out2 = array();
  $global_out3 = array();
  $global_out4 = array();
  $temp_out = array();
  
  $place = 0;
  $count = 0;
  $lastTime = -1;
  $bestTime = -1;
  $lastTeam = -1;
  $totalResult = array();
  $hasTotal = false;

  while ($r = mysql_fetch_array($res))
  {
    if ($lastTeam == $r['id'])
    {
      $out[$count]['name'] .= " / " . $r['name'];
      continue; 
    }
    else
    {
      $lastTeam = $r['id'];
    }
      
    $count++;
    $t = $r['time']/10;
    if ($bestTime == -1)
      $bestTime = $t;
    if ($lastTime != $t)
    {
      $place = $count;
      $lastTime = $t;
    }        
    $row = array();
    
    if ($r['status'] == 1) {
      $row['st'] = $r['status'];
      $row['timestamp'] = $r['timestamp'];
      $row['place'] = $place;//.".";
      $row['name'] = $r['name'];      
      $row['team'] = $r['team'];
      for($i=0;$i<$nb_radio;$i++)
      {
        $row['radio'.$i] = '';//sprintf("%d:%02d:%02d", $t/3600, ($t/60)%60, $t%60);
      }
    
    if ($t >= 3600)
        $row['time'] = sprintf("%d:%02d:%02d", $t/3600, ($t/60)%60, $t%60);
    else
      if ($t > 0)
        $row['time'] = sprintf("%02d:%02d", ($t/60), $t%60);
      else
        $row['time'] = "OK"; // No timing
        
      $after = $t - $bestTime;
      
      if ($after > 0)
        $row['after'] = sprintf("+%d:%02d", ($after/60), $after%60);        
      else
        $row['after'] = "";
    }
    else
    {
      $row['st'] = $r['status'];
      $row['timestamp'] = $r['timestamp'];
      $row['place'] = "";
      $row['name'] = $r['name'];      
      $row['team'] = $r['team'];
      
      for($i=0;$i<$nb_radio;$i++)
      {
        $row['radio'.$i] = '';//sprintf("%d:%02d:%02d", $t/3600, ($t/60)%60, $t%60);
      }
    
      $row['time'] = getStatusString($r['status']);
      $row['after'] = "";
    }
          
    if (isset($r['tottime']))
    {
      $hasTotal = true;
      if ($r['totstat'] == 1)
      {
        $tt = $r['tottime']/10;          
        if ($tt > 0)
          $row['tottime'] = sprintf("%d:%02d:%02d", $tt/3600, ($tt/60)%60, $tt%60);
        else
          $row['tottime'] = "OK"; // No timing
      }
      else
      {
        $row['tottime'] = getStatusString($r['totstat']); 
      }
      
      if ($r['totstat'] > 0)
        $totalResult[$count] = ($r['totstat']-1) * 10000000 + $r['tottime'];
      else
        $totalResult[$count] = 10000000 * 100;
    }
    
    $out[$count] = $row;
    $row['id'] = $r['id'];
    $row['status'] = $r['status'];
    $row['tt'] = $t;
    $temp_out[$count] = $row;
    $global_out[$count] = $temp_out[$count]['id'];
    $global_out2[$count] = $temp_out[$count]['status'];
    if($temp_out[$count]['status'] > 1)
    {
        $global_out3[$count] = $temp_out[$count]['id'];
    }
    $global_out4[$count] = $temp_out[$count]['tt'];
  }
  return $out;
}

function addRadioResult($res, $results)
{
  global $arr_radio;
  global $global_out;
  global $global_out2;
  global $global_out3;
  global $global_out4;
  
  $arr_radiomax = array();
  
  $out = $results;
  
  while ($r = mysql_fetch_array($res))
  {
    $key = array_search($r['id'], $global_out);
    $key_radio = array_search($r['ctrl'], $arr_radio);
    if(isset($out[$key]))
    {
      if($global_out2[$key] <= 1)
      {
        if(isset($out[$key]['radio'.$key_radio]))
        {
          $t = $r['time']/10;
          $out[$key]['radio'.$key_radio] = $t;
          
          if(!isset($arr_radiomax[$key]))
          {
              $arr_radiomax[$key] = $key_radio;
          }
          else
          {
            if($key_radio > $arr_radiomax[$key])
            {
              $arr_radiomax[$key] = $key_radio;
            }
          }
        }
        if($out[$key]['timestamp'] < $r['timestamp'])
        {
          $out[$key]['timestamp'] = $r['timestamp'];
        }
      }
      else
      {
       	if(isset($out[$key]['radio'.$key_radio]))
        {
          $t = $r['time']/10;
          $out[$key]['radio'.$key_radio] = $t;
          if($out[$key]['timestamp'] < $r['timestamp'])
          {
            $out[$key]['timestamp'] = $r['timestamp'];
          }
        }
	  }
    }
  }
  $out = ordonnertableau($out, $arr_radiomax);

  return $out;
}

function ordonnertableau($result, $arr_radiomax)
{
  global $arr_radio;
  global $global_out;
  global $global_out2;
  global $global_out3;
  global $global_out4;
  
  $arr_keyFinished = array_keys($global_out2, 1);
  $arr_keyRunning = array();
  $arr_timeRunning = array();
  $temp_tab = array();

  foreach($arr_radiomax as $k => $v)
  {
    if($arr_keyFinished != null)
    {
      if(!in_array($k, $arr_keyFinished))
      {
        $arr_keyRunning[$v][] = $k;
        $arr_timeRunning[$k] = $result[$k]['radio'.$v];
      }
    }
    else
    {
      $arr_keyRunning[$v][] = $k;
      $arr_timeRunning[$k] = $result[$k]['radio'.$v];
    }
  }

  foreach($arr_keyFinished as $mykey)
  {
    $temp_tab[] = $mykey;
  }
  
  for($i=(count($arr_radio) - 1);$i>=0;$i--)
  {
    if($arr_keyRunning[$i] != null)
    {
      foreach($arr_keyRunning[$i] as $mykey2)
      {
        $b = false;
        foreach($temp_tab as $k => $mykey)
        {
          if($arr_timeRunning[$mykey2] > $result[$mykey]['radio'.$i])
          {
            $b = false;
          }
          else
          {
            array_splice($temp_tab, $k, 0, array($mykey2));
            $b = true;
            break;
          }
        }
        if($b == false)
        {
            $temp_tab[] = $mykey2;
        }
      }
    }
  }
  $place = 0;
  $place_affichee = 0;
  $first = false;
  $last_res = array();
  $last_radio = 0;
  $finish_radio = max($arr_radiomax) + 1;

  foreach($temp_tab as $k => $v)
  {
    $place++;
    if(!$first)
    {
      $first = true;
      $place_affichee = $place;
      $last_res = array($result[$v]['radio0'], $result[$v]['radio1'], $result[$v]['radio2'], $result[$v]['radio3'], $global_out4[$v]);
      if($result[$v]['st'] == 1)
      {
        $last_radio = $finish_radio;
      }
      else
      {
        $last_radio = $arr_radiomax[$v];
      }
    }
    else
    {
      if($result[$v]['st'] == 1)
      {
        if($last_radio == $finish_radio)
        {
          if($global_out4[$v] != $last_res[4])
          {
            $last_res[4] = $global_out4[$v];
            $place_affichee = $place;
          }
        }
        else
        {
          if($global_out4[$v] != $last_res[4])
          {
            $last_res[4] = $global_out4[$v];
            $place_affichee = $place;
            $last_radio = $finish_radio;
          }
        }
      }
      else
	  {
        if($last_radio != $arr_radiomax[$v])
        {
          $last_radio = $arr_radiomax[$v];
          $last_res[$arr_radiomax[$v]] = $arr_timeRunning[$v];
          $place_affichee = $place;
        }
        else
        {
          if($arr_timeRunning[$v] != $last_res[$arr_radiomax[$v]])
          {
            $last_res[$arr_radiomax[$v]] = $arr_timeRunning[$v];
            $place_affichee = $place;
          }
        }
      }
    }

    $result[$v]['place'] = $place_affichee;
    for($i=0;$i<4;$i++)
    {
      $t = $result[$v]['radio'.$i];
      if ($t >= 3600)
        $result[$v]['radio'.$i] = sprintf("%d:%02d:%02d", $t/3600, ($t/60)%60, $t%60);
      else
        if ($t > 0)
          $result[$v]['radio'.$i] = sprintf("%02d:%02d", ($t/60), $t%60);
    }
    $out[] = $result[$v];
  }

  if($global_out3 != null)
  {
    foreach($global_out3 as $k => $v)
    {
      for($i=0;$i<4;$i++)
      {
        $t = $result[$k]['radio'.$i];
        if ($t >= 3600)
        {
            $result[$k]['radio'.$i] = sprintf("%d:%02d:%02d", $t/3600, ($t/60)%60, $t%60);
        }
        else
        {
          if ($t > 0)
          {
            $result[$k]['radio'.$i] = sprintf("%02d:%02d", ($t/60), $t%60);
          }
        }
      }
      $out[] = $result[$k];
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

  function formatResultScreen($result) {
  global $pos;
  global $lang;
  $head = true;

  print '[';
  
  $i = 0;
  
  foreach($result as $row) 
  {            
  /*
    if ($head == false) 
    {
      $first = true;
      print '[';
      foreach($row as $key => $cell) 
      {
        print $first ? "" : ",";
        print '"'.$lang[$key].'"';
        $first = false;
      }
      print ']';
      $head = true; 
    } 
    */
    if ($head)
    {
        print("[");
        $head = false;
    }
    else
    {
        print(",[");
    }
    $first = true;
    foreach($row as $cell) 
    {
        print $first ? "" : ",";
        print '"'.$cell.'"';
        $first = false;
    }
      print ']';
  }
  print "];";
}

function ordonner_relais($arr, $numlegs)
{
  $out = array();
	
  foreach ($arr as $key => $val)
  {
    $rel3_tstat[$key]  = $val['relais3']['tstat'];
	$rel2_tstat[$key]  = $val['relais2']['tstat'];
	$rel1_tstat[$key]  = $val['relais1']['tstat'];
        
    $rel3_stat[$key]  = $val['relais3']['stat'];
	$rel2_stat[$key]  = $val['relais2']['stat'];
	$rel1_stat[$key]  = $val['relais1']['stat'];
        
    $sta[$key] = $val['team_stat'];
		
    if($sta[$key] <= 1)
    {
      if($val['relais3']['radio0'] > 0)
        $rel3_radio0[$key]  = $val['relais2']['cumul'] + $val['relais3']['radio0'];
      if($val['relais3']['radio1'] > 0)
        $rel3_radio1[$key] = $val['relais2']['cumul'] + $val['relais3']['radio1'];
      if($val['relais3']['radio2'] > 0)
        $rel3_radio2[$key] = $val['relais2']['cumul'] + $val['relais3']['radio2'];
      if($val['relais3']['cumul'] > 0)
        $rel3_cumul[$key]  = $val['relais3']['cumul'];
    }

    if($sta[$key] <= 1)
    {
      if($val['relais2']['radio0'] > 0)
        $rel2_radio0[$key]  = $val['relais1']['cumul'] + $val['relais2']['radio0'];
      if($val['relais2']['radio1'] > 0)
        $rel2_radio1[$key] = $val['relais1']['cumul'] + $val['relais2']['radio1'];
      if($val['relais2']['radio2'] > 0)
        $rel2_radio2[$key] = $val['relais1']['cumul'] + $val['relais2']['radio2'];
      if($val['relais2']['cumul'] > 0)
        $rel2_cumul[$key]  = $val['relais2']['cumul'];
    }

    if($sta[$key] <= 1)
    {
      if($val['relais1']['radio0'] > 0)
        $rel1_radio0[$key]  = $val['relais1']['radio0'];
      if($val['relais1']['radio1'] > 0)
        $rel1_radio1[$key] = $val['relais1']['radio1'];
      if($val['relais1']['radio2'] > 0)
        $rel1_radio2[$key] = $val['relais1']['radio2'];
      if($val['relais1']['cumul'] > 0)
        $rel1_cumul[$key]  = $val['relais1']['cumul'];
    }
  }
	
  // Ajoute $arr en tant que dernier parametre
  //array_multisort($rel3_cumul, SORT_ASC, $rel3_radio2, SORT_ASC, $rel3_radio1, SORT_ASC, $rel3_radio0, SORT_ASC, $rel2_cumul, SORT_ASC, $rel2_radio2, SORT_ASC, $rel2_radio1, SORT_ASC, $rel2_radio0, SORT_ASC, $rel1_cumul, SORT_ASC, $rel1_radio2, SORT_ASC, $rel1_radio1, SORT_ASC, $rel1_radio0, SORT_ASC, $sta, SORT_ASC, $arr);
    
  if($rel3_cumul != null) natcasesort($rel3_cumul);
  if($rel2_cumul != null) natcasesort($rel2_cumul);
  if($rel1_cumul != null) natcasesort($rel1_cumul);
  if($rel3_radio0 != null) natcasesort($rel3_radio0);
  if($rel3_radio1 != null) natcasesort($rel3_radio1);
  if($rel3_radio2 != null) natcasesort($rel3_radio2);
  if($rel2_radio0 != null) natcasesort($rel2_radio0);
  if($rel2_radio1 != null) natcasesort($rel2_radio1);
  if($rel2_radio2 != null) natcasesort($rel2_radio2);
  if($rel1_radio0 != null) natcasesort($rel1_radio0);
  if($rel1_radio1 != null) natcasesort($rel1_radio1);
  if($rel1_radio2 != null) natcasesort($rel1_radio2);

  $place_affichee = 0;
  $place_stokee = 0;
  $last_time = -1;
    
  if($rel3_cumul != null)
    foreach($rel3_cumul as $k => $v)
    {
	$place_stockee++;
	if($arr[$k]['relais3']['cumul'] != $last_time)
	{
		$place_affichee = $place_stockee;
		$last_time = $arr[$k]['relais3']['cumul'];
	}
      $relais1_affichee = '';
      $relais2_affichee = '';
      $relais3_affichee = '';
      if(array_key_exists($k, $rel1_cumul))
        $relais1_affichee = array_search($k, array_keys($rel1_cumul)) + 1;
      if(array_key_exists($k, $rel2_cumul))
        $relais2_affichee = array_search($k, array_keys($rel2_cumul)) + 1;
      if(array_key_exists($k, $rel3_cumul))
        $relais3_affichee = array_search($k, array_keys($rel3_cumul)) + 1;
  
      $out[$k] = array($arr[$k]['team_stat'], $arr[$k]['timestamp'], $arr[$k]['relais1']['tstat'], $arr[$k]['relais2']['tstat'], $arr[$k]['relais3']['tstat'], $numlegs, $place_affichee, $arr[$k]['team_name'], 
                       $arr[$k]['relais1']['radio0'], $arr[$k]['relais1']['radio1'], $arr[$k]['relais1']['radio2'], $arr[$k]['relais1']['finish'], $relais1_affichee, $arr[$k]['relais1']['cumul'],
                       $arr[$k]['relais2']['radio0'], $arr[$k]['relais2']['radio1'], $arr[$k]['relais2']['radio2'], $arr[$k]['relais2']['finish'], $relais2_affichee, $arr[$k]['relais2']['cumul'],
                       $arr[$k]['relais3']['radio0'], $arr[$k]['relais3']['radio1'], $arr[$k]['relais3']['radio2'], $arr[$k]['relais3']['finish'], $relais3_affichee, $arr[$k]['relais3']['cumul'],
		       $arr[$k]['relais1']['name'], $arr[$k]['relais2']['name'], $arr[$k]['relais3']['name']
                      );
    }
    $last_time = -1;
    if($rel3_radio2 != null)
      foreach($rel3_radio2 as $k => $v)
        {
            if(!isset($out[$k]))
            {
		$place_stockee++;
		if(($arr[$k]['relais2']['cumul'] + $arr[$k]['relais3']['radio2']) != $last_time)
		{
			$place_affichee = $place_stockee;
			$last_time = ($arr[$k]['relais2']['cumul'] + $arr[$k]['relais3']['radio2']);
		}
                $relais1_affichee = '';
                $relais2_affichee = '';
                $relais3_affichee = '';
                if(array_key_exists($k, $rel1_cumul))
                    $relais1_affichee = array_search($k, array_keys($rel1_cumul)) + 1;
                if(array_key_exists($k, $rel2_cumul))
                    $relais2_affichee = array_search($k, array_keys($rel2_cumul)) + 1;
                $out[$k] = array($arr[$k]['team_stat'], $arr[$k]['timestamp'], $arr[$k]['relais1']['tstat'], $arr[$k]['relais2']['tstat'], $arr[$k]['relais3']['tstat'], $numlegs, $place_affichee, $arr[$k]['team_name'], 
                        $arr[$k]['relais1']['radio0'], $arr[$k]['relais1']['radio1'], $arr[$k]['relais1']['radio2'], $arr[$k]['relais1']['finish'], $relais1_affichee, $arr[$k]['relais1']['cumul'],
                        $arr[$k]['relais2']['radio0'], $arr[$k]['relais2']['radio1'], $arr[$k]['relais2']['radio2'], $arr[$k]['relais2']['finish'], $relais2_affichee, $arr[$k]['relais2']['cumul'],
                        $arr[$k]['relais3']['radio0'], $arr[$k]['relais3']['radio1'], $arr[$k]['relais3']['radio2'], $arr[$k]['relais3']['finish'], $relais3_affichee, $arr[$k]['relais3']['cumul'],
		       $arr[$k]['relais1']['name'], $arr[$k]['relais2']['name'], $arr[$k]['relais3']['name']
                );
            }
        }
	$last_time = -1;
    if($rel3_radio1 != null)
        foreach($rel3_radio1 as $k => $v)
        {
            if(!isset($out[$k]))
            {
                $place_stockee++;
		if(($arr[$k]['relais2']['cumul'] + $arr[$k]['relais3']['radio1']) != $last_time)
		{
			$place_affichee = $place_stockee;
			$last_time = ($arr[$k]['relais2']['cumul'] + $arr[$k]['relais3']['radio1']);
		}
                $relais1_affichee = '';
                $relais2_affichee = '';
                $relais3_affichee = '';
                if(array_key_exists($k, $rel1_cumul))
                    $relais1_affichee = array_search($k, array_keys($rel1_cumul)) + 1;
                if(array_key_exists($k, $rel2_cumul))
                    $relais2_affichee = array_search($k, array_keys($rel2_cumul)) + 1;
                $out[$k] = array($arr[$k]['team_stat'], $arr[$k]['timestamp'], $arr[$k]['relais1']['tstat'], $arr[$k]['relais2']['tstat'], $arr[$k]['relais3']['tstat'], $numlegs, $place_affichee, $arr[$k]['team_name'], 
                        $arr[$k]['relais1']['radio0'], $arr[$k]['relais1']['radio1'], $arr[$k]['relais1']['radio2'], $arr[$k]['relais1']['finish'], $relais1_affichee, $arr[$k]['relais1']['cumul'],
                        $arr[$k]['relais2']['radio0'], $arr[$k]['relais2']['radio1'], $arr[$k]['relais2']['radio2'], $arr[$k]['relais2']['finish'], $relais2_affichee, $arr[$k]['relais2']['cumul'],
                        $arr[$k]['relais3']['radio0'], $arr[$k]['relais3']['radio1'], $arr[$k]['relais3']['radio2'], $arr[$k]['relais3']['finish'], $relais3_affichee, $arr[$k]['relais3']['cumul'],
		       $arr[$k]['relais1']['name'], $arr[$k]['relais2']['name'], $arr[$k]['relais3']['name']
                );
            }
        }
	$last_time = -1;
    if($rel3_radio0 != null)
        foreach($rel3_radio0 as $k => $v)
        {
            if(!isset($out[$k]))
            {
                $place_stockee++;
		if(($arr[$k]['relais2']['cumul'] + $arr[$k]['relais3']['radio0']) != $last_time)
		{
			$place_affichee = $place_stockee;
			$last_time = ($arr[$k]['relais2']['cumul'] + $arr[$k]['relais3']['radio0']);
		}
                $relais1_affichee = '';
                $relais2_affichee = '';
                $relais3_affichee = '';
                if(array_key_exists($k, $rel1_cumul))
                    $relais1_affichee = array_search($k, array_keys($rel1_cumul)) + 1;
                if(array_key_exists($k, $rel2_cumul))
                    $relais2_affichee = array_search($k, array_keys($rel2_cumul)) + 1;
                $out[$k] = array($arr[$k]['team_stat'], $arr[$k]['timestamp'], $arr[$k]['relais1']['tstat'], $arr[$k]['relais2']['tstat'], $arr[$k]['relais3']['tstat'], $numlegs, $place_affichee, $arr[$k]['team_name'], 
                        $arr[$k]['relais1']['radio0'], $arr[$k]['relais1']['radio1'], $arr[$k]['relais1']['radio2'], $arr[$k]['relais1']['finish'], $relais1_affichee, $arr[$k]['relais1']['cumul'],
                        $arr[$k]['relais2']['radio0'], $arr[$k]['relais2']['radio1'], $arr[$k]['relais2']['radio2'], $arr[$k]['relais2']['finish'], $relais2_affichee, $arr[$k]['relais2']['cumul'],
                        $arr[$k]['relais3']['radio0'], $arr[$k]['relais3']['radio1'], $arr[$k]['relais3']['radio2'], $arr[$k]['relais3']['finish'], $relais3_affichee, $arr[$k]['relais3']['cumul'],
		       $arr[$k]['relais1']['name'], $arr[$k]['relais2']['name'], $arr[$k]['relais3']['name']
                );
            }
        }
	$last_time = -1;
    if($rel2_cumul != null)
        foreach($rel2_cumul as $k => $v)
        {
            if(!isset($out[$k]))
            {
		$place_stockee++;
		if(($arr[$k]['relais2']['cumul']) != $last_time)
		{
			$place_affichee = $place_stockee;
			$last_time = ($arr[$k]['relais2']['cumul']);
		}
                $relais1_affichee = '';
                $relais2_affichee = '';
                $relais3_affichee = '';
                if(array_key_exists($k, $rel1_cumul))
                    $relais1_affichee = array_search($k, array_keys($rel1_cumul)) + 1;
                if(array_key_exists($k, $rel2_cumul))
                    $relais2_affichee = array_search($k, array_keys($rel2_cumul)) + 1;
                $out[$k] = array($arr[$k]['team_stat'], $arr[$k]['timestamp'], $arr[$k]['relais1']['tstat'], $arr[$k]['relais2']['tstat'], $arr[$k]['relais3']['tstat'], $numlegs, $place_affichee, $arr[$k]['team_name'], 
                        $arr[$k]['relais1']['radio0'], $arr[$k]['relais1']['radio1'], $arr[$k]['relais1']['radio2'], $arr[$k]['relais1']['finish'], $relais1_affichee, $arr[$k]['relais1']['cumul'],
                        $arr[$k]['relais2']['radio0'], $arr[$k]['relais2']['radio1'], $arr[$k]['relais2']['radio2'], $arr[$k]['relais2']['finish'], $relais2_affichee, $arr[$k]['relais2']['cumul'],
                        $arr[$k]['relais3']['radio0'], $arr[$k]['relais3']['radio1'], $arr[$k]['relais3']['radio2'], $arr[$k]['relais3']['finish'], $relais3_affichee, $arr[$k]['relais3']['cumul'],
		       $arr[$k]['relais1']['name'], $arr[$k]['relais2']['name'], $arr[$k]['relais3']['name']
                );
            }
        }
	$last_time = -1;
    if($rel2_radio2 != null)
        foreach($rel2_radio2 as $k => $v)
        {
            if(!isset($out[$k]))
            {
                $place_stockee++;
		if(($arr[$k]['relais1']['cumul'] + $arr[$k]['relais2']['radio2']) != $last_time)
		{
			$place_affichee = $place_stockee;
			$last_time = ($arr[$k]['relais1']['cumul'] + $arr[$k]['relais2']['radio2']);
		}
                $relais1_affichee = '';
                $relais2_affichee = '';
                $relais3_affichee = '';
                if(array_key_exists($k, $rel1_cumul))
                    $relais1_affichee = array_search($k, array_keys($rel1_cumul)) + 1;
                $out[$k] = array($arr[$k]['team_stat'], $arr[$k]['timestamp'], $arr[$k]['relais1']['tstat'], $arr[$k]['relais2']['tstat'], $arr[$k]['relais3']['tstat'], $numlegs, $place_affichee, $arr[$k]['team_name'], 
                        $arr[$k]['relais1']['radio0'], $arr[$k]['relais1']['radio1'], $arr[$k]['relais1']['radio2'], $arr[$k]['relais1']['finish'], $relais1_affichee, $arr[$k]['relais1']['cumul'],
                        $arr[$k]['relais2']['radio0'], $arr[$k]['relais2']['radio1'], $arr[$k]['relais2']['radio2'], $arr[$k]['relais2']['finish'], $relais2_affichee, $arr[$k]['relais2']['cumul'],
                        $arr[$k]['relais3']['radio0'], $arr[$k]['relais3']['radio1'], $arr[$k]['relais3']['radio2'], $arr[$k]['relais3']['finish'], $relais3_affichee, $arr[$k]['relais3']['cumul'],
		       $arr[$k]['relais1']['name'], $arr[$k]['relais2']['name'], $arr[$k]['relais3']['name']
                );
            }
        }
	$last_time = -1;
    if($rel2_radio1 != null)
        foreach($rel2_radio1 as $k => $v)
        {
            if(!isset($out[$k]))
            {
                $place_stockee++;
		if(($arr[$k]['relais1']['cumul'] + $arr[$k]['relais2']['radio1']) != $last_time)
		{
			$place_affichee = $place_stockee;
			$last_time = ($arr[$k]['relais1']['cumul'] + $arr[$k]['relais2']['radio1']);
		}
                $relais1_affichee = '';
                $relais2_affichee = '';
                $relais3_affichee = '';
                if(array_key_exists($k, $rel1_cumul))
                    $relais1_affichee = array_search($k, array_keys($rel1_cumul)) + 1;
                $out[$k] = array($arr[$k]['team_stat'], $arr[$k]['timestamp'], $arr[$k]['relais1']['tstat'], $arr[$k]['relais2']['tstat'], $arr[$k]['relais3']['tstat'], $numlegs, $place_affichee, $arr[$k]['team_name'], 
                        $arr[$k]['relais1']['radio0'], $arr[$k]['relais1']['radio1'], $arr[$k]['relais1']['radio2'], $arr[$k]['relais1']['finish'], $relais1_affichee, $arr[$k]['relais1']['cumul'],
                        $arr[$k]['relais2']['radio0'], $arr[$k]['relais2']['radio1'], $arr[$k]['relais2']['radio2'], $arr[$k]['relais2']['finish'], $relais2_affichee, $arr[$k]['relais2']['cumul'],
                        $arr[$k]['relais3']['radio0'], $arr[$k]['relais3']['radio1'], $arr[$k]['relais3']['radio2'], $arr[$k]['relais3']['finish'], $relais3_affichee, $arr[$k]['relais3']['cumul'],
		       $arr[$k]['relais1']['name'], $arr[$k]['relais2']['name'], $arr[$k]['relais3']['name']
                );
            }
        }
	$last_time = -1;
    if($rel2_radio0 != null)
        foreach($rel2_radio0 as $k => $v)
        {
            if(!isset($out[$k]))
            {
                $place_stockee++;
		if(($arr[$k]['relais1']['cumul'] + $arr[$k]['relais2']['radio0']) != $last_time)
		{
			$place_affichee = $place_stockee;
			$last_time = ($arr[$k]['relais1']['cumul'] + $arr[$k]['relais2']['radio0']);
		}
                $relais1_affichee = '';
                $relais2_affichee = '';
                $relais3_affichee = '';
                if(array_key_exists($k, $rel1_cumul))
                    $relais1_affichee = array_search($k, array_keys($rel1_cumul)) + 1;
                $out[$k] = array($arr[$k]['team_stat'], $arr[$k]['timestamp'], $arr[$k]['relais1']['tstat'], $arr[$k]['relais2']['tstat'], $arr[$k]['relais3']['tstat'], $numlegs, $place_affichee, $arr[$k]['team_name'], 
                        $arr[$k]['relais1']['radio0'], $arr[$k]['relais1']['radio1'], $arr[$k]['relais1']['radio2'], $arr[$k]['relais1']['finish'], $relais1_affichee, $arr[$k]['relais1']['cumul'],
                        $arr[$k]['relais2']['radio0'], $arr[$k]['relais2']['radio1'], $arr[$k]['relais2']['radio2'], $arr[$k]['relais2']['finish'], $relais2_affichee, $arr[$k]['relais2']['cumul'],
                        $arr[$k]['relais3']['radio0'], $arr[$k]['relais3']['radio1'], $arr[$k]['relais3']['radio2'], $arr[$k]['relais3']['finish'], $relais3_affichee, $arr[$k]['relais3']['cumul'],
		       $arr[$k]['relais1']['name'], $arr[$k]['relais2']['name'], $arr[$k]['relais3']['name']
                );
            }
        }
	$last_time = -1;
    if($rel1_cumul != null)
        foreach($rel1_cumul as $k => $v)
        {
            if(!isset($out[$k]))
            {
                $place_stockee++;
		if(($arr[$k]['relais1']['cumul']) != $last_time)
		{
			$place_affichee = $place_stockee;
			$last_time = ($arr[$k]['relais1']['cumul']);
		}
                $relais1_affichee = '';
                $relais2_affichee = '';
                $relais3_affichee = '';
                if(array_key_exists($k, $rel1_cumul))
                    $relais1_affichee = array_search($k, array_keys($rel1_cumul)) + 1;
                $out[$k] = array($arr[$k]['team_stat'], $arr[$k]['timestamp'], $arr[$k]['relais1']['tstat'], $arr[$k]['relais2']['tstat'], $arr[$k]['relais3']['tstat'], $numlegs, $place_affichee, $arr[$k]['team_name'], 
                        $arr[$k]['relais1']['radio0'], $arr[$k]['relais1']['radio1'], $arr[$k]['relais1']['radio2'], $arr[$k]['relais1']['finish'], $relais1_affichee, $arr[$k]['relais1']['cumul'],
                        $arr[$k]['relais2']['radio0'], $arr[$k]['relais2']['radio1'], $arr[$k]['relais2']['radio2'], $arr[$k]['relais2']['finish'], $relais2_affichee, $arr[$k]['relais2']['cumul'],
                        $arr[$k]['relais3']['radio0'], $arr[$k]['relais3']['radio1'], $arr[$k]['relais3']['radio2'], $arr[$k]['relais3']['finish'], $relais3_affichee, $arr[$k]['relais3']['cumul'],
		       $arr[$k]['relais1']['name'], $arr[$k]['relais2']['name'], $arr[$k]['relais3']['name']
                );
            }
        }
	$last_time = -1;
    if($rel1_radio2 != null)
        foreach($rel1_radio2 as $k => $v)
        {
            if(!isset($out[$k]))
            {
                $place_stockee++;
		if(($arr[$k]['relais1']['radio2']) != $last_time)
		{
			$place_affichee = $place_stockee;
			$last_time = ($arr[$k]['relais1']['radio2']);
		}
                $relais1_affichee = '';
                $relais2_affichee = '';
                $relais3_affichee = '';
                $out[$k] = array($arr[$k]['team_stat'], $arr[$k]['timestamp'], $arr[$k]['relais1']['tstat'], $arr[$k]['relais2']['tstat'], $arr[$k]['relais3']['tstat'], $numlegs, $place_affichee, $arr[$k]['team_name'], 
                        $arr[$k]['relais1']['radio0'], $arr[$k]['relais1']['radio1'], $arr[$k]['relais1']['radio2'], $arr[$k]['relais1']['finish'], $relais1_affichee, $arr[$k]['relais1']['cumul'],
                        $arr[$k]['relais2']['radio0'], $arr[$k]['relais2']['radio1'], $arr[$k]['relais2']['radio2'], $arr[$k]['relais2']['finish'], $relais2_affichee, $arr[$k]['relais2']['cumul'],
                        $arr[$k]['relais3']['radio0'], $arr[$k]['relais3']['radio1'], $arr[$k]['relais3']['radio2'], $arr[$k]['relais3']['finish'], $relais3_affichee, $arr[$k]['relais3']['cumul'],
		       $arr[$k]['relais1']['name'], $arr[$k]['relais2']['name'], $arr[$k]['relais3']['name']
                );
            }
        }
	$last_time = -1;
    if($rel1_radio1 != null)
        foreach($rel1_radio1 as $k => $v)
        {
            if(!isset($out[$k]))
            {
                $place_stockee++;
		if(($arr[$k]['relais1']['radio1']) != $last_time)
		{
			$place_affichee = $place_stockee;
			$last_time = ($arr[$k]['relais1']['radio1']);
		}
                $relais1_affichee = '';
                $relais2_affichee = '';
                $relais3_affichee = '';
                $out[$k] = array($arr[$k]['team_stat'], $arr[$k]['timestamp'], $arr[$k]['relais1']['tstat'], $arr[$k]['relais2']['tstat'], $arr[$k]['relais3']['tstat'], $numlegs, $place_affichee, $arr[$k]['team_name'], 
                        $arr[$k]['relais1']['radio0'], $arr[$k]['relais1']['radio1'], $arr[$k]['relais1']['radio2'], $arr[$k]['relais1']['finish'], $relais1_affichee, $arr[$k]['relais1']['cumul'],
                        $arr[$k]['relais2']['radio0'], $arr[$k]['relais2']['radio1'], $arr[$k]['relais2']['radio2'], $arr[$k]['relais2']['finish'], $relais2_affichee, $arr[$k]['relais2']['cumul'],
                        $arr[$k]['relais3']['radio0'], $arr[$k]['relais3']['radio1'], $arr[$k]['relais3']['radio2'], $arr[$k]['relais3']['finish'], $relais3_affichee, $arr[$k]['relais3']['cumul'],
		       $arr[$k]['relais1']['name'], $arr[$k]['relais2']['name'], $arr[$k]['relais3']['name']
                );
            }
        }
	$last_time = -1;
    if($rel1_radio0 != null)
        foreach($rel1_radio0 as $k => $v)
        {
            if(!isset($out[$k]))
            {
                $place_stockee++;
		if(($arr[$k]['relais1']['radio0']) != $last_time)
		{
			$place_affichee = $place_stockee;
			$last_time = ($arr[$k]['relais1']['radio0']);
		}
                $relais1_affichee = '';
                $relais2_affichee = '';
                $relais3_affichee = '';
                $out[$k] = array($arr[$k]['team_stat'], $arr[$k]['timestamp'], $arr[$k]['relais1']['tstat'], $arr[$k]['relais2']['tstat'], $arr[$k]['relais3']['tstat'], $numlegs, $place_affichee, $arr[$k]['team_name'], 
                        $arr[$k]['relais1']['radio0'], $arr[$k]['relais1']['radio1'], $arr[$k]['relais1']['radio2'], $arr[$k]['relais1']['finish'], $relais1_affichee, $arr[$k]['relais1']['cumul'],
                        $arr[$k]['relais2']['radio0'], $arr[$k]['relais2']['radio1'], $arr[$k]['relais2']['radio2'], $arr[$k]['relais2']['finish'], $relais2_affichee, $arr[$k]['relais2']['cumul'],
                        $arr[$k]['relais3']['radio0'], $arr[$k]['relais3']['radio1'], $arr[$k]['relais3']['radio2'], $arr[$k]['relais3']['finish'], $relais3_affichee, $arr[$k]['relais3']['cumul'],
		       $arr[$k]['relais1']['name'], $arr[$k]['relais2']['name'], $arr[$k]['relais3']['name']
                );
            }
        }
    if($sta != null)
        foreach($sta as $k => $v)
        {
            if($v > 1)
            {
                if(!isset($out[$k]))
                {
                    $place_affichee = '';
                    $relais1_affichee = '';
                    $relais2_affichee = '';
                    $relais3_affichee = '';
                    $out[$k] = array($arr[$k]['team_stat'], $arr[$k]['timestamp'], $arr[$k]['relais1']['tstat'], $arr[$k]['relais2']['tstat'], $arr[$k]['relais3']['tstat'], $numlegs, $place_affichee, $arr[$k]['team_name'], 
                            $arr[$k]['relais1']['radio0'], $arr[$k]['relais1']['radio1'], $arr[$k]['relais1']['radio2'], $arr[$k]['relais1']['finish'], $relais1_affichee, $arr[$k]['relais1']['cumul'],
                            $arr[$k]['relais2']['radio0'], $arr[$k]['relais2']['radio1'], $arr[$k]['relais2']['radio2'], $arr[$k]['relais2']['finish'], $relais2_affichee, $arr[$k]['relais2']['cumul'],
                            $arr[$k]['relais3']['radio0'], $arr[$k]['relais3']['radio1'], $arr[$k]['relais3']['radio2'], $arr[$k]['relais3']['finish'], $relais3_affichee, $arr[$k]['relais3']['cumul'],
		       $arr[$k]['relais1']['name'], $arr[$k]['relais2']['name'], $arr[$k]['relais3']['name']
                    );
                }
            }
        }
    
    foreach($out as $k => $v)
    {
	//$out[$k][6] = getStatusString($out[$k][0]);
        $min = 8;
        $max = 13;
        for($i=$min;$i<=$max;$i++)
        {
            if(($i == $max) && ($rel1_tstat[$k] > 1))
            {
                $out[$k][$i] = getStatusString($rel1_tstat[$k]);
            }
            else
            if(($i == ($max -2)) && ($rel1_stat[$k] > 1))
            {
                $out[$k][$i] = getStatusString($rel1_stat[$k]);
            }
            else
            if($i != ($max-1))
            {
                $t = $v[$i] / 10;
                if ($t >= 3600)
                    $out[$k][$i] = sprintf("%d:%02d:%02d", $t/3600, ($t/60)%60, $t%60);
                else
                if ($t > 0)
                    $out[$k][$i] = sprintf("%02d:%02d", ($t/60), $t%60);
                else
                    $out[$k][$i] = '';
            }
        }
        $min = 14;
        $max = 19;
        for($i=$min;$i<=$max;$i++)
        {
            if(($i == $max) && ($rel2_tstat[$k] > 1))
            {
                $out[$k][$i] = getStatusString($rel2_tstat[$k]);
            }
            else
            if(($i == ($max -2)) && ($rel2_stat[$k] > 1))
            {
                $out[$k][$i] = getStatusString($rel2_stat[$k]);
            }
            else
            if($i != ($max-1))
            {
                $t = $v[$i] / 10;
                if ($t >= 3600)
                    $out[$k][$i] = sprintf("%d:%02d:%02d", $t/3600, ($t/60)%60, $t%60);
                else
                if ($t > 0)
                    $out[$k][$i] = sprintf("%02d:%02d", ($t/60), $t%60);
                else
                    $out[$k][$i] = '';
            }
        }
        $min = 20;
        $max = 25;
        for($i=$min;$i<=$max;$i++)
        {
            if(($i == $max) && ($rel3_tstat[$k] > 1))
            {
                $out[$k][$i] = getStatusString($rel3_tstat[$k]);
            }
            else
            if(($i == ($max -2)) && ($rel3_stat[$k] > 1))
            {
                $out[$k][$i] = getStatusString($rel3_stat[$k]);
            }
            else
            if($i != ($max-1))
            {
                $t = $v[$i] / 10;
                if ($t >= 3600)
                    $out[$k][$i] = sprintf("%d:%02d:%02d", $t/3600, ($t/60)%60, $t%60);
                else
                if ($t > 0)
                    $out[$k][$i] = sprintf("%02d:%02d", ($t/60), $t%60);
                else
                    $out[$k][$i] = '';
            }
        }
    }
    
	
	return $out;
}

function formatRelaisResult($result)
{
  global $pos;
  global $lang;
  $head = true;

    print '[';
  
  $i = 0;
  
  foreach($result as $row) 
  {            
    if ($head)
    {
        print("[");
        $head = false;
    }
    else
    {
        print(",[");
    }
    $first = true;
    foreach($row as $cell) 
    {
        if(is_array($cell))
        {
            foreach($cell as $col)
            {
                print ",";
                print '"'.$col.'"';
            }
        }
        else
        {
            print $first ? "" : ",";
            print '"'.$cell.'"';
            $first = false;
        }
    }
      print ']';
  }
  print "];";
}


function selectRadio($cls) {
  global $cmpId;
  $radio = '';
  $sql = "SELECT leg, ctrl, mopcontrol.name FROM mopclasscontrol, mopcontrol ".
         "WHERE mopcontrol.cid='$cmpId' AND mopclasscontrol.cid='$cmpId' ".
         "AND mopclasscontrol.id='$cls' AND mopclasscontrol.ctrl=mopcontrol.id ORDER BY leg ASC, ord ASC";
         
  
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
  $sql = "SELECT ctrl, mopcontrol.name FROM mopclasscontrol, mopcontrol ".
         "WHERE mopcontrol.cid='$cmpId' AND mopclasscontrol.cid='$cmpId' ".
         "AND mopclasscontrol.id='$cls' AND mopclasscontrol.ctrl=mopcontrol.id AND leg='$leg' AND ord='$ord'";
         
  
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
   //$tables = array(0=>"mopcontrol", "mopclass", "moporganization", "mopcompetition", "mopcompetitor", 
   //                   "mopteam", "mopteammember", "mopclasscontrol", "mopradio", "resultclass");
   // The table "resultclass" has been removed by JM from the list on 2015-09-09 in order to keep the classes selection when the MeOS service is restarted
   // may have side effects if some changes are made in MeOS classes for the competition, or if the cid is reused.
   // Well, wait and see...
   $tables = array(0=>"mopcontrol", "mopclass", "moporganization", "mopcompetition", "mopcompetitor", 
                      "mopteam", "mopteammember", "mopclasscontrol", "mopradio");
                      
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
  updateTable("mopcompetition", $cid, 1, $sqlupdate);
}

/** Update control table */
function processControl($cid, $ctrl) {
  $id = mysql_real_escape_string($ctrl['id']);
  $name = mysql_real_escape_string($ctrl);
  $sqlupdate = "name='$name'";
  updateTable("mopcontrol", $cid, $id, $sqlupdate);
}

/** Update class table */
function processClass($cid, $cls) {
  $id = mysql_real_escape_string($cls['id']);
  $ord = mysql_real_escape_string($cls['ord']);
  $name = mysql_real_escape_string($cls);
  $sqlupdate = "name='$name', ord='$ord'";
  updateTable("mopclass", $cid, $id, $sqlupdate);
    
  if (isset($cls['radio'])) {
    $radio = mysql_real_escape_string($cls['radio']);
    updateLinkTable("mopclasscontrol", $cid, $id, "ctrl", $radio);    
  }
}

/** Update organization table */
function processOrganization($cid, $org) {
  $id = mysql_real_escape_string($org['id']);
  $name = mysql_real_escape_string($org);
  $sqlupdate = "name='$name'";
  updateTable("moporganization", $cid, $id, $sqlupdate);
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
  $now = time();
  
  
  $sqlupdate = "name='$name', org=$org, cls=$cls, stat=$stat, st=$st, rt=$rt,timestamp=$now";

  if (isset($cmp->input)) {
    $input = $cmp->input;
    $it = (int)$input['it'];
    $tstat = (int)$input['tstat'];
    $sqlupdate.=", it=$it, tstat=$tstat";
  }

  updateTable("mopcompetitor", $cid, $id, $sqlupdate);  
  if (isset($cmp->radio)) {
    $sql = "DELETE FROM mopradio WHERE cid='$cid' AND id='$id'";
    mysql_query($sql);
    $radios = explode(";", $cmp->radio);
    foreach($radios as $radio) {
      $tmp = explode(",", $radio);
      $radioId = (int)$tmp[0];
      $radioTime = (int)$tmp[1];
      $sql = "REPLACE INTO mopradio SET cid='$cid', id='$id', ctrl='$radioId', rt='$radioTime', timestamp=$now";
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
  updateTable("mopteam", $cid, $id, $sqlupdate);
  
  if (isset($team->r)) {
    updateLinkTable("mopteammember", $cid, $id, "rid", $team->r);
  }
}

/** MOP return code. */
function returnStatus($stat) {
  die('<?xml version="1.0"?><MOPStatus status="'.$stat.'"></MOPStatus>');
}


/* MW */
function defineVariable($variable, $value)
{
    print $variable." = eval('".$value."');";
}

function defineVariableArr($variable, $value1, $value2)
{
    print $variable." = eval(['".$value1."', '".$value2."']);\r\n";
}

function defineVariableArrFromArr($variable, $arr)
{
    print $variable." = Array;";
    foreach($arr as $k => $v)
    {
        print $variable."[".$k."] = eval('".$v."');\r\n";
    }
}

function defineVariableArr2x($variable, $arr1, $arr2)
{
    $em1 = '[\''.implode('\', \'', $arr1).'\']';
    $em2 = '[\''.implode('\', \'', $arr2).'\']'; 
    print $variable." = eval([".$em1.", ".$em2."]);\r\n";
}

function displayContentHtml($filename)
{
    return file_get_contents('htmlfiles/'.$filename);
}

function displayContentText($texte, $size, $color)
{
    $retour = '';
    $retour .= '<div style="display:table;height:480px;overflow:hidden;width:100%;">';
    $retour .= '<div style="display:table-cell;vertical-align:middle;width:100%;margin:0;text-align:center;font-size:'.$size.'px;color:#'.$color.';">';
    $retour .= $texte;
    $retour .= '</div>';
    $retour .= '</div>';
    
    return $retour;
}

function displayContentPicture($picture)
{
    $retour = '';
    $arr_img = getimagesize ('pictures/'.$picture);
    if($arr_img[0] > $arr_img[1])
    {
        $retour .= '<div style="display:table;height:470px;overflow:hidden;width:100%;">';
        $retour .= '<div style="padding:2px;display:table-cell;vertical-align:middle;width:100%;text-align:center;"><img src="pictures/'.$picture.'" alt="" max-width="100%" width="100%" /></div>';
        $retour .= '</div>';
    }
    else
    {
        $retour .= '<div style="padding:2px;text-align:center;"><img src="pictures/'.$picture.'" alt="" max-height="470px" height="470px" /></div>';
    }
    return $retour;
}

function displayTopPicture($picture, $hauteur)
{
    $retour = '';
    $arr_img = getimagesize ('pictures/'.$picture);
    $height_txt = '';
    if($arr_img[1] > $hauteur)
    {
        $height_txt = 'height="'.$hauteur.'px"';
        $maxheight_txt = 'max-height="'.$hauteur.'px"';
    }
    if(($arr_img[0] > $arr_img[1]) && ($arr_img[1] > $hauteur))
    {
        $retour .= '<img src="pictures/'.$picture.'" alt="" '.$height_txt.' '.$maxheight_txt.' max-width="100%" />';
    }
    else
    if($arr_img[0] > $arr_img[1])
    {
        $retour .= '<img src="pictures/'.$picture.'" alt="" '.$maxheight_txt.' max-width="100%" />';
    }
    else
    {
        $retour .= '<img src="pictures/'.$picture.'" alt="" '.$height_txt.' />';
    }
    return $retour;
}

function calculeStart($res) {
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
    $start_time_s = $r['st'] / 10.0;
    $start_time = date("H:i",$start_time_s);
    
    
    $t = $r['time']/10;
    if ($bestTime == -1)
      $bestTime = $t;
    if ($lastTime != $t) {
      $place = $count;
      $lastTime = $t;
    }        
    $row = array();
    
    $row['start_time'] = $start_time;
    if ($r['status'] == 1) {
      $row['name'] = $r['name'];      
      $row['team'] = $r['team'];
    }
    else {
      $row['name'] = $r['name'];      
      $row['team'] = $r['team'];
    }
    
    $out[$count] = $row;
  }
  
  return $out;
}

?>
