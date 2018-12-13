<?php
  /*
  Copyright 2016 Metraware
  
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

  
  session_start();
  //date_default_timezone_set('Europe/Paris');
  date_default_timezone_set('UTC');
  include_once('functions.php');
  redirectSwitchUsers();
  
	include_once('lang.php');
  include_once('screenfunctions.php');


  $_SESSION['CurrentLanguage'] = isset($_SESSION['CurrentLanguage']) ? $_SESSION['CurrentLanguage'] : autoSelectLanguage(array('fr','en','sv'),'en');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Radio Configs</title>
    <link rel="stylesheet" type="text/css" href="styles/screen.css" />
    
    <script type="text/javascript">

      function AddRadioConfig(prompt_text,srcid)
      {
        defaultname="New radio conf "+srcid.toString();
        name=prompt(prompt_text,defaultname);
        if (name!="null")
        {
          location.replace("screenradioconfig.php?action=add&srcid="+srcid+"&name="+name);
        }
      }        

      function EditRadioConfig(srcid)
      {
        location.replace("screenradioedit.php?srcid="+srcid);
      }

      function PlayRadioConfig(srcid)
      {
        location.replace("screenradioconfig.php?action=play&srcid="+srcid);
      }

      function RenameRadioConfig(prompt_text,srcid,oldname)
      {
        name=prompt(prompt_text,oldname);
        if (name!="null")
        {
          location.replace("screenradioconfig.php?action=rename&srcid="+srcid+"&name="+name);
        }
      }

      function CloneRadioConfig(prompt_text,oldsrcid,newsrcid)
      {
        defaultname="New conf "+newsrcid.toString();
        name=prompt(prompt_text,defaultname);
        if (name!='null')
        {
          location.replace("screenradioconfig.php?action=clone&oldsrcid="+oldsrcid+"&newsrcid="+newsrcid+'&name='+name);
        }
      }        

      function DelRadioConfig(prompt_text,srcid,configname)
      {
        if(confirm(prompt_text+configname+" ?"))
        {
          location.replace("screenradioconfig.php?action=del&srcid="+srcid);
        }
      }


/*
      function DelRadiodata(prompt_text)
      {
          if (confirm(prompt_text+" ?"))
            location.replace("screenradioupdate.php?action=clear");
      }
      
      function ViewRadioData()
      {
        window.open("screenradiodisplay.php");
      }
      
      function ViewRadioLog()
      {
        window.open("pictures/radiolog.txt");
      }
*/
  
    </script>
  </head>
  <body>
    
<?php

    $PHP_SELF = $_SERVER['PHP_SELF'];
    $link = ConnectToDB();
    
    $action = isset($_GET['action']) ? strval($_GET['action']) : "";

  //----------- Radio configuration functions ---------------------------------
  
  function AddNewRadioConfiguration($srcid,$name)
  {
	global $link;

    $sql = "INSERT INTO resultradioconfig SET srcid=$srcid, srcname='$name'"; 
    $ret=mysqli_query($link, $sql);
  }
  
  function DelRadioConfiguration($srcid)
  {
	global $link;

    $sql = "DELETE FROM resultradioconfig WHERE srcid='$srcid'";  
    mysqli_query($link, $sql);
    $sql = "DELETE FROM resultradioposition WHERE srcid='$srcid'";  
    mysqli_query($link, $sql);
  }

  function CloneRadioPosition($oldsrcid,$newsrcid)
  {
	global $link;

    $sql = 'SELECT * FROM resultradioposition WHERE srcid='.$oldsrcid;
    $res = mysqli_query($link, $sql);
    if (mysqli_num_rows($res) > 0)
    {
      while ($r = mysqli_fetch_array($res))
      {
        $str = "srcid=$newsrcid, ";

        $radioid=$r['radioid'];
        $str = $str."radioid=".$radioid.", ";

        $radiox=$r['radiox'];
        $str = $str."radiox=".$radiox.", ";

        $radioy=$r['radioy'];
        $str = $str."radioy=".$radioy." ";
  
        $sql = "INSERT INTO resultradioposition SET $str";
        $ret=mysqli_query($link, $sql);
      }
    }
  }
  
  //---------- Actions ------------------------------------

  if ($action==="add")
  {
    if (isset($_GET['srcid']))
    {
      $srcid = intval($_GET['srcid']);
      $name = isset($_GET['name']) ? $_GET['name'] : "New configuration";
      AddNewRadioConfiguration($srcid,$name);
    }
  }

  if ($action==="play")
  {
    if (isset($_GET['srcid']))
    {
      $srcid = intval($_GET['srcid']);

      $res = mysqli_query($link, "SELECT srcid FROM resultradioconfig WHERE srcid=$srcid");
      if (mysqli_num_rows($res) > 0)
      {
        $sql = "UPDATE resultradioconfig SET active=0";
        $res = mysqli_query($link, $sql);
        $sql = "UPDATE resultradioconfig SET active=1 WHERE srcid=$srcid";
        $res = mysqli_query($link, $sql);
        print "<script>window.open('screenradiodisplay.php')</script>";  // open view in a new window
      }
    }
  }

  if ($action==="rename")
  {
    if (isset($_GET['srcid']))
    {
      $srcid = intval($_GET['srcid']);
      $newname = urldecode(isset($_GET['name']) ? $_GET['name'] : "no name ".$srcid);

      $res = mysqli_query($link, "SELECT srcid FROM resultradioconfig WHERE srcid=$srcid");

      if (mysqli_num_rows($res) > 0)
      {
        $sql = "UPDATE resultradioconfig SET srcname='".$newname."' WHERE srcid=".$srcid;
        $res = mysqli_query($link, $sql);
      }
    }
    mysqli_query($link, $sql);
  }

  if ($action==="clone")
  {
    if (isset($_GET['oldsrcid']) && isset($_GET['newsrcid']))
    {
      $oldsrcid = intval($_GET['oldsrcid']);
      $newsrcid = intval($_GET['newsrcid']);
      $name = isset($_GET['name']) ? $_GET['name'] : "New configuration";
      AddNewRadioConfiguration($newsrcid,$name);
      CloneRadioPosition($oldsrcid,$newsrcid);
    }
  }

  if ($action==="del")
  {
    $srcid = isset($_GET['srcid']) ? intval($_GET['srcid']) : 0;
    DelRadioConfiguration($srcid);
  }



  //------------------------------------------------------------------------------
  
  //-- Determine next available rcid for add or clone operations
    
  $sql = "SELECT srcid FROM resultradioconfig";
  $res = mysqli_query($link, $sql);
  $nextsrcid=1;
  if (mysqli_num_rows($res) > 0)
  {
    while ($r = mysqli_fetch_array($res))
    {
      $srcid=$r['srcid'];
      if ($srcid>=$nextsrcid)
      {
        $nextsrcid = $srcid+1;
      }
    }
  }

  print "<table border>\n";
  print "<tr>";
  print "<th colspan=4>".MyGetText(86)."</th>";
  print "<th colspan=3>&nbsp;</th>";
  print "</tr>";

  $sql = "SELECT * FROM resultradioconfig";
  $res = mysqli_query($link, $sql);
  if (mysqli_num_rows($res) > 0)
  {
    while ($r = mysqli_fetch_array($res))
    {
      $srcid=$r['srcid'];
      $srcname=$r['srcname'];
      $active = $r['active'];
      print "<tr>\n";
      print "<td>".$srcname."</td>\n";
      print "<td><img src='img/edit.png' title='".MyGetText(1)."' onclick='EditRadioConfig(".$srcid.");'></img></td>\n";
      print "<td><img src='img/play.png' title='".MyGetText(2)."' onclick='PlayRadioConfig(".$srcid.");'></img></td>\n";
      if ($active==1)
      {
        print "<td><img src='img/run.png' title='".MyGetText(3)."'></img></td>\n";
      }
      else
      {
        print "<td>&nbsp;</td>\n";
      }
      print "<td><img src='img/rename.png' title='".MyGetText(4)."' onclick='RenameRadioConfig(\"".MyGetText(54)."\",".$srcid.",\"".$srcname."\");'></img></td>\n";
      print "<td><img src='img/clone.png' title='".MyGetText(5)."' onclick='CloneRadioConfig(\"".MyGetText(13)."\",".$srcid.",".$nextsrcid.");'></img></td>\n";
      print "<td><img src='img/suppr.png' title='".MyGetText(6)."' onclick='DelRadioConfig(\"".MyGetText(14)."\",".$srcid.",\"".$srcname."\");'></img></td>\n";
      print "</tr>\n";
    } 
  }
  print "</table>\n";
  print "<input type='button' value='".MyGetText(7)."' onclick='AddRadioConfig(\"".MyGetText(13)."\",".$nextsrcid.");'>"; // New button

  print "<br/>\n";
  print "<a href='screenconfig.php'>".MyGetText(19)."</a>&nbsp;&nbsp;&nbsp;"; // Link to main page
  print "<br/>\n";


?>        
  </body>
</html>
