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
  date_default_timezone_set('Europe/Paris');
	
	$rcid = ((isset($_GET['rcid'])) ? intval($_GET['rcid']) : 0);
  $sid = ((isset($_GET['sid'])) ? $_GET['sid'] : "0");
  
  $limit = ((isset($_GET['limit'])) ? intval($_GET['limit']) : 0);
  $out = array();
	$now = time();
	
  if(($sid > 0) && ($rcid > 0))
  {
    $sql = 'UPDATE resultscreen SET panel1lastrefresh='.$now.' WHERE rcid='.$rcid.' AND sid='.$sid;
    mysqli_query($link, $sql);
  }
	
	if(($rcid > 0) && ($limit > 0))
	{
		$sql = 'SELECT * FROM resultblog WHERE rcid='.$rcid.' ORDER BY timestamp DESC LIMIT '.$limit;
		$res = mysqli_query($link, $sql) or exit;
		$num = mysqli_num_rows($res);
		if($num)
		{
			while($current = mysqli_fetch_assoc($res))
			{
        $mytimestamp = strtotime($current['timestamp']);
        $mydate = date('H:i:s', $mytimestamp);
        $mytimestamp = $now - $mytimestamp; 
				$out[] = '[\''.($mytimestamp).'\', \''.str_replace(array("\r", "\n"), array(' ', ' '), htmlspecialchars(addslashes(utf8_encode($current['text'])))).'\', \''.($mydate).'\']';
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
