<?php

  $ip=$_SERVER['REMOTE_ADDR'];
  $ipnb=explode('.',$ip);
  if (($ipnb[0]!='192')||($ipnb[1]!='168')||($ipnb[2]!='0')||($ipnb[3]=='20'))
  {
      header("Location: http://<ToBeDefined>/show.php");
      exit;//die();
  }
  
  // ajout 20170518
  session_start();
  date_default_timezone_set('UTC');
  //date_default_timezone_set('Europe/Paris');
  include_once('functions.php');
  include_once('lang.php');
  include_once('screenfunctions.php');
  include_once('config.php');
  $PHP_SELF = $_SERVER['PHP_SELF'];
//$link = ConnectToDB();

  $sql = 'SELECT cid FROM mopcompetition ORDER BY cid ASC';
  $res = mysqli_query($link,$sql);
  $new_cid = 444444;
  if (mysqli_num_rows($res))
  {
    $arr_cid = array();
    while($r = mysqli_fetch_assoc($res))
    {
      $arr_cid[] = $r['cid'];
    }
    $max_cid = max($arr_cid);
    $count_cid = count($arr_cid);
    if($count_cid < $max_cid)
    {
      $inc = 10; // changer ici le start
      while(in_array($inc, $arr_cid))
      {
        $inc++;
      }
      $new_cid = $inc;
    }
    else
    {
      $new_cid = $max_cid + 1;
    }
  }
  else
  {
    $new_cid = 777777;
  }
  // ajout 20170518

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <meta name="generator" content="PSPad editor, www.pspad.com">
  <title>Screen configuration portail</title>
  </head>
  <body>
  	<b>For screens configuration <a href="http://<ToBeDefined>/screenconfig.php">click here !</a></b>
    <br/>
    <br/>
    <hr>
    <b>Help for MeOS configuration</b><br/>
    <b>First available competition ID :<span style="color:blue;font-size:2em;"> <?php echo $new_cid; ?></span></b>
    <br/>
    <br/>
    <b>URL to be used in MEOS service &nbsp;</b>http://<ToBeDefined>/update.php
    <br/>
    <br/>
    <img src='config.jpg' title='MeOS configuration'></img>
    <br/>
  </body>
</html>
