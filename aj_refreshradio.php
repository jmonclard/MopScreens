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
  //date_default_timezone_set('Europe/Paris');
  date_default_timezone_set('UTC');
	
	$rcid = ((isset($_GET['rcid'])) ? intval($_GET['rcid']) : 0);
  $sid = ((isset($_GET['sid'])) ? $_GET['sid'] : "0");
  
  $cmpId = ((isset($_GET['cmpId'])) ? $_GET['cmpId'] : "0");
  $cls = ((isset($_GET['cls'])) ? intval($_GET['cls']) : 0);
  $radioid = ((isset($_GET['radioid'])) ? intval($_GET['radioid']) : 0);
  $limit = ((isset($_GET['limit'])) ? intval($_GET['limit']) : 0);
  $mode = ((isset($_GET['mode'])) ? intval($_GET['mode']) : CST_MODE_INDIVIDUAL);
  $out = array();
	$now = time();
  
  $sql = 'UPDATE resultscreen SET panel1lastrefresh='.$now.' WHERE rcid='.$rcid.' AND sid='.$sid;
  mysqli_query($link, $sql);
  
  if(($mode != CST_MODE_INDIVIDUAL) && ($mode != CST_MODE_RELAY))
  {
    $mode = CST_MODE_INDIVIDUAL;
  }
	
	
	if(($cmpId > 0) && ($limit > 0) && ($cls > 0))
	{
    $sql = '';
    if($mode == CST_MODE_RELAY)
    {
      $sql = 'SELECT mr.*, mc.name, mt.name AS orgname FROM mopradio AS mr INNER JOIN mopcompetitor AS mc ON mc.cid=mr.cid AND mr.id=mc.id INNER JOIN mopteammember AS mtm ON mtm.cid=mc.cid AND mtm.rid=mc.id INNER JOIN mopteam AS mt ON mt.cid=mtm.cid AND mt.id=mtm.id WHERE mc.cls='.$cls.' AND mr.cid='.$cmpId.' AND mr.ctrl='.$radioid.' ORDER BY mr.timestamp DESC LIMIT '.$limit;
    }
    else
    {
      $sql = 'SELECT mr.*, mc.name, mo.name AS orgname FROM mopradio AS mr INNER JOIN mopcompetitor AS mc ON mc.cid=mr.cid AND mr.id=mc.id INNER JOIN moporganization AS mo ON mo.cid=mc.cid AND mo.id=mc.org WHERE mc.cls='.$cls.' AND mr.cid='.$cmpId.' AND mr.ctrl='.$radioid.' ORDER BY mr.timestamp DESC LIMIT '.$limit;
    }
		$res = mysqli_query($link, $sql) or exit;
		$num = mysqli_num_rows($res);
		if($num)
		{
			while($current = mysqli_fetch_assoc($res))
			{
        //print_r($current);
        $mytimestamp = ($now - $current['timestamp']);
        $mydate = date('i:s', $mytimestamp);
        //$mytimestamp = $now - $mytimestamp; 
				$out[] = '[\''.($mytimestamp).'\', \''.htmlspecialchars(addslashes($current['name'])).'\', \''.htmlspecialchars(addslashes($current['orgname'])).'\', \''.($mydate).'\']';
			}
		}
		
	}
	if($out != null)
	{
		print '['.implode(', ', $out).'];';
	}
	else
	{
		print '[];';
	}
