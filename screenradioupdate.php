<?php
  session_start();
  date_default_timezone_set('Europe/Paris');
  include_once('functions.php');
  redirectSwitchUsers();
  
  
  include_once('lang.php');
  
  
  include_once('config.php');

  $_SESSION['CurrentLanguage'] = isset($_SESSION['CurrentLanguage']) ? $_SESSION['CurrentLanguage'] : autoSelectLanguage(array('fr','en','sv'),'en');
  

  $PHP_SELF = $_SERVER['PHP_SELF'];
  $link = ConnectToDB();
  
  if(isset($_GET['action']))
  {
    $action = trim($_GET['action']);
    switch($action)
    {
      case 'clear':
        $sql = 'TRUNCATE resultradio';
        mysqli_query($link, $sql);
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
    &&(isset($_GET['rxlevel']))
    &&(isset($_GET['status'])))
  {
    $idsender = intval($_GET['idsender']);
    $idreceiver = intval($_GET['idreceiver']);
    $senderbattery = intval($_GET['senderbattery']);
    $rxlevel = intval($_GET['rxlevel']);
    $status = intval($_GET['status']);
    
    $sql = 'INSERT INTO resultradio (idsender, idreceiver, senderbattery, rxlevel, status) VALUES ('.$idsender.', '.$idreceiver.', '.$senderbattery.', '.$rxlevel.', '.$status.')';
    mysqli_query($link, $sql);
  }