<?php
  /*
  Copyright 2013 Melin Software HB
  Modified by Jerome Monclard 2015-2020
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
//include_once('lang.php');
$link = ConnectToDB();
/** Connecto to MySQL */
function ConnectToDB() {
  $link = mysqli_connect(MYSQL_HOSTNAME, MYSQL_USERNAME, MYSQL_PASSWORD);
  if (!$link) {
    die('Not connected : ' . mysqli_connect_error());//mysqli_error($link));
  }
  $db_selected = mysqli_select_db($link, MYSQL_DBNAME);
  if (!$db_selected) {
    die ("Can't use ". MYSQL_HOSTNAME. ' : ' . mysqli_error($link));
  }
  return $link;
}
function redirectSwitchUsers()
{
  $ip=$_SERVER['REMOTE_ADDR'];
  $ipnb=explode('.',$ip);
  if (($ipnb[0]!='192')||($ipnb[1]!='168')||($ipnb[2]!='0')||($ipnb[3]=='20'))
  {
      header("Location: http://<ToBeDefined>/show.php");
      exit;//die();
  }
}
function query($sql) {
$link = ConnectToDB();
 $result = mysqli_query($link, $sql);
 if (!$result) {
   die('Invalid query: ' . mysqli_error($link));
 }
 return $result;
}
/**
 * Détection automatique de la langue du navigateur
 * Les codes langues du tableau $aLanguages doivent obligatoirement être sur 2 caractères
 * Utilisation : $langue = autoSelectLanguage(array('fr','en','es','it','de','cn'), 'en')
 * @param array $aLanguages Tableau 1D des langues du site disponibles (ex: array('fr','en','es','it','de','cn')).
 * @param string $sDefault Langue à choisir par défaut si aucune n'est trouvée
 * @return string La langue du navigateur ou bien la langue par défaut
 * @author Hugo Hamon
 * @version 0.1
 */
function autoSelectLanguage($aLanguages, $sDefault = 'fr') {
  if(!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
	$aBrowserLanguages = explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']);
	foreach($aBrowserLanguages as $sBrowserLanguage) {
	  $sLang = strtolower(substr($sBrowserLanguage,0,2));
	  if(in_array($sLang, $aLanguages)) {
		return $sLang;
	  }
	}
  }
  return $sDefault;
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


function newGetStatusString($status) {
	$text = '?';
		  switch($status) {
		    case 0:
		      $text = MyGetText(73); //Unknown, running?
		      break;
		    case 1:
		      $text = MyGetText(74); // OK
		      break;
		    case 3:
		      $text = MyGetText(75); // Missing punch
		      break;
		    case 4:
		      $text = MyGetText(76); //Did not finish
		      break;
		    case 5:
		      $text = MyGetText(77); // Disqualified
		      break;
		    case 6:
		      $text = MyGetText(78); // Overtime
		      break;
		    case 20:
		      $text = MyGetText(79); // Did not start;
		      break;
		    case 99:
		      $text = MyGetText(80); //Not participating;
		      break;
		  }
	return $text;
}

$global_out = array();
$global_out2 = array();
$global_out3 = array();
$global_out4 = array();

function newCalculateResult($res, $nb_radio = 4)
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

  while ($r = mysqli_fetch_array($res))
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
      $row['name'] = $r['name'].'/'.strtolower($r['country']); /*addFlag($r['country']).*/
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
      $row['name'] = $r['name'].'/'.strtolower($r['country']); /*addFlag($r['country']).*/
      $row['team'] = $r['team'];
      for($i=0;$i<$nb_radio;$i++)
      {
        $row['radio'.$i] = '';//sprintf("%d:%02d:%02d", $t/3600, ($t/60)%60, $t%60);
      }
      $row['time'] = newGetStatusString($r['status']);
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
        $row['tottime'] = newGetStatusString($r['totstat']);
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

function CalculateRogainingResult($res)
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

  while ($r = mysqli_fetch_array($res))
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
      $row['name'] = $r['name'].'/'.strtolower($r['country']); /*addFlag($r['country']).*/
      $row['team'] = $r['team'];
      if ($t >= 3600)
        $row['time'] = sprintf("%d:%02d:%02d", $t/3600, ($t/60)%60, $t%60);
      else
      if ($t > 0)
        $row['time'] = sprintf("%02d:%02d", ($t/60), $t%60);
      else
        $row['time'] = "OK"; // No timing
    }
    else  /* status != 1 */
    {
      $row['st'] = $r['status'];
      $row['timestamp'] = $r['timestamp'];
      $row['place'] = "";
      $row['name'] = $r['name'].'/'.strtolower($r['country']); /*addFlag($r['country']).*/
      $row['team'] = $r['team'];
      $row['time'] = newGetStatusString($r['status']);
    }
    $row['ptsgross'] = $r['ptsgross'];
    $row['pts'] = $r['pts'];
    $out[$count] = $row;
  }
  return $out;
}

function newCalculateMultistageAlternateResult($res, $bestTime, $nb_radio = 4)
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
  $lastTeam = -1;
  $totalResult = array();
  $hasTotal = false;

//while ($r = mysqli_fetch_array($res))
foreach($res as $r)
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
    $row = array();
    if ($r['status'] == 1) {
      $row['st'] = $r['status'];
      $row['timestamp'] = $r['timestamp'];
      if($r['totstat'] == 1)
      {
	      $row['place'] = $r['rank_tot'];//.".";
	  }
	  else
	  {
	  	$row['place'] = '';
	  }
      $row['name'] = $r['name'].'/'.strtolower($r['country']); /*addFlag($r['country']).*/
      $row['team'] = $r['team'];
      /*$row['tottime'] = $r['tottime'];
      $row['totstat'] = $r['totstat'];*/
      for($i=0;$i<$nb_radio;$i++)
      {
        $row['radio'.$i] = '';//sprintf("%d:%02d:%02d", $t/3600, ($t/60)%60, $t%60);
      }
		if (isset($r['tottime']))
		{
			$hasTotal = true;
			if (($r['totstat'] == 1) && ($r['status'] == 1))
			{
    			$tt = $r['tottime']/10;
    			if ($tt > 0)
    			$row['tottime'] = sprintf("%d:%02d:%02d", $tt/3600, ($tt/60)%60, $tt%60);
    			else
    			$row['tottime'] = "OK"; // No timing
			}
			else
			{
    			if($r['totstat'] > 1)
    			{
        			$row['tottime'] = newGetStatusString($r['totstat']);
    			}
    			else
    			{
        			$row['tottime'] = newGetStatusString($r['status']);
    			}
			}
			if ($r['totstat'] > 0)
    			$totalResult[$count] = ($r['totstat']-1) * 10000000 + $r['tottime'];
			else
    			$totalResult[$count] = 10000000 * 100;


		}
    	if ($t >= 3600)
        	$row['time'] = sprintf("%d:%02d:%02d", $t/3600, ($t/60)%60, $t%60);
    	else
      	if ($t > 0)
        	$row['time'] = sprintf("%02d:%02d", ($t/60), $t%60);
      	else
        	$row['time'] = "OK"; // No timing
      	$after = $t - $bestTime/10;
      	if ($after > 0)
        	$row['after'] = sprintf("+%d:%02d", ($after/60), $after%60);
      	else
        	$row['after'] = "";
        $row['inter_classement'] = $r['rank_etape'];
    }
    else
    {
      $row['st'] = $r['status'];
      $row['timestamp'] = $r['timestamp'];
      $row['place'] = "";
      $row['name'] = $r['name'].'/'.strtolower($r['country']); /*addFlag($r['country']).*/
      $row['team'] = $r['team'];
      /*$row['tottime'] = $r['tottime'];
      $row['totstat'] = $r['totstat'];*/
      for($i=0;$i<$nb_radio;$i++)
      {
        $row['radio'.$i] = '';//sprintf("%d:%02d:%02d", $t/3600, ($t/60)%60, $t%60);
      }
      if($r['totstat'] > 1)
    {
        $row['tottime'] = newGetStatusString($r['totstat']);
    }
    else
    {
        $row['tottime'] = newGetStatusString($r['status']);
    }
      $row['time'] = newGetStatusString($r['status']);
      $row['after'] = "";
      $row['inter_classement'] = "";
    }

    $out[$count] = $row;
    $row['id'] = $r['id'];
    $row['status'] = $r['status'];
    $row['tt'] = $t;

  }

  return $out;
}

function newCalculateMultistageResult($res, $bestTime, $nb_radio = 4)
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
  $lastTeam = -1;
  $totalResult = array();
  $hasTotal = false;

//while ($r = mysqli_fetch_array($res))
if($res != null)
{
	foreach($res as $r)
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
    $row = array();
    if ($r['status'] == 1) {
      $row['st'] = $r['status'];
      $row['timestamp'] = $r['timestamp'];
      $row['place'] = $r['rank_etape'];//.".";
      $row['name'] = $r['name'].'/'.strtolower($r['country']); /*addFlag($r['country']).*/
      $row['team'] = $r['team'];
      /*$row['tottime'] = $r['tottime'];
      $row['totstat'] = $r['totstat'];*/
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
      $after = $t - $bestTime/10;
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
      $row['name'] = $r['name'].'/'.strtolower($r['country']); /*addFlag($r['country']).*/
      $row['team'] = $r['team'];
      /*$row['tottime'] = $r['tottime'];
      $row['totstat'] = $r['totstat'];*/
      for($i=0;$i<$nb_radio;$i++)
      {
        $row['radio'.$i] = '';//sprintf("%d:%02d:%02d", $t/3600, ($t/60)%60, $t%60);
      }
      $row['time'] = newGetStatusString($r['status']);
      $row['after'] = "";
    }
    if (isset($r['tottime']))
    {
      $hasTotal = true;
      if (($r['totstat'] == 1) && ($r['status'] == 1))
      {
        $tt = $r['tottime']/10;
        if ($tt > 0)
          $row['tottime'] = sprintf("%d:%02d:%02d", $tt/3600, ($tt/60)%60, $tt%60);
        else
          $row['tottime'] = "OK"; // No timing
      }
      else
      {
      	if($r['totstat'] > 1)
      	{
        	$row['tottime'] = newGetStatusString($r['totstat']);
        }
        else
        {
        	$row['tottime'] = newGetStatusString($r['status']);
        }
      }
      if ($r['totstat'] > 0)
        $totalResult[$count] = ($r['totstat']-1) * 10000000 + $r['tottime'];
      else
        $totalResult[$count] = 10000000 * 100;

		if(($r['totstat'] == 1) && ($r['status'] == 1))
		{
	       	$row['inter_classement'] = $r['rank_tot'];
	    }
	    else
	    {
	    	$row['inter_classement'] = '';
	    }

    }
    $out[$count] = $row;
    $row['id'] = $r['id'];
    $row['status'] = $r['status'];
    $row['tt'] = $t;
  	}
  }

  return $out;
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
  while ($r = mysqli_fetch_array($res))
  {
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

    if ($r['status'] == 1)
    {
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
    else
    {
      $row['place'] = "";
      $row['name'] = $r['name'];
      $row['team'] = $r['team'];

      $row['time'] = getStatusString($r['status']);
      $row['after'] = "";
    }


    if (isset($r['tottime']))
    {
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

  if ($hasTotal)
  {
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

function addRadioMultistageResult($res, $results)
{
	// do nothing, but have to return something to be transparent
	return $results;
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
  while ($r = mysqli_fetch_array($res))
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
  $out = array();

  $nradios = count($arr_radio); // 2016
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
  for($i=($nradios - 1);$i>=0;$i--)
  {
    if((isset($arr_keyRunning[$i])) && ($arr_keyRunning[$i] != null))
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
  $finish_radio = 1;
  if($arr_radiomax != null)
    $finish_radio = max($arr_radiomax) + 1;

  foreach($temp_tab as $k => $v)
  {
    $place++;
    $last_res = array(); // 2016
    if(!$first)
    {
      $first = true;
      $place_affichee = $place;
      //$last_res = array($result[$v]['radio0'], $result[$v]['radio1'], $result[$v]['radio2'], $result[$v]['radio3'], $global_out4[$v]); // 2016
      for($inc=0;$inc<$nradios;$inc++)
      {
        $last_res[] = $result[$v]['radio'.$inc];
      }
      $last_res[] = $global_out4[$v];
      // 2016
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
          if((!isset($last_res[$nradios])) || ((isset($last_res[$nradios])) && ($global_out4[$v] != $last_res[$nradios]))) // 2016 // 2017
          {
            $last_res[$nradios] = $global_out4[$v]; // 2016
            $place_affichee = $place;
          }
        }
        else
        {
          if((!isset($last_res[$nradios])) || ((isset($last_res[$nradios])) && ($global_out4[$v] != $last_res[$nradios]))) // 2016 // 2017
          {
            $last_res[$nradios] = $global_out4[$v]; // 2016
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
    for($i=0;$i<$nradios;$i++) // 2016
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
      for($i=0;$i<$nradios;$i++) // 2016
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

function formatResultScreen($result, $limit = 9999)
{
  global $pos;
  global $lang;

  $head = true;
  print '[';
  $i = 0;
  if($result != null)
  {
    foreach($result as $row)
    {
      $i++;
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
      if($i >= $limit)
        break;
    }
  }
  print "];";
}


function reorder_relay($arr, $numlegs)
{
	$out = array();
	$rel_tstat = array();
	$rel_stat = array();
	$rel_radio0 = array();
	$rel_radio1 = array();
	$rel_radio2 = array();
	$rel_radio3 = array();
	$sta = array();
	$rel_cumul = array();
	for($i=1;$i<=$numlegs;$i++)
	{
		$rel_tstat[$i] = array();
		$rel_stat[$i] = array();
		$rel_radio0[$i] = array();
		$rel_radio1[$i] = array();
		$rel_radio2[$i] = array();
		$rel_radio3[$i] = array();
		$rel_cumul[$i] = array();
	}
	foreach ($arr as $key => $val)
	{
		for($i=1;$i<=$numlegs;$i++)
		{
			$rel_tstat[$i][$key]  = $val['relay'.$i]['tstat'];
			$rel_stat[$i][$key]  = $val['relay'.$i]['stat'];
		}
        $sta[$key] = $val['team_stat'];
        if($sta[$key] <= 1)
        {
          for($i=1;$i<=$numlegs;$i++)
          {
            if($i == 1)
            {
              if($val['relay'.$i]['radio0'] > 0)
                $rel_radio0[$i][$key]  = $val['relay'.$i]['radio0'];
              if($val['relay'.$i]['radio1'] > 0)
                $rel_radio1[$i][$key] = $val['relay'.$i]['radio1'];
              if($val['relay'.$i]['radio2'] > 0)
                $rel_radio2[$i][$key] = $val['relay'.$i]['radio2'];
              if($val['relay'.$i]['radio3'] > 0)
                $rel_radio3[$i][$key] = $val['relay'.$i]['radio3'];
              if(($val['relay'.$i]['tstat'] <= 1) && ($val['relay'.$i]['stat'] <= 1) && ($val['relay'.$i]['cumul'] > 0))
                $rel_cumul[$i][$key]  = $val['relay'.$i]['cumul'];
            }
            else
            {
              if($val['relay'.$i]['radio0'] > 0)
              {
                if($val['relay'.($i-1)]['cumul'] > 0)
                {
                  $rel_radio0[$i][$key]  = $val['relay'.($i-1)]['cumul'] + $val['relay'.$i]['radio0'];
                }
                else
                {
                }
              }
              if($val['relay'.$i]['radio1'] > 0)
              {
                if($val['relay'.($i-1)]['cumul'] > 0)
                {
                  $rel_radio1[$i][$key] = $val['relay'.($i-1)]['cumul'] + $val['relay'.$i]['radio1'];
                }
                else
                {
                }
              }
              if($val['relay'.$i]['radio2'] > 0)
              {
                if($val['relay'.($i-1)]['cumul'] > 0)
                {
                  $rel_radio2[$i][$key] = $val['relay'.($i-1)]['cumul'] + $val['relay'.$i]['radio2'];
                }
                else
                {
                }
              }
              if($val['relay'.$i]['radio3'] > 0)
              {
                if($val['relay'.($i-1)]['cumul'] > 0)
                {
                  $rel_radio3[$i][$key] = $val['relay'.($i-1)]['cumul'] + $val['relay'.$i]['radio3'];
                }
                else
                {
                }
              }
              if(($val['relay'.$i]['tstat'] <= 1) && ($val['relay'.$i]['stat'] <= 1) && ($val['relay'.$i]['cumul'] > 0))
              {
                if($val['relay'.($i-1)]['cumul'] > 0)
                {
                  $rel_cumul[$i][$key]  = $val['relay'.$i]['cumul'];
                }
                else
                {
                  $arr[$key]['relay'.$i]['cumul'] = 0;
                }
              }
            }
          }
        }
	}
	// Ajoute $arr en tant que dernier parametre
	//array_multisort($rel3_cumul, SORT_ASC, $rel3_radio2, SORT_ASC, $rel3_radio1, SORT_ASC, $rel3_radio0, SORT_ASC, $rel2_cumul, SORT_ASC, $rel2_radio2, SORT_ASC, $rel2_radio1, SORT_ASC, $rel2_radio0, SORT_ASC, $rel1_cumul, SORT_ASC, $rel1_radio2, SORT_ASC, $rel1_radio1, SORT_ASC, $rel1_radio0, SORT_ASC, $sta, SORT_ASC, $arr);
	for($i=1;$i<=$numlegs;$i++)
	{
		if($rel_cumul[$i] != null) natcasesort($rel_cumul[$i]);
		if($rel_radio0[$i] != null) natcasesort($rel_radio0[$i]);
		if($rel_radio1[$i] != null) natcasesort($rel_radio1[$i]);
		if($rel_radio2[$i] != null) natcasesort($rel_radio2[$i]);
		if($rel_radio3[$i] != null) natcasesort($rel_radio3[$i]);
	}
	/*
	print_r($rel_cumul);
	echo '<hr />';
	print_r($rel_radio0);
	echo '<hr />';
	print_r($rel_tstat);
	echo '<hr />';
	print_r($rel_stat);
	echo '<hr />';
	print_r($sta);
	echo '<hr />';*/
    $place_affichee = 0;
    $place_stockee = 0;
    $last_time = -1;
	for($i=$numlegs;$i>=1;$i--)
	{
		if($rel_cumul[$i] != null)
		{
			$last_time = -1;
			foreach($rel_cumul[$i] as $k => $v)
			{
				if(!isset($out[$k]))
				{
					$place_stockee++;
					if($arr[$k]['relay'.$i]['cumul'] != $last_time)
					{
					  $place_affichee = $place_stockee;
					  $last_time = $arr[$k]['relay'.$i]['cumul'];
					}
					for($j=1;$j<=$numlegs;$j++)
					{
						$displayed_relay[$j] = '';
					}
					for($j=1;$j<=$i;$j++)
					{
						if(array_key_exists($k, $rel_cumul[$j]))
							$displayed_relay[$j] = array_search($k, array_keys($rel_cumul[$j])) + 1;
						else
							$displayed_relay[$j] = '';
					}
					$out[$k] = array($arr[$k]['team_stat'], $arr[$k]['timestamp']);
					for($j=1;$j<=$numlegs;$j++)
					{
						$out[$k][] = $arr[$k]['relay'.$j]['tstat'];
					}
					$out[$k] = array_merge($out[$k], array($numlegs, $place_affichee, $arr[$k]['team_name']));
					for($j=1;$j<=$numlegs;$j++)
					{
						$temp_arr = array($arr[$k]['relay'.$j]['radio0'], $arr[$k]['relay'.$j]['radio1'], $arr[$k]['relay'.$j]['radio2'], $arr[$k]['relay'.$j]['radio3'], $arr[$k]['relay'.$j]['finish'], $displayed_relay[$j], $arr[$k]['relay'.$j]['cumul']);
						$out[$k] = array_merge($out[$k], $temp_arr);
					}
					for($j=1;$j<=$numlegs;$j++)
					{
						if(isset($arr[$k]['relay'.$j]['name']))
        			    {
         			     $out[$k][] = $arr[$k]['relay'.$j]['name'];
         			    }
        			    else
         			    {
            			  $out[$k][] = '';
            			}
					}
				}
			}
		}
		if($rel_radio3[$i] != null)
		{
			$last_time = -1;
			foreach($rel_radio3[$i] as $k => $v)
			{
				if(!isset($out[$k]))
				{
					$place_stockee++;
					if($i > 1)
					{
						if(($arr[$k]['relay'.($i-1)]['cumul'] + $arr[$k]['relay'.$i]['radio3']) != $last_time)
						{
						  $place_affichee = $place_stockee;
						  $last_time = ($arr[$k]['relay'.($i-1)]['cumul'] + $arr[$k]['relay'.$i]['radio3']);
						}
					}
					else
					{
						if($arr[$k]['relay'.$i]['radio3'] != $last_time)
						{
						  $place_affichee = $place_stockee;
						  $last_time = $arr[$k]['relay'.$i]['radio3'];
						}
					}
					for($j=1;$j<=$numlegs;$j++)
					{
						$displayed_relay[$j] = '';
					}
					for($j=1;$j<$i;$j++)
					{
						if(array_key_exists($k, $rel_cumul[$j]))
							$displayed_relay[$j] = array_search($k, array_keys($rel_cumul[$j])) + 1;
						else
							$displayed_relay[$j] = '';
					}
					$out[$k] = array($arr[$k]['team_stat'], $arr[$k]['timestamp']);
					for($j=1;$j<=$numlegs;$j++)
					{
						$out[$k][] = $arr[$k]['relay'.$j]['tstat'];
					}
					$out[$k] = array_merge($out[$k], array($numlegs, $place_affichee, $arr[$k]['team_name']));
					for($j=1;$j<=$numlegs;$j++)
					{
						$temp_arr = array($arr[$k]['relay'.$j]['radio0'], $arr[$k]['relay'.$j]['radio1'], $arr[$k]['relay'.$j]['radio2'], $arr[$k]['relay'.$j]['radio3'], $arr[$k]['relay'.$j]['finish'], $displayed_relay[$j], $arr[$k]['relay'.$j]['cumul']);
						$out[$k] = array_merge($out[$k], $temp_arr);
					}
					for($j=1;$j<=$numlegs;$j++)
					{
						if(isset($arr[$k]['relay'.$j]['name']))
            {
              $out[$k][] = $arr[$k]['relay'.$j]['name'];
            }
            else
            {
              $out[$k][] = '';
            }
					}
				}
			}
		}
		if($rel_radio2[$i] != null)
		{
			$last_time = -1;
			foreach($rel_radio2[$i] as $k => $v)
			{
				if(!isset($out[$k]))
				{
					$place_stockee++;
					if($i > 1)
					{
						if(($arr[$k]['relay'.($i-1)]['cumul'] + $arr[$k]['relay'.$i]['radio2']) != $last_time)
						{
						  $place_affichee = $place_stockee;
						  $last_time = ($arr[$k]['relay'.($i-1)]['cumul'] + $arr[$k]['relay'.$i]['radio2']);
						}
					}
					else
					{
						if($arr[$k]['relay'.$i]['radio2'] != $last_time)
						{
						  $place_affichee = $place_stockee;
						  $last_time = $arr[$k]['relay'.$i]['radio2'];
						}
					}
					for($j=1;$j<=$numlegs;$j++)
					{
						$displayed_relay[$j] = '';
					}
					for($j=1;$j<$i;$j++)
					{
						if(array_key_exists($k, $rel_cumul[$j]))
							$displayed_relay[$j] = array_search($k, array_keys($rel_cumul[$j])) + 1;
						else
							$displayed_relay[$j] = '';
					}
					$out[$k] = array($arr[$k]['team_stat'], $arr[$k]['timestamp']);
					for($j=1;$j<=$numlegs;$j++)
					{
						$out[$k][] = $arr[$k]['relay'.$j]['tstat'];
					}
					$out[$k] = array_merge($out[$k], array($numlegs, $place_affichee, $arr[$k]['team_name']));
					for($j=1;$j<=$numlegs;$j++)
					{
						$temp_arr = array($arr[$k]['relay'.$j]['radio0'], $arr[$k]['relay'.$j]['radio1'], $arr[$k]['relay'.$j]['radio2'], $arr[$k]['relay'.$j]['radio3'], $arr[$k]['relay'.$j]['finish'], $displayed_relay[$j], $arr[$k]['relay'.$j]['cumul']);
						$out[$k] = array_merge($out[$k], $temp_arr);
					}
					for($j=1;$j<=$numlegs;$j++)
					{
						if(isset($arr[$k]['relay'.$j]['name']))
            {
              $out[$k][] = $arr[$k]['relay'.$j]['name'];
            }
            else
            {
              $out[$k][] = '';
            }
					}
				}
			}
		}
		if($rel_radio1[$i] != null)
		{
			$last_time = -1;
			foreach($rel_radio1[$i] as $k => $v)
			{
				if(!isset($out[$k]))
				{
					$place_stockee++;
					if($i > 1)
					{
						if(($arr[$k]['relay'.($i-1)]['cumul'] + $arr[$k]['relay'.$i]['radio1']) != $last_time)
						{
						  $place_affichee = $place_stockee;
						  $last_time = ($arr[$k]['relay'.($i-1)]['cumul'] + $arr[$k]['relay'.$i]['radio1']);
						}
					}
					else
					{
						if($arr[$k]['relay'.$i]['radio1'] != $last_time)
						{
						  $place_affichee = $place_stockee;
						  $last_time = $arr[$k]['relay'.$i]['radio1'];
						}
					}
					for($j=1;$j<=$numlegs;$j++)
					{
						$displayed_relay[$j] = '';
					}
					for($j=1;$j<$i;$j++)
					{
						if(array_key_exists($k, $rel_cumul[$j]))
							$displayed_relay[$j] = array_search($k, array_keys($rel_cumul[$j])) + 1;
						else
							$displayed_relay[$j] = '';
					}
					$out[$k] = array($arr[$k]['team_stat'], $arr[$k]['timestamp']);
					for($j=1;$j<=$numlegs;$j++)
					{
						$out[$k][] = $arr[$k]['relay'.$j]['tstat'];
					}
					$out[$k] = array_merge($out[$k], array($numlegs, $place_affichee, $arr[$k]['team_name']));
					for($j=1;$j<=$numlegs;$j++)
					{
						$temp_arr = array($arr[$k]['relay'.$j]['radio0'], $arr[$k]['relay'.$j]['radio1'], $arr[$k]['relay'.$j]['radio2'], $arr[$k]['relay'.$j]['radio3'], $arr[$k]['relay'.$j]['finish'], $displayed_relay[$j], $arr[$k]['relay'.$j]['cumul']);
						$out[$k] = array_merge($out[$k], $temp_arr);
					}
					for($j=1;$j<=$numlegs;$j++)
					{
            if(isset($arr[$k]['relay'.$j]['name']))
            {
              $out[$k][] = $arr[$k]['relay'.$j]['name'];
            }
            else
            {
              $out[$k][] = '';
            }
					}
				}
			}
		}
		if($rel_radio0[$i] != null)
		{
			$last_time = -1;
			foreach($rel_radio0[$i] as $k => $v)
			{
				if(!isset($out[$k]))
				{
					$place_stockee++;
					if($i > 1)
					{
						if(($arr[$k]['relay'.($i-1)]['cumul'] + $arr[$k]['relay'.$i]['radio0']) != $last_time)
						{
						  $place_affichee = $place_stockee;
						  $last_time = ($arr[$k]['relay'.($i-1)]['cumul'] + $arr[$k]['relay'.$i]['radio0']);
						}
					}
					else
					{
						if($arr[$k]['relay'.$i]['radio0'] != $last_time)
						{
						  $place_affichee = $place_stockee;
						  $last_time = $arr[$k]['relay'.$i]['radio0'];
						}
					}
					for($j=1;$j<=$numlegs;$j++)
					{
						$displayed_relay[$j] = '';
					}
					for($j=1;$j<$i;$j++)
					{
						if(array_key_exists($k, $rel_cumul[$j]))
							$displayed_relay[$j] = array_search($k, array_keys($rel_cumul[$j])) + 1;
						else
							$displayed_relay[$j] = '';
					}
					$out[$k] = array($arr[$k]['team_stat'], $arr[$k]['timestamp']);
					for($j=1;$j<=$numlegs;$j++)
					{
						$out[$k][] = $arr[$k]['relay'.$j]['tstat'];
					}
					$out[$k] = array_merge($out[$k], array($numlegs, $place_affichee, $arr[$k]['team_name']));
					for($j=1;$j<=$numlegs;$j++)
					{
						$temp_arr = array($arr[$k]['relay'.$j]['radio0'], $arr[$k]['relay'.$j]['radio1'], $arr[$k]['relay'.$j]['radio2'], $arr[$k]['relay'.$j]['radio3'], $arr[$k]['relay'.$j]['finish'], $displayed_relay[$j], $arr[$k]['relay'.$j]['cumul']);
						$out[$k] = array_merge($out[$k], $temp_arr);
					}
					for($j=1;$j<=$numlegs;$j++)
					{
            if(isset($arr[$k]['relay'.$j]['name']))
            {
              $out[$k][] = $arr[$k]['relay'.$j]['name'];
            }
            else
            {
              $out[$k][] = '';
            }
					}
				}
			}
		}
	}
    if($sta != null)
	{
        foreach($sta as $k => $v)
        {
            if($v > 1)
            {
                if(!isset($out[$k]))
                {
                    $place_affichee = '';
                    for($j=1;$j<=$numlegs;$j++)
                    {
                      $displayed_relay[$j] = '';
                    }
                    $out[$k] = array($arr[$k]['team_stat'], $arr[$k]['timestamp']);
                    for($j=1;$j<=$numlegs;$j++)
                    {
                      $out[$k][] = $arr[$k]['relay'.$j]['tstat'];
                    }
                    $out[$k] = array_merge($out[$k], array($numlegs, $place_affichee, $arr[$k]['team_name']));
                    for($j=1;$j<=$numlegs;$j++)
                    {
                      $temp_arr = array($arr[$k]['relay'.$j]['radio0'], $arr[$k]['relay'.$j]['radio1'], $arr[$k]['relay'.$j]['radio2'], $arr[$k]['relay'.$j]['radio3'], $arr[$k]['relay'.$j]['finish'], $displayed_relay[$j], $arr[$k]['relay'.$j]['cumul']);
                      $out[$k] = array_merge($out[$k], $temp_arr);
                    }
                    for($j=1;$j<=$numlegs;$j++)
                    {
                      if(isset($arr[$k]['relay'.$j]['name']))
                      {
                        $out[$k][] = $arr[$k]['relay'.$j]['name'];
                      }
                      else
                      {
                        $out[$k][] = '';
                      }
                    }
                }
            }
        }
	}
	for($j=1;$j<=$numlegs;$j++)
	{
		foreach($out as $k => $v)
		{
			$min = 5 + $numlegs + 7 * ($j-1);//6 * ($j-1);
			$max = $min + 6;//5;
			for($i=$min;$i<=$max;$i++)
			{
				//echo '-'.$j.'/'.$i.'-';
				if(($i == $max) && ($rel_tstat[$j][$k] > 1))
				{
					$out[$k][$i] = newGetStatusString($rel_tstat[$j][$k]);
				}
				else
				if(($i == $max) && ($rel_stat[$j][$k] > 1))
				{
					$out[$k][$i] = newGetStatusString($rel_stat[$j][$k]);
				}
				else
				if(($i == ($max -2)) && ($rel_stat[$j][$k] > 1))
				{
					$out[$k][$i] = newGetStatusString($rel_stat[$j][$k]);
				}
				else
				if($i != ($max-1))
				{
					$v[$i] = intval($v[$i]);
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
	}
	return $out;
}
function formatRelayResults($result, $limit = 99999)
{
  global $pos;

  $head = true;
  print '[';
  $i = 0;
  foreach($result as $row)
  {
    $i++;
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
    if($i >= $limit)
      break;
  }
  print "];";
}
function selectRadio($cls) {
  global $cmpId;
  $radio = '';
  $sql = "SELECT leg, ctrl, mopcontrol.name FROM mopclasscontrol, mopcontrol ".
         "WHERE mopcontrol.cid='$cmpId' AND mopclasscontrol.cid='$cmpId' ".
         "AND mopclasscontrol.id='$cls' AND mopclasscontrol.ctrl=mopcontrol.id ORDER BY leg ASC, ord ASC";
  $link = ConnectToDB();
  $res = mysqli_query($link, $sql);
  $radios = mysqli_num_rows($res);
  if ($radios > 0)
  {
    if (isset($_GET['radio'])) {
      $radio = $_GET['radio'];
    }
    while ($r = mysqli_fetch_array($res)) {
      print '<a href="'.$_SERVER['PHP_SELF']."?cls=$cls&radio=$r[ctrl]".'">'.$r['name']."</a><br/>\n";
    }
    print '<a href="'.$_SERVER['PHP_SELF']."?cls=$cls&radio=finish".'">'.'Finish'."</a><br/>\n";
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
   $link = ConnectToDB();
  $res = mysqli_query($link, $sql);
  $radios = mysqli_num_rows($res);
  //print $sql;
  if ($radios > 0) {
    while ($r = mysqli_fetch_array($res)) {
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
  $link = ConnectToDB();
  $ifc = "cid='$cid' AND id='$id'";
  $res = mysqli_query($link, "SELECT id FROM `$table` WHERE $ifc");
  if (mysqli_num_rows($res) > 0) {
    $sql = "UPDATE `$table` SET $sqlupdate WHERE $ifc";
  }
  else {
    $sql = "INSERT INTO `$table` SET cid='$cid', id='$id', $sqlupdate";
  }
  //print "$sql\n";
  mysqli_query($link, $sql);
}
/** Update a link with outer level over legs and other level over fieldName (controls, team members etc)*/
function updateLinkTable($table, $cid, $id, $fieldName, $encoded) {
  $sql = "DELETE FROM $table WHERE cid='$cid' AND id='$id'";
  $link = ConnectToDB();
  mysqli_query($link, $sql);
  $legNumber = 1;
  $legs = explode(";", $encoded);
  foreach($legs as $leg) {
    $runners = explode(",", $leg);
    foreach($runners as $key => $runner) {
      $sql = "INSERT INTO $table SET cid='$cid', id='$id', leg=$legNumber, ord=$key, $fieldName=$runner";
      //print "$sql \n";
      mysqli_query($link, $sql);
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
   $link = ConnectToDB();
   $tables = array(0=>"mopcontrol", "mopclass", "moporganization", "mopcompetition", "mopcompetitor",
                      "mopteam", "mopteammember", "mopclasscontrol", "mopradio");
   foreach($tables as $table) {
     $sql = "DELETE FROM $table WHERE cid=$cid";
     mysqli_query($link, $sql);
   }
}
/** Update control table */
function processCompetition($cid, $cmp) {
  $link = ConnectToDB();
  if(!isset($cmp['delete']))
  {
  	$name = mysqli_real_escape_string($link, substr($cmp, 0, 64));
  	$date = mysqli_real_escape_string($link, $cmp['date']);
  	$organizer = mysqli_real_escape_string($link, $cmp['organizer']);
  	$homepage = mysqli_real_escape_string($link, $cmp['homepage']);
  	$sqlupdate = "name='$name', date='$date', organizer='$organizer', homepage='$homepage'";
  	updateTable("mopcompetition", $cid, 1, $sqlupdate);
  }
}
/** Update control table */
function processControl($cid, $ctrl) {
  $link = ConnectToDB();
  if(!isset($ctrl['delete']))
  {
  	$id = mysqli_real_escape_string($link, $ctrl['id']);
  	$name = mysqli_real_escape_string($link, $ctrl);
  	$sqlupdate = "name='$name'";
  	updateTable("mopcontrol", $cid, $id, $sqlupdate);
  }
}
/** Update class table */
function processClass($cid, $cls) {
  $link = ConnectToDB();
  if(!isset($cls['delete']))
  {
  	$id = mysqli_real_escape_string($link , $cls['id']);
  	$ord = mysqli_real_escape_string($link , $cls['ord']);
  	$name = mysqli_real_escape_string($link , $cls);
  	$sqlupdate = "name='$name', ord='$ord'";
  	updateTable("mopclass", $cid, $id, $sqlupdate);
  	if (isset($cls['radio'])) {
    	$radio = mysqli_real_escape_string($link , $cls['radio']);
    	updateLinkTable("mopclasscontrol", $cid, $id, "ctrl", $radio);
  	}
  }
}
/** Update organization table */
function processOrganization($cid, $org) {
  $link = ConnectToDB();
  if(!isset($org['delete']))
  {
  	$id = mysqli_real_escape_string($link , $org['id']);
  	$name = mysqli_real_escape_string($link , $org);
  	$sqlupdate = "name='$name'";
  	updateTable("moporganization", $cid, $id, $sqlupdate);
  }
}
/** Update competitor table */
function processCompetitor($cid, $cmp) {
  $link = ConnectToDB();
  if(!isset($cmp['delete']))
  {
  	$base = $cmp->base;
  	$id = mysqli_real_escape_string($link, $cmp['id']);
  	$name = mysqli_real_escape_string($link, $base);
  	$org = (int)$base['org'];
  	$cls = (int)$base['cls'];
  	$stat = (int)$base['stat'];
  	$st = (int)$base['st'];
  	$rt = (int)$base['rt'];
  	$flag = mysqli_real_escape_string($link, $base['flag']);
  	$now = time();
  	$sqlupdate = "name='$name', org=$org, cls=$cls, stat=$stat, st=$st, rt=$rt, country='$flag', timestamp=$now";
  	if (isset($cmp->input)) {
    	$input = $cmp->input;
    	$it = (int)$input['it'];
    	$tstat = (int)$input['tstat'];
    	$sqlupdate.=", it=$it, tstat=$tstat";
  	}
    if (isset($base['pts'])) {
      $rogpoints = (int)$base['pts'];
      $rogpointsgross = (int)$base['ptsgr'];
    	$sqlupdate.=", rogpoints=$rogpoints, rogpointsgross=$rogpointsgross";
    }
  	updateTable("mopcompetitor", $cid, $id, $sqlupdate);
	/*
 	// ORIGINAL
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
	*/
  	if (isset($cmp->radio))
  	{
    	$radios = explode(";", $cmp->radio);
    	foreach($radios as $radio)
    	{
      	$tmp = explode(",", $radio);
      	$radioId = (int)$tmp[0];
      	$radioTime = (int)$tmp[1];
      	$sql = "SELECT 1 FROM mopradio WHERE cid='$cid' AND id='$id' AND ctrl='$radioId'";
      	$res = mysqli_query($link, $sql);
      	if(mysqli_num_rows($res))
      	{
        	$sql = "UPDATE mopradio SET rt='$radioTime' WHERE cid='$cid' AND id='$id' AND ctrl='$radioId'";
        	mysqli_query($link, $sql);
      	}
      	else
      	{
        	$sql = "INSERT INTO mopradio SET cid='$cid', id='$id', ctrl='$radioId', rt='$radioTime', timestamp=$now";
        	mysqli_query($link, $sql);
      	}
    	}
  	}
  }
}
/** Update team table */
function processTeam($cid, $team) {
  $link = ConnectToDB();
  if(!isset($team['delete']))
  {
  	$base = $team->base;
  	$id = mysqli_real_escape_string($link, $team['id']);
  	$name = mysqli_real_escape_string($link, $base);
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
}
/** MOP return code. */
function returnStatus($stat) {
  die('<?xml version="1.0"?><MOPStatus status="'.$stat.'"></MOPStatus>');
}
/* MW */

function doJsFormat($val)
{
  return addslashes($val); // addslashes(addslashes($val)); // $val;//
}

function defineVariable($variable, $value)
{
    print $variable." = eval('".doJsFormat($value)."');";
}
function defineVariableArr($variable, $value1, $value2, $value3 = null, $value4 = null)
{
  if(($value3 !== null) && ($value4 !== null))
  {
    print $variable." = eval(['".doJsFormat($value1)."', '".doJsFormat($value2)."', '".doJsFormat($value3)."', '".doJsFormat($value4)."']);\r\n";
  }
  else
  if($value3 !== null)
  {
    print $variable." = eval(['".doJsFormat($value1)."', '".doJsFormat($value2)."', '".doJsFormat($value3)."']);\r\n";
  }
  else
  {
    print $variable." = eval(['".doJsFormat($value1)."', '".doJsFormat($value2)."']);\r\n";
  }
}
function defineVariableArrFromArr($variable, $arr)
{
    print $variable." = Array;";
    foreach($arr as $k => $v)
    {
        print $variable."[".$k."] = eval('".doJsFormat($v)."');\r\n";
    }
}
function defineVariableArr2x($variable, $arr1, $arr2)
{
    $arr1tmp = array_map("doJsFormat", $arr1);
    $arr2tmp = array_map("doJsFormat", $arr2);
    $em1 = '[\''.implode('\', \'', $arr1tmp).'\']';
    $em2 = '[\''.implode('\', \'', $arr2tmp).'\']';
    print $variable." = eval([".$em1.", ".$em2."]);\r\n";
}
function defineVariableArrNx($variable, $arrMultiDimension, $nbVoulu)
{
  $em = array();
  for($i=0;$i<$nbVoulu;$i++)
  {
    if(isset($arrMultiDimension[$i]))
    {
      $arrtmp = array_map("doJsFormat", $arrMultiDimension[$i]);
      $em[] = '[\''.implode('\', \'', $arrtmp).'\']';
    }
    else
    if(isset($arrMultiDimension[0]))
    {
      $arrtmp = array_map("doJsFormat", $arrMultiDimension[0]);
      $em[] = '[\''.implode('\', \'', $arrtmp).'\']';
    }
    else
      $em[] = '[\''.implode('\', \'', array(-1)).'\']';
  }
  print $variable." = eval([".implode(',',$em)."]);\r\n";
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
function displayContentPicture($picture, $panel, $panelcount)
{
    $max_width = floor(980 / $panelcount);
    $max_height = 470;
    $retour = '';
    $arr_img = getimagesize ('pictures/'.$picture);
    $or_width = $arr_img[0];
    $or_height = $arr_img[1];
    $ratio_h = $or_height / $max_height;
    $ratio_w = $or_width / $max_width;

    //screen.width screen.height

  $retour .= '<div id="imgpan'.$panel.'" style="display:table;overflow:hidden;margin:auto;">';
  $retour .= '<div id="imgp'.$panel.'" style="padding:2px;display:table-cell;vertical-align:middle;text-align:center;"><img id="imgs'.$panel.'" src="pictures/'.$picture.'" alt="L" title="L" /></div>';
  $retour .= '</div>';
  $retour .= '<script type="text/javascript">';
  $retour .= 'function computeSize'.$panel.'()';
  $retour .= '{';
  $retour .= 'ratio_device = 1.25;';
  $retour .= 'screenw = (screen.height - 70) / '.$panelcount.' / ratio_device;';
  $retour .= 'screenh = (screen.width - 140) / ratio_device;';
  $retour .= 'imgw = '.$or_width.';';
  $retour .= 'imgh = '.$or_height.';';
  $retour .= 'ratio_h = imgh / screenh;';
  $retour .= 'ratio_w = imgw / screenw;';
  $retour .= 'ratio = Math.max(ratio_h, ratio_w);';
  $retour .= 'h = imgh / ratio;';
  $retour .= 'w = imgw / ratio;';
  $retour .= 'return {h:h, w:w};';
  $retour .= '}'."\n";
  $retour .= 'var mysize'.$panel.' = computeSize'.$panel.'();'."\n";
  $retour .= 'document.getElementById("imgpan'.$panel.'").style.height = mysize'.$panel.'.h;'."\n";
  $retour .= 'document.getElementById("imgpan'.$panel.'").style.width = mysize'.$panel.'.w;'."\n";

  $retour .= 'document.getElementById("imgp'.$panel.'").style.height = mysize'.$panel.'.h;'."\n";
  $retour .= 'document.getElementById("imgp'.$panel.'").style.width = mysize'.$panel.'.w;'."\n";

  $retour .= 'document.getElementById("imgs'.$panel.'").height = mysize'.$panel.'.h;'."\n";
  $retour .= 'document.getElementById("imgs'.$panel.'").width = mysize'.$panel.'.w;'."\n";
           /*
  $retour .= 'document.write("*" + imgh + "*" + imgw + "*<br />");';
  $retour .= 'document.write("/" + ratio_h + "/" + ratio_w + "/<br />");';
  $retour .= 'document.write("+" + screenh + "+" + screenw + "+" + screen.width + "x" + screen.availWidth + "x" + screen.availHeight + "+<br />");';
  $retour .= 'document.write("*" + mysize'.$panel.'.h + "*" + mysize'.$panel.'.w + "*");';
        */
  $retour .= '</script>';
    /*
    if($or_width >= $or_height)
    {
        $retour .= '<div style="display:table;height:470px;overflow:hidden;width:'.$max_width.'px;">';
        $retour .= '<div style="padding:2px;display:table-cell;vertical-align:middle;width:100%;text-align:center;"><img src="pictures/'.$picture.'" alt="H" title="H" max-width="100%" width="100%" /></div>';
        $retour .= '</div>';
//        $retour .= '<div style="padding:2px;text-align:center;"><img src="pictures/'.$picture.'" alt="H" title="H" max-height="470px" height="470px"/></div>';
    }
    else
    {

//        $retour .= '<div style="padding:2px;text-align:center;"><img src="pictures/'.$picture.'" alt="L" title="L" max-height="470px"; vertical-align:middle;/></div>';
//        $retour .= '<div style="padding:2px;text-align:center;"><img src="pictures/'.$picture.'" alt="" max-height="470px" height="470px" /></div>';
    }
    */
    return $retour;
}
function displayContentBlog($rcid, $nlines, $highlight, $panel)
{
  $link = ConnectToDB();
  $content = '';
  $highlight_s = $highlight * 60;
  $sql = 'SELECT * FROM resultblog WHERE rcid='.$rcid.' ORDER BY timestamp DESC LIMIT '.$nlines;
  $res = mysqli_query($link, $sql);
  $content .= '<ul class="blogview" id="bloglist'.$panel.'">';
  if(mysqli_num_rows($res))
  {
    $now = time();
    $arr_data = array();
    while($r = mysqli_fetch_assoc($res))
    {
      $mytimestamp = strtotime($r['timestamp']);
      $mytext = '';
      if($highlight_s > ($now - $mytimestamp))
      {
        $mytext .= '<li class="blogrecent">';
      }
      else
      {
        $mytext .= '<li>';
      }
      $mytext .= '<span class="blogtime">'.date('H:i:s', $mytimestamp).'</span>';//date('d-m-Y H:i:s', $mytimestamp);

      $mytext .= htmlspecialchars($r['text']);
      $arr_data[] = $mytext;
    }
    if($arr_data != null)
    {
      $content .= implode('</li>', $arr_data).'</li>';
    }
  }
  $content .= '</ul>';
  return $content;
}
function displayContentRadio($rcid, $nlines, $highlight, $panel)
{
  $link = ConnectToDB();
  $content = '';
  $highlight_s = $highlight * 60;
  //$sql = 'SELECT * FROM resultblog WHERE rcid='.$rcid.' ORDER BY timestamp DESC LIMIT '.$nlines;
  //$res = mysqli_query($link, $sql);
  $content .= '<table class="radioview" cellspacing="0" cellpadding="0" id="radiolist'.$panel.'">';
  /*if(mysqli_num_rows($res))
  {
    $now = time();
    $arr_data = array();
    while($r = mysqli_fetch_assoc($res))
    {
      $mytimestamp = strtotime($r['timestamp']);
      $mytext = '';
      if($highlight_s > ($now - $mytimestamp))
      {
        $mytext .= '<li class="radiorecent">';
      }
      else
      {
        $mytext .= '<li>';
      }
      $mytext .= '<span class="radiotime">'.date('H:i:s', $mytimestamp).'</span>';//date('d-m-Y H:i:s', $mytimestamp);

      $mytext .= htmlspecialchars($r['text']);
      $arr_data[] = $mytext;
    }
    if($arr_data != null)
    {
      $content .= implode('</li>', $arr_data).'</li>';
    }
  }*/
  $content .= '</table>';
  return $content;
}
function displayTopPicture($picture, $hauteur)
{
    $retour = '';
    $arr_img = getimagesize ('pictures/'.$picture);
    $height_txt = '';
    $maxheight_txt = '';
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
  while ($r = mysqli_fetch_array($res)) {
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

function addFlag($country_flag)
{
	$txt = '';
	$curflag = strtolower($country_flag);
	if(file_exists('img/flags-mini/'.$curflag.'.png'))
	{
		$txt = '<img src="img/flags-mini/'.$curflag.'.png" alt="'.$curflag.'" title="'.$curflag.'" class="countryflag" />';
	}
	return $txt;
}
?>
