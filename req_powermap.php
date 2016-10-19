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
  
  $arr_radio = array();
  $radioconfig = array();
  $srcmap = '';
  $sizeX = 1000;
  $sizeY = 1000;
  $sql = 'SELECT * FROM resultradioconfig WHERE active=1';
  $res = mysql_query($sql);
  if(1 == mysql_num_rows($res))
  {
    $radioconfig = mysql_fetch_array($res);
    $sql = 'SELECT * FROM resultradioposition WHERE srcid='.$radioconfig['srcid'].' ORDER BY radioid ASC';
    $res = mysql_query($sql);
    if(mysql_num_rows($res))
    {
      while ($r = mysql_fetch_array($res))
      {
        $arr_radio[] = '['.$r['radioid'].','.$r['radiox'].','.$r['radioy'].']'; 
      }
    }
  }
  
  if($radioconfig != null)
  {
    // 0:haut gauche 1:bas droit
    echo 'var positionX0 = '.$radioconfig['srcx0'].';'."\n";
    echo 'var positionY0 = '.$radioconfig['srcy0'].';'."\n";
    echo 'var positionX1 = '.$radioconfig['srcx1'].';'."\n";
    echo 'var positionY1 = '.$radioconfig['srcy1'].';'."\n";
    $srcmap = 'pictures/'.htmlspecialchars($radioconfig['srcmap']);
    if(file_exists(dirname(__FILE__).'/'.$srcmap))
    {
      $size = getimagesize($srcmap);
      if($size)
      {
        $sizeX = $size[0];
        $sizeX = $size[1];
      }
    }
  }
  else
  {
    echo 'var positionX0 = 0;'."\n";
    echo 'var positionY0 = 0;'."\n";
    echo 'var positionX1 = 100;'."\n";
    echo 'var positionY1 = 100;'."\n";
  }
  
  if($arr_radio != null)
  {
    echo 'var positionInfo = ['.implode(', '."\n", $arr_radio).'];';
  }
  else
  {
    echo 'var positionInfo = [];';
  }