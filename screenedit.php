<?php
  /*
  Copyright 2014-2016 Metraware
  
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
  date_default_timezone_set('Europe/Paris');
  include_once('functions.php');
  redirectSwitchUsers();
  
  include_once('lang.php');
  $_SESSION['CurrentLanguage'] = isset($_SESSION['CurrentLanguage']) ? $_SESSION['CurrentLanguage'] : autoSelectLanguage(array('fr','en','sv'),'en');
  
  include_once('screenfunctions.php');
  include_once('config.php');
  

  function InsertFileList($list, $listname,$current_value,$listid)
  {
      print "<td><select name='".$listname."' id='".$listid."'>\n";
      foreach ($list as $id => $name)
      {
        if ($name==$current_value)
        {
          print "<option value='".$id."' selected='selected'>".$name."</option>\n";
        }
        else
        {
          print "<option value='".$id."'>".$name."</option>\n";
        }
      }
      print "</select></td>\n";
  }
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style>
          li
          {
            display: inline;
            line-height:2.5em;
          }
          li a:active, li a:link, li a:visited
          {
            border: 1px solid #606060;
            color: #808080;
            padding: 5px 10px;
            text-decoration: none;
            white-space: nowrap;
          }

          li a.active:active, li a.active:link, li a.cr:visited
          {
            color: #1f1f1f;
            font-weight: bold;
            background-color: #30c030;
          }/*
          li a.active:hover, li a:hover
          {
            background-color: #30c030;
          }*/
        </style>
        <title>Screen edit</title>
        <script type="text/javascript">
            function GoBack(rcid)
            {
                location.replace("screen.php?rcid="+rcid);
            }
            
            function Reload(rcid,sid)
            {
                var compet = document.getElementById("cid");
                var cid = compet.options[compet.selectedIndex].value;
                location.replace("screenedit.php?action=reload&rcid="+rcid+"&sid="+sid+"&cid="+cid);
            }
            
            function ManagePanel(sel,i)
            {
              sel=parseInt(sel);

              //document.getElementById('panel'+i+'mode').disabled=true;
              //document.getElementById('panel'+i+'tm_count').disabled=true;
              //document.getElementById('panel'+i+'alternate').disabled=true;
              document.getElementById('panel'+i+'content8').disabled=true;

              document.getElementById('panel'+i+'content1txt').disabled=true;

              document.getElementById('panel'+i+'content2').disabled=true;
              document.getElementById('panel'+i+'txtsize').disabled=true;
              document.getElementById('panel'+i+'txtcolor').disabled=true;

              document.getElementById('panel'+i+'content3txt').disabled=true;

              document.getElementById('panel'+i+'content4').disabled=true;
              document.getElementById('panel'+i+'content5').disabled=true;
              document.getElementById('panel'+i+'content6').disabled=true;
              document.getElementById('panel'+i+'content9').disabled=true;
              document.getElementById('panel'+i+'firstline').disabled=true;
              document.getElementById('panel'+i+'fixedlines').disabled=true;
              document.getElementById('panel'+i+'scrolledlines').disabled=true;
              document.getElementById('panel'+i+'scrolltime').disabled=true;
              document.getElementById('panel'+i+'scrollbeforetime').disabled=true;
              document.getElementById('panel'+i+'scrollaftertime').disabled=true;
              document.getElementById('panel'+i+'updateduration').disabled=true;

              switch(sel) {
                case 1 :
                  document.getElementById('panel'+i+'content1txt').disabled=false;
                  break;
                case 2 :
                  document.getElementById('panel'+i+'content2').disabled=false;
                  document.getElementById('panel'+i+'txtsize').disabled=false;
                  document.getElementById('panel'+i+'txtcolor').disabled=false;
                  break;
                case 3 :
                  document.getElementById('panel'+i+'content3txt').disabled=false;
                  break;
                case 4 :
                  document.getElementById('panel'+i+'content4').disabled=false;
                  document.getElementById('panel'+i+'firstline').disabled=false;
                  document.getElementById('panel'+i+'fixedlines').disabled=false;
                  document.getElementById('panel'+i+'scrolledlines').disabled=false;
                  document.getElementById('panel'+i+'scrolltime').disabled=false;
                  document.getElementById('panel'+i+'scrollbeforetime').disabled=false;
                  document.getElementById('panel'+i+'scrollaftertime').disabled=false;
                  document.getElementById('panel'+i+'updateduration').disabled=false;
                  break;
                case 5 : // results
                  document.getElementById('panel'+i+'content5').disabled=false;
                  document.getElementById('panel'+i+'firstline').disabled=false;
                  document.getElementById('panel'+i+'fixedlines').disabled=false;
                  document.getElementById('panel'+i+'scrolledlines').disabled=false;
                  document.getElementById('panel'+i+'scrolltime').disabled=false;
                  document.getElementById('panel'+i+'scrollbeforetime').disabled=false;
                  document.getElementById('panel'+i+'scrollaftertime').disabled=false;
                  document.getElementById('panel'+i+'updateduration').disabled=false;
                  break;
                case 6 : // summary
                  document.getElementById('panel'+i+'content6').disabled=false;
                  document.getElementById('panel'+i+'firstline').disabled=false;
                  document.getElementById('panel'+i+'fixedlines').disabled=false;
                  document.getElementById('panel'+i+'updateduration').disabled=false;
                  break;
                case 7 : // Blog
                  document.getElementById('panel'+i+'fixedlines').disabled=false;
                  document.getElementById('panel'+i+'updateduration').disabled=false;
                  break;
                case 8 : // Slides
                  document.getElementById('panel'+i+'content8').disabled=false;
                  document.getElementById('panel'+i+'scrolltime').disabled=false;
                  break;
                case 9 : // Radio
                  document.getElementById('panel'+i+'content9').disabled=false;
                  document.getElementById('panel'+i+'fixedlines').disabled=false;
                  break;
                }
            }
            
           
            function EditClassesList(rcid,cid,sid,panel)
            {
                location.replace("screenclasses.php?rcid="+rcid+"&cid="+cid+"&sid="+sid+"&panel="+panel);
            }
            
        </script>
        <script type="text/javascript" src="jscolor/jscolor.js"></script>
    </head>
    <body>
<?php

  $PHP_SELF = $_SERVER['PHP_SELF'];
  $link = ConnectToDB();

  class Panel {
    var $numpanel;
    var $classes;
    
    var $content;
    var $mode;
    var $tm_count;
    var $alternate;
    var $pict;
    var $slides;
    var $txt;
    var $txtsize;
    var $txtcolor;
    var $html;
    var $firstline;
    var $fixedlines;
    var $scrolledlines;
    var $scrolltime;
    var $scrollbeforetime;
    var $scrollaftertime;
    var $updateduration;
    var $radioctrl;
    
    function Panel($num)
    {
      $this->numpanel = $num;
    }

    function Initialise($r, $cls, $rad = 0)
    {
      $this->content=$r['panel'.$this->numpanel.'content'];
      $this->mode = $r['panel'.$this->numpanel.'mode'];
      $this->tm_count = $r['panel'.$this->numpanel.'tm_count'];
      $this->alternate = $r['panel'.$this->numpanel.'alternate'];
      $this->pict=$r['panel'.$this->numpanel.'pict'];
      $this->slides=$r['panel'.$this->numpanel.'slides'];
      $this->txt=stripslashes($r['panel'.$this->numpanel.'txt']);
      $this->txtsize=$r['panel'.$this->numpanel.'txtsize'];
      $this->txtcolor=$r['panel'.$this->numpanel.'txtcolor'];
      $this->html=$r['panel'.$this->numpanel.'html'];
      $this->firstline=$r['panel'.$this->numpanel.'firstline'];
      $this->fixedlines=$r['panel'.$this->numpanel.'fixedlines'];
      $this->scrolledlines=$r['panel'.$this->numpanel.'scrolledlines'];
      $this->scrolltime=$r['panel'.$this->numpanel.'scrolltime'];
      $this->scrollbeforetime=$r['panel'.$this->numpanel.'scrollbeforetime'];
      $this->scrollaftertime=$r['panel'.$this->numpanel.'scrollaftertime'];
      $this->updateduration=$r['panel'.$this->numpanel.'updateduration'];
      $this->radioctrl=$r['panel'.$this->numpanel.'radioctrl'];
      
      $this->classes = $cls;
    }
    
    function Display()
    {
      global $picturefilelist;
      global $slidesfilelist;
      global $htmlfilelist;
      
      print '<div id="pan'.$this->numpanel.'div">';
      $prefix = 'panel'.$this->numpanel;
      //print "<h4>$prefix</h4>"; //TODO to be replaced with tab higlighting

      print "<table>\n";


      // mode
      print "<tr>\n";
      print "<td><input type='checkbox' name='chkall[]' value='".$prefix."mode'></input></td>\n";
      print "<td>".MyGetText(30)."</td>\n";
      print "<td><select name='".$prefix."mode' size=1>\n";
      switch($this->mode) {
          case 1:
              print "<option value='1' selected>".MyGetText(101)."</option>\n"; // indiv
              print "<option value='2'>".MyGetText(102)."</option>\n"; // relay
              print "<option value='3'>".MyGetText(103)."</option>\n"; // multistages
			  print "<option value='4'>".MyGetText(105)."</option>\n"; // showO
              break;
          case 2:
              print "<option value='1'>".MyGetText(101)."</option>\n"; // indiv
              print "<option value='2' selected>".MyGetText(102)."</option>\n"; // relay
              print "<option value='3'>".MyGetText(103)."</option>\n"; // multistages
			  print "<option value='4'>".MyGetText(105)."</option>\n"; // showO
              break;
          case 3:
              print "<option value='1'>".MyGetText(101)."</option>\n"; // indiv
              print "<option value='2'>".MyGetText(102)."</option>\n"; // relay
              print "<option value='3' selected>".MyGetText(103)."</option>\n"; // multistages
			  print "<option value='4'>".MyGetText(105)."</option>\n"; // showO
              break;
          case 4:
              print "<option value='1'>".MyGetText(101)."</option>\n"; // indiv
              print "<option value='2'>".MyGetText(102)."</option>\n"; // relay
              print "<option value='3'>".MyGetText(103)."</option>\n"; // multistages
			  print "<option value='4' selected>".MyGetText(105)."</option>\n"; // showO
              break;
      }
      print "</select></td>\n";
      print "</tr>\n";

      // relay team member count
      print "<tr>\n";
      print '<td><input type="checkbox" name="chkall[]" value="'.$prefix.'tm_count"></input></td>';
      print "<td>".MyGetText(81)."</td>\n";
      $str=NumericIntList($prefix."tm_count",1,10,$this->tm_count);
      print "<td>$str</td>\n";
      print "</tr>\n";

      // alternate
      print "<tr>\n";
      print "<td><input type='checkbox' name='chkall[]' value='".$prefix."alternate'></input></td>\n";
      print "<td>".MyGetText(97)."</td>\n";
      print "<td><select name='".$prefix."alternate' size=1>\n";
      switch($this->alternate) {
          case 0:
              print "<option value='0' selected>".MyGetText(104)."</option>\n"; // Classical
			  print "<option value='1'>".MyGetText(106)."</option>\n"; // Alternate
              break;
			case 1:
				print "<option value='0'>".MyGetText(104)."</option>\n"; // Classical
			  print "<option value='1' selected>".MyGetText(106)."</option>\n"; // Alternate
			break;
          default:
              print "<option value='0' selected>".MyGetText(104)."</option>\n"; // Classical
			  print "<option value='1'>".MyGetText(106)."</option>\n"; // Alternate
              break;
      }
      print "</select></td>\n";
      print "</tr>\n";

      // Empty line
      print "<tr>\n";
      print "<td>&nbsp;</td>\n";
      print "<td>&nbsp;</td>\n";
      print "</tr>\n";

      // content
      print "<tr>\n";
      print "<td>&nbsp;</td>\n";
      print '<td><input type="checkbox" name="chkall[]" value="'.$prefix.'content"></input></td>';
      print "</tr>\n";

      // Picture
      print "<tr>\n";
      print '<td><input type="checkbox" name="chkall[]" value="'.$prefix.'pict"></input></td>';
      print '<td><input type="radio" name="'.$prefix.'content" value="1" id="'.$prefix.'id1">'.MyGetText(38).'</input></td>'; 
      InsertFileList($picturefilelist,$prefix.'pict',$this->pict,$prefix.'content1txt');
      print "</tr>\n";
      
      // Slides
      print "<tr>\n";
      print '<td><input type="checkbox" name="chkall[]" value="'.$prefix.'slides"></input></td>';
      print '<td><input type="radio" name="'.$prefix.'content" value="8" id="'.$prefix.'id8">'.MyGetText(99).'</input></td>'; 
      InsertFileList($slidesfilelist,$prefix.'slides',$this->slides,$prefix.'content8');
      print "</tr>\n";
      
      // Text
      print "<tr>\n";
      print '<td><input type="checkbox" name="chkall[]" value="'.$prefix.'txt"></input></td>';
      print '<td><input type="radio" name="'.$prefix.'content" value="2" id="'.$prefix.'id2">'.MyGetText(39).'</input></td>'; 
      print '<td><textarea name="'.$prefix.'txt" cols=64 rows=4 maxlength=500 id="'.$prefix.'content2">'.$this->txt.'</textarea></td>';
      $str=NumericIntList($prefix.'txtsize',1,72,$this->txtsize);
      print "<td>$str</td>\n";
      print '<td>'.MyGetText(66).' : <input type="text" name="'.$prefix.'txtcolor" id="'.$prefix.'txtcolor" class="color" size=6 value="'.$this->txtcolor.'"></td>';
      print "</tr>\n";

       // Blog
      print "<tr>\n";
      print "<td>&nbsp;</td>\n";
      print '<td><input type="radio" name="'.$prefix.'content" value="7" id="'.$prefix.'id7">'.MyGetText(93).'</input></td>'; 
      print "</tr>\n";

       // HTML
      print "<tr>\n";
      print '<td><input type="checkbox" name="chkall[]" value="'.$prefix.'html"></input></td>';
      print '<td><input type="radio" name="'.$prefix.'content" value="3" id="'.$prefix.'id3">'.MyGetText(40).'</input></td>'; 
      InsertFileList($htmlfilelist,$prefix.'html',$this->html,$prefix.'content3txt');
      print "</tr>\n";

      // Start list
      print "<tr>\n";
      print "<td>&nbsp;</td>\n";
      print '<td><input type="radio" name="'.$prefix.'content" value="4" id="'.$prefix.'id4">'.MyGetText(42).'</input></td>'; 
      print '<td><input type="text" name="'.$prefix.'startlist" size=64 id="'.$prefix.'content4" readonly value="'.$this->classes.'"></td>';
      print "</tr>\n";
      
      // Results
      print "<tr>\n";
      print "<td>&nbsp;</td>\n";
      print '<td><input type="radio" name="'.$prefix.'content" value="5" id="'.$prefix.'id5">'.MyGetText(43).'</input></td>'; 
      print '<td><input type="text" name="'.$prefix.'results" size=64 id="'.$prefix.'content5" readonly value="'.$this->classes.'"></td>';
      print "</tr>\n";

      // Summary
      print "<tr>\n";
      print "<td>&nbsp;</td>\n";
      print '<td><input type="radio" name="'.$prefix.'content" value="6" id="'.$prefix.'id6">'.MyGetText(92).'</input></td>'; 
      print '<td><input type="text" name="'.$prefix.'summary" size=64 id="'.$prefix.'content6" readonly value="'.$this->classes.'"></td>';
      print "</tr>\n";

      // Radio
      print "<tr>\n";
      print "<td>&nbsp;</td>\n";
      print '<td><input type="radio" name="'.$prefix.'content" value="9" id="'.$prefix.'id9">'.MyGetText(107).'</input></td>'; 
      print '<td><input type="text" name="'.$prefix.'radio" size=64 id="'.$prefix.'content9" readonly value="'.$this->classes.'"></td>';
      $str=NumericIntList($prefix.'radioctrl',31,255,$this->radioctrl);
      print "<td>$str</td>\n";
      print "</tr>\n";

      // First line
      print "<tr>\n";
      print '<td><input type="checkbox" name="chkall[]" value="'.$prefix.'firstline"></input></td>';
      $str=NumericIntList($prefix."firstline",1,999,$this->firstline);
      print "<td>".MyGetText(58)."</td>\n";
      print "<td>$str</td>\n";
      print "</tr>\n";

      // Fixed line
      print "<tr>\n";
      print '<td><input type="checkbox" name="chkall[]" value="'.$prefix.'fixedlines"></input></td>';
      $str=NumericIntList($prefix."fixedlines",0,30,$this->fixedlines);
      print "<td>".MyGetText(59)."</td>\n"; 
      print "<td>$str</td>\n";
      print "</tr>\n";

      // Scrolling lines
      print "<tr>\n";
      print '<td><input type="checkbox" name="chkall[]" value="'.$prefix.'scrolledlines"></input></td>';
      $str=NumericIntList($prefix."scrolledlines",0,30,$this->scrolledlines);
      print "<td>".MyGetText(60)."</td>\n"; 
      print "<td>$str</td>\n";
      print "</tr>\n";

      // Before scroll wait time
      print "<tr>\n";
      print '<td><input type="checkbox" name="chkall[]" value="'.$prefix.'scrollbeforetime"></input></td>';
      $str=NumericIntList($prefix."scrollbeforetime",1,200,$this->scrollbeforetime);
      print "<td>".MyGetText(62)."</td>\n";
      print "<td>$str 1/10°s</td> \n";
      print "</tr>\n";

      // Scroll speed
      print "<tr>\n";
      print '<td><input type="checkbox" name="chkall[]" value="'.$prefix.'scrolltime"></input></td>';
      $str=NumericIntList($prefix."scrolltime",1,200,$this->scrolltime);
      print "<td>".MyGetText(61)."</td>\n"; 
      print "<td>$str 1/10°s</td> \n";
      print "</tr>\n";

      print "<tr>\n";
      print '<td><input type="checkbox" name="chkall[]" value="'.$prefix.'scrollaftertime"></input></td>';
      $str=NumericIntList($prefix."scrollaftertime",1,200,$this->scrollaftertime);
      print "<td>".MyGetText(63)."</td>\n"; // After scroll wait time
      print "<td>$str 1/10°s</td> \n";
      print "</tr>\n";

      print "<tr>\n";
      print '<td><input type="checkbox" name="chkall[]" value="'.$prefix.'updateduration"></input></td>';
      $str=NumericIntList($prefix."updateduration",1,200,$this->updateduration);
      print "<td>".MyGetText(64)."</td>\n"; // recent higlight
      print "<td>$str min</td> \n";
      print "</tr>\n";

      print "</table>\n";

      print '</div>';
    }

  }

  $panel1 = new Panel(1);
  $panel2 = new Panel(2);
  $panel3 = new Panel(3);
  $panel4 = new Panel(4);
  $panels = array($panel1,$panel2,$panel3,$panel4); 


  $rcid = isset($_GET['rcid']) ? intval($_GET['rcid']) : 0;
  $sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
  if (($rcid>0) && ($sid>0))
  {
      $action = isset($_GET['action']) ? $_GET['action'] : "none";
      
      
      if ($action == "clearclasses")
      {
          $cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
          $panel = isset($_GET['panel']) ? intval($_GET['panel']) : 0;
          if (($cid>0)&&($panel>0))
          {
              $sql = "DELETE FROM resultclass WHERE rcid='$rcid' AND cid='$cid' AND sid='$sid' AND panel='$panel'";  
              mysqli_query($link, $sql);
          }
      }
      
      if ($action == "updateclasses")
      {
          $cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
          $panel = isset($_GET['panel']) ? intval($_GET['panel']) : 0;
          if (($cid>0)&&($panel>0))
          {
              $sql = "DELETE FROM resultclass WHERE rcid='$rcid' AND cid='$cid' AND sid='$sid' AND panel='$panel'";  
              mysqli_query($link, $sql);
              $selclasses = isset($_GET['selclasses']) ? $_GET['selclasses'] : null;
              if ($selclasses !== null)
              { 
                  foreach ($selclasses as $i => $id)
                  {
                      $str = "'".$rcid."', ";
                      $str = $str."'".$cid."', ";
                      $str = $str."'".$id."', ";
                      $str = $str."'".$sid."', ";
                      $str = $str."'".$panel."'";
                      $sql = "INSERT INTO resultclass (rcid, cid, id, sid, panel) VALUES ($str)";
                      $res = mysqli_query($link, $sql);
                  }
              }
          }

      }
      
      $configname = GetConfigurationName($rcid,$link);
      print "<h2>$configname, ".MyGetText(24)." $sid</h2>\n"; // Screen
      
      $sql = "SELECT * FROM resultscreen WHERE rcid=$rcid AND sid=$sid";
      $res = mysqli_query($link, $sql);
      
      if (mysqli_num_rows($res) > 0)
      {
      
          $r = mysqli_fetch_array($res);
          $cid=$r['cid'];
          if (($action == "reload")||($action == "updateclasses"))
          {
            $cid = isset($_GET['cid']) ? intval($_GET['cid']) : $cid;
          }
          $style=$r['style'];
          $title=stripslashes($r['title']);
          $titlesize=$r['titlesize'];
          $titlecolor=$r['titlecolor'];
          $subtitle=stripslashes($r['subtitle']);
          $subtitlesize=$r['subtitlesize'];
          $subtitlecolor=$r['subtitlecolor'];
          $titleleftpict=$r['titleleftpict'];
          $titlerightpict=$r['titlerightpict'];
          $panelscount=$r['panelscount'];
          
          for ($i=1; $i<=NB_PANEL; $i++)
          {
            $panels[$i-1]->Initialise($r, GetClasses($rcid, $cid, $sid,$i,$link));
          }

          //---------- files lists creation ----
          $stylefilelist= array();
          $stylefilelist[" "]=" ";
          $tmp_stylefilelist=array_diff(scandir("./styles"), array('..', '.','index.php','index.html'));
          foreach ($tmp_stylefilelist as $name)
          {
            $stylefilelist[$name]=$name;
          }

          $picturefilelist= array();
          $picturefilelist[" "]=" ";
          $tmp_picturefilelist=array_diff(scandir("./pictures"), array('..', '.','index.php','index.html','serverip.txt','radiolog.txt'));
          foreach ($tmp_picturefilelist as $name)
          {
            $picturefilelist[$name]=$name;
          }

          $slidesfilelist= array();
          $slidesfilelist[" "]=" ";
          $tmp_slidesfilelist=array_diff(scandir("./slides"), array('..', '.','index.php','index.html'));
          foreach ($tmp_slidesfilelist as $name)
          {
            $slidesfilelist[$name]=$name;
          }

          $htmlfilelist= array();
          $htmlfilelist[" "]=" ";
          $tmp_htmlfilelist=array_diff(scandir("./htmlfiles"), array('..', '.','index.php','index.html'));
          foreach ($tmp_htmlfilelist as $name)
          {
            $htmlfilelist[$name]=$name;
          }
  
          
          print "<form name='screenedit' method=GET action='screen.php'>\n";
          print "<input type='hidden' name='action' value='update'>\n";
          print "<input type='hidden' name='rcid' value='$rcid'>\n";
          print "<input type='hidden' name='sid' value='$sid'>\n";

          //----------------- screen global ---------------------- 
          print "<table>\n";
          print "<tr>\n";
          print "<td><input type='checkbox' name='chkall[]' value='cid'></input></td>\n";
          print "<td>".MyGetText(55)." :</td>\n"; // competition
          print "<td><select name='cid' id='cid' size=1 onchange='Reload(".$rcid.",".$sid.");'>";
          $sql = "SELECT name, cid FROM mopcompetition";
          print "<option value=0> </option>";
          $res = mysqli_query($link, $sql);
          while ($r = mysqli_fetch_array($res))
          {
              $competname=$r['name'];
              $competid=$r['cid'];
              if ($competid==$cid)
              {
                  print "<option value=$competid selected>$competid - $competname</option>";
              }
              else
              {
                  print "<option value=$competid>$competid - $competname</option>";
              }
          }
          print "</select></td>\n";
          print "</tr>\n";

          // panels count
          print "<tr>\n";
          print "<td><input type='checkbox' name='chkall[]' value='panelscount'></input></td>\n";
          print "<td>".MyGetText(98)." :</td>\n";
          print "<td><select name='panelscount' size=1>\n";
          switch($panelscount) {
              case 1:
                  print "<option value='1' selected>1 ".MyGetText(31)."</option>\n"; // one panel
                  print "<option value='2'>2 ".MyGetText(37)."</option>\n"; // Two panels
                  print "<option value='3'>3 ".MyGetText(37)."</option>\n"; // Three panels
                  print "<option value='4'>4 ".MyGetText(37)."</option>\n"; // Four panels
                  break;
              case 2:
                  print "<option value='1'>1 ".MyGetText(31)."</option>\n"; // one panel
                  print "<option value='2' selected>2 ".MyGetText(37)."</option>\n"; // Two panels
                  print "<option value='3'>3 ".MyGetText(37)."</option>\n"; // Three panels
                  print "<option value='4'>4 ".MyGetText(37)."</option>\n"; // Four panels
                  break;
              case 3:
                  print "<option value='1'>1 ".MyGetText(31)."</option>\n"; // one panel
                  print "<option value='2'>2 ".MyGetText(37)."</option>\n"; // Two panels
                  print "<option value='3' selected>3 ".MyGetText(37)."</option>\n"; // Three panels
                  print "<option value='4'>4 ".MyGetText(37)."</option>\n"; // Four panels
                  break;
              case 4:
                  print "<option value='1'>1 ".MyGetText(31)."</option>\n"; // one panel
                  print "<option value='2'>2 ".MyGetText(37)."</option>\n"; // Two panels
                  print "<option value='3'>3 ".MyGetText(37)."</option>\n"; // Three panels
                  print "<option value='4' selected>4 ".MyGetText(37)."</option>\n"; // Four panels
                  break;
          }
          print "</select></td>\n";
          print "</tr>\n";

          // style
          print "<tr>\n";
          print "<td><input type='checkbox' name='chkall[]' value='style'></input></td>\n";
          print "<td>".MyGetText(96)."</td>\n"; // Style
          InsertFileList($stylefilelist,'style',$style,'stylecontenttxt');
          print "</tr>\n";

          print "</table>\n";

          //----------------- top of the screen ---------------------- 
          print "<hr>\n";
          print "<h4>".MyGetText(100)."</h4>\n";

          print "<table>\n";
          print "<tr>\n";
          print "<td><input type='checkbox' name='chkall[]' value='title'></input></td>\n";
          print "<td>".MyGetText(25)." :</td>\n"; // Title
          print '<td><input type="text" name="title" size=64 maxlength=120 value="'.$title.'"></td>';
          $str=NumericIntList("titlesize",1,32,$titlesize);
          print "<td>$str</td>\n";
          print '<td>'.MyGetText(66).' : <input type="text" name="titlecolor" class="color" size=6 value="'.$titlecolor.'"></td>'; // color
          print "</tr>\n";
          
          print "<tr>\n";
          print "<td><input type='checkbox' name='chkall[]' value='subtitle'></input></td>\n";
          print "<td>".MyGetText(26)." : </td>\n"; // subtitle
          print '<td><input type="text" name="subtitle" size=64 maxlength=120 value="'.$subtitle.'"></td>';
          $str=NumericIntList("subtitlesize",1,32,$subtitlesize);
          print "<td>$str</td>\n";
          print '<td>'.MyGetText(66).' : <input type="text" name="subtitlecolor" class="color" size=6 value="'.$subtitlecolor.'"></td>'; // color
          print "</tr>\n";
          
          print "<tr>\n";
          print "<td><input type='checkbox' name='chkall[]' value='titleleftpict'></input></td>\n";
          print "<td>".MyGetText(56)."</td>\n"; // Left picture
          InsertFileList($picturefilelist,'titleleftpict',$titleleftpict,'titleleftcontenttxt');
          print "</tr>\n";
          
          print "<tr>\n";
          print "<td><input type='checkbox' name='chkall[]' value='titlerightpict'></input></td>\n";
          print "<td>".MyGetText(57)."</td>\n"; // Right picture
          InsertFileList($picturefilelist,'titlerightpict',$titlerightpict,'titlerightcontenttxt');
          print "</tr>\n";
		  
          
          print "</table>\n";

          //------- Panels configuration -------------------------------

          print "<hr />\n";
          print "<ul>\n";
          for ($i=1; $i<=NB_PANEL; $i++)
          {
            print "<li>\n";
            print '<a href="#" id="linkpan'.$i.'" onclick="return devPanel('.$i.');">'.MyGetText(37).' '.$i.'</a>&nbsp;';
            print "</li>\n";
          }
          print "</ul>\n";

          for ($i=1; $i<=NB_PANEL; $i++)
          {
            $panels[$i-1]->Display();
          }

          print "<hr>\n";
          print "<div align=center>\n";
          print MyGetText(65);
          print "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' value='".MyGetText(52)."'>&nbsp;&nbsp;&nbsp;"; //OK
          print "<input type='button' value='".MyGetText(53)."' onclick='GoBack($rcid);'>"; // cancel
          print "</div>\n";
          print "</form>\n";
      }
  }
    
?>
        <script type="text/javascript">
            window.onload = function() 
            {
<?php
                for ($i=1; $i<=NB_PANEL; $i++)
                {
                  print 'document.getElementById("panel'.$i.'id'.$panels[$i-1]->content.'").click();';
                  print "\n";
                }
?>
            }

<?php
            for ($i=1; $i<=NB_PANEL; $i++)
            {

              print 'var radiopanel'.$i.'content = document.screenedit.panel'.$i.'content;';
              print 'var radiopanel'.$i.'contentprev = null;';
              print 'for(var j = 0; j < radiopanel'.$i.'content.length; j++)';
              print '{';
              print '    radiopanel'.$i.'content[j].onclick = function() {';
              print '        if(this !== radiopanel'.$i.'contentprev) {';
              print '            radiopanel'.$i.'contentprev = this;';
              print '        }';
              print '        ManagePanel(this.value,'.$i.');';
              print '     };';
              print '}';
              print "\n";
            }
?>

          function devPanel(num)
          {
            document.getElementById("pan1div").style.display = "none";
            document.getElementById("pan2div").style.display = "none";
            document.getElementById("pan3div").style.display = "none";
            document.getElementById("pan4div").style.display = "none";
            document.getElementById("linkpan1").className = "";
            document.getElementById("linkpan2").className = "";
            document.getElementById("linkpan3").className = "";
            document.getElementById("linkpan4").className = "";
            if(num)
            {
              document.getElementById("pan"+num+"div").style.display = "block";
              document.getElementById("linkpan" + num).className = "active";
            }
            return false;
          }
          devPanel(1);
        </script>
        
    </body>
</html>

