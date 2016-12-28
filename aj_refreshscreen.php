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
	
	$rcid = ((isset($_GET['rcid'])) ? intval($_GET['rcid']) : 0);
	$out = array();
	$now = time();
	
	if($rcid > 0)
	{
		$sql = 'SELECT panel1lastrefresh, panel1lastredraw, panel2lastrefresh, panel2lastredraw, panel3lastrefresh, panel3lastredraw, panel4lastrefresh, panel4lastredraw FROM resultscreen WHERE rcid='.$rcid.' ORDER BY sid ASC';
		$res = mysqli_query($link, $sql) or exit;
		$num = mysqli_num_rows($res);
		if($num)
		{
			while($current = mysqli_fetch_assoc($res))
			{
				$out[] = $current['panel1lastrefresh']; // max($current['panel1lastrefresh'], $current['panel2lastrefresh'], $current['panel3lastrefresh'], $current['panel4lastrefresh']);
				$out[] = $current['panel1lastredraw']; // max($current['panel1lastredraw'], $current['panel2lastredraw'], $current['panel3lastredraw'], $current['panel4lastredraw']);
			}
		}
		
	}
	if($out != null)
	{
		print '['.$now.','.implode(', ', $out).'];';
	}
	else
	{
		print '['.$now.'];';
	}
