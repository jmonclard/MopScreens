<?php
  session_start();
  date_default_timezone_set('UTC');
  include_once('functions.php');
  redirectSwitchUsers();
  
  
  include_once('lang.php');
  
  
  include_once('config.php');

  $_SESSION['CurrentLanguage'] = isset($_SESSION['CurrentLanguage']) ? $_SESSION['CurrentLanguage'] : autoSelectLanguage(array('fr','en','sv'),'en');
  

  $PHP_SELF = $_SERVER['PHP_SELF'];
  ConnectToDB();
  
  if(isset($_GET['action']))
  {
    $action = trim($_GET['action']);
    switch($action)
    {
      case 'clear':
        $sql = 'TRUNCATE resultradio';
        mysql_query($sql);
        header('location: screenconfig.php');
      break;
      default:
      break;
    }
  }
  else
  if((isset($_GET['idsender']))
    && (isset($_GET['idreceiver']))
    &&(isset($_GET['senderbattery']))
    &&(isset($_GET['rxlevel'])))
  {
    $idsender = intval($_GET['idsender']);
    $idreceiver = intval($_GET['idreceiver']);
    $senderbattery = intval($_GET['senderbattery']);
    $rxlevel = intval($_GET['rxlevel']);
    
    $sql = 'INSERT INTO resultradio (idsender, idreceiver, senderbattery, rxlevel) VALUES ('.$idsender.', '.$idreceiver.', '.$senderbattery.', '.$rxlevel.')';
    mysql_query($sql);
  }