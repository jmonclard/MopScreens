<?php
  session_start();
  date_default_timezone_set('UTC');
  include_once('functions.php');
  include_once('lang.php');
  redirectSwitchUsers();
  include_once('config.php');

  $_SESSION['CurrentLanguage'] = isset($_SESSION['CurrentLanguage']) ? $_SESSION['CurrentLanguage'] : autoSelectLanguage(array('fr','en','sv'),'en');
  

  $PHP_SELF = $_SERVER['PHP_SELF'];
  ConnectToDB();
  

  $sql = 'SELECT * FROM (SELECT * FROM resultradio ORDER BY timestamp DESC) x GROUP BY idsender';
  $res = mysql_query($sql);
  $now = time();
  //echo 'id, battery, age'.'<br />';
  $batteryInfo = array();
  while ($r = mysql_fetch_array($res))
  {
    //echo $r['idsender'].', '.$r['senderbattery'].', '.($now - strtotime($r['timestamp'])).'<br/>';
    $batteryInfo[] = '['.$r['idsender'].', '.$r['senderbattery'].', '.($now - strtotime($r['timestamp'])).']';
  }
  //echo '<hr />';
  $sql = 'SELECT * FROM (SELECT * FROM resultradio ORDER BY timestamp DESC) x GROUP BY idsender, idreceiver';
  $res = mysql_query($sql);
  $now = time();
  //echo 'sender id, receiver id, rx level, age'.'<br />';
  $levelsInfo = array();
  while ($r = mysql_fetch_array($res))
  {
    //echo $r['idsender'].', '.$r['idreceiver'].', '.$r['rxlevel'].', '.($now - strtotime($r['timestamp'])).'<br/>';
    $levelsInfo[] = '['.$r['idsender'].', '.$r['idreceiver'].', '.$r['rxlevel'].', '.($now - strtotime($r['timestamp'])).']';
  }
  $output = '';
  if($batteryInfo != null)
  {
    $output = '['.implode(', ', $batteryInfo).']';
  }
  else
  {
    $output = '[]';
  }
  
  if($levelsInfo != null)
  {
    $output = '['.$output.', ['.implode(', ', $levelsInfo).']'.'];';
  }
  else
  {
    $output = '['.$output.', []'.'];';
  }
  echo $output;
  
  
  /*
  var batteryInfo = // id, battery, age
[
    [0, 1000, 381],
    [1, 1001, 381],
    [2, 1002, 381],
    [3, 1003, 381],
    [4, 1004, 381],
    [5, 1005, 381],
    [6, 1006, 381],
    [7, 1007, 381]
];
//  var levelsInfo = [];
  var levelsInfo = //senderid, receiverid, rxlevel, age
[
    [0, 1, -100, 20],
    [1, 2, -99, 20],
    [1, 5, -20, 80],
    [2, 3, -98, 30],
    [3, 4, -97, 20],
    [3, 5, -60, 45],
    [4, 5, -96, 5],
    [5, 6, -95, 1],
    [6, 7, -94, 666]
];
  var totalInfo = [batteryInfo, levelsInfo];
  */