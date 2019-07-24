<?php

  $ip=$_SERVER['REMOTE_ADDR'];
  $ipnb=explode('.',$ip);
  if (($ipnb[0]!='192')||($ipnb[1]!='168')||($ipnb[2]!='0')||($ipnb[3]=='20'))
  {
      header("Location: http://192.168.0.10/cfco/show.php");
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
      $inc = 1; // changer ici le start
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
  	<b>Pour la configuration des écrans <a href="http://192.168.0.10/cfco/screenconfig.php">cliquez ici !</a></b>
    <br/>
    <br/>
    <hr>
    <b>Aide à la configuration de MeOS</b><br/>
    <b>Premier identifiant de competition disponible :<span style="color:blue;font-size:2em;"> <?php echo $new_cid; ?></span></b>
    <br/>
    <br/>
    <b>URL pour MEOS</b> (faire un copier/coller) : &nbsp;http:192.168.0.10/cfco/update.php
    <br/>
    <br/>
    <img src='config.jpg' title='MeOS configuration'></img>
    <br/>
    <br/>
    <hr>
    <b>Configuration du NUC (pour les gourous !)</b><br/>
    <br/>
    <table border=1>
      <tr>
        <th colspan=2>Configuration</th>
      </tr>
      <tr>
        <td>Connexion MeOS</td>
        <td>resultpaca</td>
      </tr>
      <tr>
        <td>Ubuntu Nom</td>
        <td>LiguePacaCO</td>
      </tr>
      <tr>
        <td>Ubuntu User</td>
        <td>lpacaco</td>
      </tr>
      <tr>
        <td>Ubuntu password</td>
        <td>lpacaco</td>
      </tr>
      <tr>
        <td>MySQL compte</td>
        <td>lpacaco</td>
      </tr>
      <tr>
        <td>MySQL password</td>
        <td>lpacaco</td>
      </tr>
      <tr>
        <td>PhpMyAdmin compte</td>
        <td>root</td>
      </tr>
      <tr>
        <td>PhpMyAdmin password</td>
        <td>lpacaco</td>
      </tr>
      <tr>
        <td>Serveur Apache</td>
        <td>/var/www/html</td>
      </tr>
      <tr>
        <td>Répertoire gestion écran</td>
        <td>192.168.0.10/cfco</td>
      </tr>
    </table>

  </body>
</html>
