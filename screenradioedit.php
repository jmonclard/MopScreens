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
      function MapChange(srcid)
      {
        var maplist=document.getElementById('maplistid');
        var map = maplist.options[maplist.selectedIndex].value;
        location.replace("screenradioedit.php?action=mapchange&srcid="+srcid+"&map="+map);
      }
      
      function ChangeCoord(srcid)
      {
        var x0=document.getElementById('x0textid').value;
        var y0=document.getElementById('y0textid').value;
        var x1=document.getElementById('x1textid').value;
        var y1=document.getElementById('y1textid').value;
        location.replace("screenradioedit.php?action=mapcoordchange&srcid="+srcid+"&x0="+x0+"&y0="+y0+"&x1="+x1+"&y1="+y1);
      }

      function DelRadio(prompt_text,srcid,radioid)
      {
        if(confirm(prompt_text+radioid+" ?"))
        {
          location.replace("screenradioedit.php?action=delradio&srcid="+srcid+"&radioid="+radioid);
        }
      }

      function AddRadio(srcid,radioid,x,y)
      {
        location.replace("screenradioedit.php?action=addradio&srcid="+srcid+"&radioid="+radioid+"&x="+x+"&y="+y);
      }
  
      function EditRadio(id,x,y)
      {
        var zoneid=document.getElementById('idid');
        var zonex=document.getElementById('xposid');
        var zoney=document.getElementById('yposid');
        var zoneoldid=document.getElementById('oldid');
        var lediv = document.getElementById('editzoneid');
        
        zoneid.value=id;
        zonex.value=x;
        zoney.value=y;
        zoneoldid.value=id;
        lediv.style.display="block";
      }

      function ChangePos(srcid, oldradioid)
      {
        var id=document.getElementById('idid').value;
        var x=document.getElementById('xposid').value;
        var y=document.getElementById('yposid').value;
        var oldid=document.getElementById('oldid').value;
        
        location.replace("screenradioedit.php?action=editradio&srcid="+srcid+"&oldradioid="+oldid+"&newradioid="+id+"&x="+x+"&y="+y);
      }


      function CancelPos()
      {
        var lediv = document.getElementById('editzoneid');
        lediv.style.display="none";
      }



      // Get point in global SVG space
      function cursorPoint(evt,pt,svg){
        pt.x = evt.clientX; pt.y = evt.clientY;
        return pt.matrixTransform(svg.getScreenCTM().inverse());
      }


      function evtclick(evt, echX, echY, x0, y0)
      {
        var svg = document.querySelector('svg');
        var pt = svg.createSVGPoint();
        var loc = cursorPoint(evt,pt,svg);

        var x=loc.x/echX+x0;
        var y=loc.y/echY+y0;
        var selectedElement = evt.target;
        var zonex=document.getElementById('xposid');
        var zoney=document.getElementById('yposid');

  
        zonex.value=Math.round(x*100)/100;
        zoney.value=Math.round(y*100)/100;
        selectedElement.removeAttributeNS(null, "onmousemove");
      }

    </script>
  </head>
  <body>
<?php

    $PHP_SELF = $_SERVER['PHP_SELF'];
    $link = ConnectToDB();
    
    $srcid = isset($_GET['srcid']) ? intval($_GET['srcid']) : 0;
    $action = isset($_GET['action']) ? strval($_GET['action']) : "";

  //----------- Radio configuration edition functions ---------------------------------


  function InsertFileList($list, $listname,$current_value,$listid,$onchangeaction)
  {
      print "<select name='".$listname."' id='".$listid."' onchange='".$onchangeaction."'>\n";
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
      print "</select>\n";
  }
  
  function AddNewRadio($srcid,$radioid,$x,$y)
  {
  global $link;
      $sql = "INSERT INTO resultradioposition SET srcid=$srcid, radioid=$radioid, radiox=$x, radioy=$y"; 
      $ret=mysqli_query($link, $sql);
  }

  function DelRadio($srcid,$radioid)
  {
  global $link;
    $sql = "DELETE FROM resultradioposition WHERE srcid='$srcid' AND radioid='$radioid'";  
    mysqli_query($link, $sql);
  }

  //---------- Actions ------------------------------------
  if ($action==="mapchange")
  {
    $map = isset($_GET['map']) ? strval($_GET['map']) : "";
    $sql = "UPDATE resultradioconfig SET srcmap='".$map."' WHERE srcid=".$srcid;
    $res = mysqli_query($link, $sql);
  }

  if ($action==="mapcoordchange")
  {
    $x0 = isset($_GET['x0']) ? floatval($_GET['x0']) : 0.0;
    $y0 = isset($_GET['y0']) ? floatval($_GET['y0']) : 0.0;
    $x1 = isset($_GET['x1']) ? floatval($_GET['x1']) : 100.0;
    $y1 = isset($_GET['y1']) ? floatval($_GET['y1']) : 100.0;
    if ($x0>$x1) $x1=$x0+1.0;
    if ($y0>$y1) $y1=$y0+1.0;
    $sql = "UPDATE resultradioconfig SET srcx0='".$x0."', srcy0='".$y0."', srcx1='".$x1."', srcy1='".$y1."'  WHERE srcid=".$srcid;
    $res = mysqli_query($link, $sql);
  }

  if ($action==="delradio")
  {
    if (isset($_GET['radioid']))
    {
      $radioid = intval($_GET['radioid']);
      DelRadio($srcid,$radioid);
    }
  }

  if ($action==="addradio")
  {
    if (isset($_GET['radioid']))
    {
      $radioid = intval($_GET['radioid']);
      $x = isset($_GET['x']) ? floatval($_GET['x']) : 10.0;
      $y = isset($_GET['y']) ? floatval($_GET['y']) : 10.0;
      AddNewRadio($srcid,$radioid,$x,$y);
    }
  }

  if ($action==="editradio")
  {
    if ((isset($_GET['oldradioid']))&&(isset($_GET['newradioid'])))
    {
      $oldradioid = intval($_GET['oldradioid']);
      $newradioid = intval($_GET['newradioid']);
      $x = isset($_GET['x']) ? floatval($_GET['x']) : 10.0;
      $y = isset($_GET['y']) ? floatval($_GET['y']) : 10.0;
      DelRadio($srcid,$oldradioid);
      AddNewRadio($srcid,$newradioid,$x,$y);
    }
  }


  $res = mysqli_query($link, "SELECT * FROM resultradioconfig WHERE srcid=$srcid");
  if (mysqli_num_rows($res) > 0)
  {
  
    $r = mysqli_fetch_array($res);

    $srcname=$r['srcname'];
    $srcmap=$r['srcmap'];
    $srcx0=floatval($r['srcx0']);
    $srcy0=floatval($r['srcy0']);
    $srcx1=floatval($r['srcx1']);
    $srcy1=floatval($r['srcy1']);
    if ($srcx1<=$srcx0) $srcx1=$srcx0+1.0;
    if ($srcy1<=$srcy0) $srcy1=$srcy0+1.0;

    //---- radio list -----
    
    $arr_radio = array();

    $res = mysqli_query($link, "SELECT * FROM resultradioposition WHERE srcid=$srcid");
    if (mysqli_num_rows($res) > 0)
    {
      while ($r = mysqli_fetch_array($res))
      {
        $arr_radio[] = [$r['radioid'],$r['radiox'],$r['radioy']];
      }    
    }
    

    //---------- picture list creation ----
    
    $picturefilelist= array();
    $picturefilelist[" "]=" ";
    $tmp_picturefilelist=array_diff(scandir("./pictures"), array('..', '.','index.php','index.html','serverip.txt','radiolog.txt'));
    foreach ($tmp_picturefilelist as $name)
    {
      $picturefilelist[$name]=$name;
    }

    print "<h4>".$srcname."</h4>\n";

    //-- map selection ---------------
    print MyGetText(88)."\n"; // map
    InsertFileList($picturefilelist,'map',$srcmap,'maplistid','MapChange('.$srcid.')');
    print "<br/>\n";

    //-- map and radio controls ----
    $srcmap = './pictures/'.htmlspecialchars($srcmap);
    if(file_exists(dirname(__FILE__).'/'.$srcmap))
    {
      $size = getimagesize($srcmap);
      if($size)
      {
        $sizeX = $size[0];
        $sizeY = $size[1];
      }
    }
    if ($sizeY>0)
      $echelle =500.0/$sizeY;
    else
      $echelle=1.0;
    $dx=$echelle*$sizeX;
    $dy=$echelle*$sizeY;
    $echX=$echelle*$sizeX/($srcx1-$srcx0);
    $echY=$echelle*$sizeY/($srcy1-$srcy0);
    $mousevent="evtclick(evt,".$echX.",".$echY.",".$srcx0.",".$srcy0.");";
    print '<svg id="map" width="'.$dx.'" height="'.$dy.'" xmlns="http://www.w3.org/2000/svg">';
    print '<image id="background" onmouseup="'.$mousevent.'" xlink:href="'.$srcmap.'" x="0" y="0" width="'.$dx.'" height="'.$dy.'"/>';
    print '<rect x="0" y="0" width="'.$dx.'" height="'.$dy.'" style="stroke-width:1;stroke:blue;fill:none;" />';

    foreach ($arr_radio as $r)
    {
        $id = intval($r[0]);
        $x=floatval($r[1]);
        $y=floatval($r[2]);
    
    
        $nouvX = $x*$echX;
        $nouvY = $y*$echY;
        $txtX = $nouvX + 10; 
        $txtY = $nouvY - 10; 
        
        
        if ($id==0)
        {
          print '<circle cx="'.$nouvX.'" cy="'.$nouvY.'" r="10" style="stroke-width:2;stroke:magenta;fill:none;" />';
          print '<circle cx="'.$nouvX.'" cy="'.$nouvY.'" r="6" style="stroke-width:2;stroke:magenta;fill:none;" />';
        }
        else if ($id>=200)
        {
          $xr0=$nouvX-10;
          $yr0=$nouvY-10;
          print '<rect x="'.$xr0.'" y="'.$yr0.'" width="20" height="20" style="stroke-width:2;stroke:magenta;fill:none;" />';
        }
        else
        {
          print '<circle cx="'.$nouvX.'" cy="'.$nouvY.'" r="10" style="stroke-width:2;stroke:magenta;fill:none;" />';
        }
        print '<text x="'.$txtX.'" y="'.$txtY.'" fill="magenta">'.$id.'</text>';

        
    }

    print '</svg>';


    print "<br/>".MyGetText(88)." X0=<input type='text' name=x0 size=2 id=x0textid value='".$srcx0."'>&nbsp;\n";
    print "Y0=<input type='text' name=x0 size=2 id=y0textid value='".$srcy0."'>&nbsp;\n";
    print "X1=<input type='text' name=x0 size=2 id=x1textid value='".$srcx1."'>&nbsp;\n";
    print "Y1=<input type='text' name=x0 size=2 id=y1textid value='".$srcy1."'>&nbsp;\n";
    print "<input type='button' value='".MyGetText(89)."' onclick='ChangeCoord(".$srcid.");'>"; // Change
    print "<table border>\n";
    print "<tr>\n";
    print "<th>".MyGetText(8)."</th>";
    print "<th>X</th>";
    print "<th>Y</th>";
    print "<th colspan=2>&nbsp;</th>";
    print "</tr>\n";

    $maxid=-1;
    foreach ($arr_radio as $r)
    {
      $id = intval($r[0]);
      if ($id>$maxid) $maxid=$id;
      $x=floatval($r[1]);
      $y=floatval($r[2]);
      print "<tr>\n";
      print "<td>".$id."</td>\n";
      print "<td>".$x."</td>\n";
      print "<td>".$y."</td>\n";
      print "<td><img src='img/suppr.png' title='".MyGetText(6)."' onclick='DelRadio(\"".MyGetText(90)."\",".$srcid.",\"".$id."\");'></img></td>\n";
      print "<td><img src='img/edit.png' title='".MyGetText(1)."' onclick='EditRadio(".$id.",".$x.",".$y.");'></img></td>\n";
      print "</tr>\n";
    }
    $availid = $maxid + 1;

    print "</table>\n";
    $newx=($srcx1+$srcx0)/2.0;
    $newy=($srcy1+$srcy0)/2.0;
    print "<input type='button' value='".MyGetText(7)."' onclick='AddRadio(".$srcid.",".$availid.",".$newx.",".$newy.");'>"; // New

    //-- radio edit zone

    print "<div id='editzoneid' style='display:none'>\n";
    print "<hr>\n";
    print MyGetText(8)."&nbsp;<input type='text' name=x0 size=2 id=idid value='0'>&nbsp\n";
    print "X&nbsp;<input type='text' name=x0 size=2 id=xposid value='10'>&nbsp;\n";
    print "Y&nbsp;<input type='text' name=x0 size=2 id=yposid value='10'>&nbsp\n";
    print "<input type='hidden' id='oldid' value='0'>\n";
    print "<input type='button' value='".MyGetText(89)."' onclick='ChangePos(".$srcid.");'>"; // Change
    print "&nbsp;&nbsp;&nbsp;<input type='button' value='".MyGetText(53)."' onclick='CancelPos();'>"; // Cancel
    print "<br/>(".MyGetText(91).")\n";
    print "</div>\n";

    print "<hr>\n";
    print "<div align=center>\n";
    print "<input type='button' value='".MyGetText(87)."' onclick='location.replace(\"screenradioconfig.php\");'>"; // Close
    print "</div>\n";
    
  }
?>        
  </body>
</html>

