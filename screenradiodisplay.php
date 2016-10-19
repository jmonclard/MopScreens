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
  date_default_timezone_set('UTC');
  include_once('functions.php');
  redirectSwitchUsers();
  
  
  include_once('lang.php');
  include_once('screenfunctions.php');
  include_once('config.php');

  $_SESSION['CurrentLanguage'] = isset($_SESSION['CurrentLanguage']) ? $_SESSION['CurrentLanguage'] : autoSelectLanguage(array('fr','en','sv'),'en');


  $PHP_SELF = $_SERVER['PHP_SELF'];
  ConnectToDB();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <title>Radio display</title>
  <script type='text/javascript'>
  var svgNS = "http://www.w3.org/2000/svg";
  var xlinkNS = "http://www.w3.org/1999/xlink";

  var RAYON_BATTERIE = 20;
  var RAYON_ACCROCHE = 40;
  var previousElems = [];
  var previousData = "";
  //var positionInfo = [];
<?php

  if(isset($_GET['action']))
  {
    $action = trim($_GET['action']);
    switch($action)
    {
      case 'clear':
        $sql = 'TRUNCATE resultradio';
        mysql_query($sql);
      break;
      default:
      break;
    }
  }


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
        $sizeY = $size[1];
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

  echo 'var TAILLE_X = '.$sizeX.';'."\n";
  echo 'var TAILLE_Y = '.$sizeY.';'."\n";
?>

  var SCALE_X = TAILLE_X / (positionX1 - positionX0);
  var SCALE_Y = TAILLE_Y / (positionY1 - positionY0);


//  var batteryInfo = [];
  var batteryInfo = null; // id, battery, age
//  var levelsInfo = [];
  var levelsInfo = null; //senderid, receiverid, rxlevel, age

  var totalInfo = null;//[batteryInfo, levelsInfo];

/* usage des logs, à visualiser avec F12 dans
_log("Je suis un log");
_loginfo("Je suis un log info");
_logwarn("Je suis un log warn");
_logerror("Je suis un log error");
*/

  function _log(s)
  {
    if (typeof window.console != 'undefined')
    {
        var d = new Date();
        console.log(d.toISOString() + "\t" + s);
    }
  }
  function _loginfo(s)
  {
    if (typeof window.console != 'undefined')
    {
        var d = new Date();
        console.info(d.toISOString() + "\t" + s);
    }
  }
  function _logwarn(s)
  {
    if (typeof window.console != 'undefined')
    {
        var d = new Date();
        console.warn(d.toISOString() + "\t" + s);
    }
  }
  function _logexception(s)
  {
    if (typeof window.console != 'undefined')
    {
        var d = new Date();
        console.error(d.toISOString() + "\t" + s);
    }
  }
  function _logerror(s)
  {
    if (typeof window.console != 'undefined')
    {
        var d = new Date();
        console.error(d.toISOString() + "\t" + s);
    }
  }

  function getData()
  {
    var xmlhttp;
    try
    {
        if(window.XMLHttpRequest)
        {
            xmlhttp = new XMLHttpRequest();
        }
        else
        {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }

        xmlhttp.onreadystatechange = function()
        {
           if(xmlhttp.readyState == 4)
           {
             var lines = xmlhttp.responseText;//_log("Lines=" + lines);
             totalInfo = eval(lines);//_log("Len="+totalInfo.length);
             updateDisplay(totalInfo);
             previousData = totalInfo;
           }
        }
        xmlhttp.open("GET", "aj_refreshradioinfo.php", true);
        xmlhttp.send();
    }
    catch(err)
    {
        _logexception(err.message);
        updateDisplay(previousData);
    }
  }

  function ViewRadioLog()
  {
    window.open("pictures/radiolog.txt");
  }

  function DelRadiodata(prompt_text)
  {
      if (confirm(prompt_text+" ?"))
      {
        location.replace("screenradiodisplay.php?action=clear");
      }
  }

  function getPositionById(id)
  {
    var knownIdPositionCount = positionInfo.length;
    var found = false;
    var i;
    for(i=0;i<knownIdPositionCount;i++)
    {
        if (positionInfo[i][0] == id)
        {
            var xp = positionInfo[i][1];
            var yp = positionInfo[i][2];
            var xc = (xp - positionX0) * SCALE_X;
            var yc = (yp - positionY0) * SCALE_Y;
            found = {x:xc, y:yc};
            //_loginfo("Id:" + id +" is at position (" + found.x + " , " + found.y + ")");
            break;
        }
    }
    /*
    if (!found)
    {
        // TODO: ceci est temporaire, dans la vraie vie, si le point n'existe pas => il n'existe pas !
        found = {x:Math.random() * 900, y:Math.random()*600};
        positionInfo.push([id, found.x, found.y]);
    }
    */

    return found;
  }
  function distance(x0,y0, x1, y1)
  {
    var x = x1-x0;
    var y = y1-y0;
    return Math.sqrt(x*x + y*y);
  }
  function intersect(x0,y0, x1, y1, r)
  {
    // x0,y0 centre d'un cercle
    // x1,y1 un point ailleurs
    // r le rayon
    // on retourne le point qui intersecte le cercle sur le segment [P0:P1]
    // attention, si les cercles sont proches le résultat est vrai mais étrange
    var theta = Math.atan2(y1-y0, x1-x0);
    var x = x0 + r * Math.cos(theta);
    var y = y0 + r * Math.sin(theta);
    return {x:x, y:y};
  }

  function drawRect(svg,svgNS,elementName,elementX,elementY,elementW, elementH, elementLevel,elementAge,qual)
  {
    var x0 = elementX-elementW/2;
    var y0 = elementY-elementH/2;
    var newelemB2 = document.createElementNS(svgNS,"rect");
    newelemB2.setAttributeNS(null,"id",elementName);
    newelemB2.setAttributeNS(null,"x",x0);
    newelemB2.setAttributeNS(null,"y",y0);
    newelemB2.setAttributeNS(null,"width",elementW);
    newelemB2.setAttributeNS(null,"height",elementH);
    newelemB2.setAttributeNS(null,"style",qual);
    newelemB2.setAttributeNS(null,"title","ID : " + elementName + "\nLevel : " + elementLevel + " mV\nAge : " + ToHMSstring(elementAge));
    svg.appendChild(newelemB2);
  }

  function drawFillRect(svg,svgNS,elementName,elementX,elementY,elementW, elementH, elementFillH, elementLevel,elementAge,qualfill)
  {
    var x0 = elementX-elementW/2;
    var y0 = elementY+elementH/2-elementFillH;
    var newelemB2 = document.createElementNS(svgNS,"rect");
    newelemB2.setAttributeNS(null,"id",elementName);
    newelemB2.setAttributeNS(null,"x",x0);
    newelemB2.setAttributeNS(null,"y",y0);
    newelemB2.setAttributeNS(null,"width",elementW);
    newelemB2.setAttributeNS(null,"height",elementFillH);
    newelemB2.setAttributeNS(null,"style",qualfill);
    newelemB2.setAttributeNS(null,"title","ID : " + elementName + "\nLevel : " + elementLevel + " mV\nAge : " + ToHMSstring(elementAge));
    svg.appendChild(newelemB2);
  }

  function drawBattery(svg,svgNS,elementName,elementX,elementY, ratioFill, elementLevel,elementAge,qual,qualfill)
  {
    var h = ratioFill*1.2*RAYON_BATTERIE;
    drawRect(svg,svgNS,elementName,elementX,elementY-0.6*RAYON_BATTERIE,0.2*RAYON_BATTERIE, 0.2*RAYON_BATTERIE, elementLevel,elementAge,qual);
    drawRect(svg,svgNS,elementName,elementX,elementY+0.1*RAYON_BATTERIE,0.4*RAYON_BATTERIE, 1.2*RAYON_BATTERIE, elementLevel,elementAge,qual);
    drawFillRect(svg,svgNS,elementName,elementX,elementY+0.1*RAYON_BATTERIE,0.4*RAYON_BATTERIE, 1.2*RAYON_BATTERIE, h, elementLevel,elementAge,qualfill);
  }

  function ToHMSstring(totalSec)
  {
    var hours = parseInt( totalSec / 3600 );
    var minutes = parseInt( totalSec / 60 ) % 60;
    var seconds = totalSec % 60;
    var result;

    if (hours==0)
    {
      if (minutes==0)
      {
        result = (seconds  < 10 ? "0" + seconds : seconds)+" s";
      }
      else
      {
        result = (minutes < 10 ? "0" + minutes : minutes) + " min " + (seconds  < 10 ? "0" + seconds : seconds)+" s";
      }
    }
    else
    {
      result = (hours < 10 ? "0" + hours : hours) + " h " + (minutes < 10 ? "0" + minutes : minutes) + " min " + (seconds  < 10 ? "0" + seconds : seconds)+" s";
    }
    return result;
  }

  function addPositionWithoutBattery(svg, id)
  {
    addBattery(svg, id, 0, 66666);
    /*
    var level = 0;
    var age = 66666;
    var bposition = getPositionById(id);
    if (bposition != null)
    {
        // ajout d'une batterie
        var elementID = id;
        var elementName = elementID;
        var elementR = RAYON_BATTERIE;// TODO: pour l'instant rayon des batteries => a changer en rectangle...
        var elementX = bposition.x;
        var elementY = bposition.y;
        var elementLevel = level;
        var elementAge = age;
        previousElems.push(elementName);

        // choix de la couleur de la batterie en fonction du niveau (en mV), on convertit en %
        var status_pc = 0.0;
        if (elementLevel>=3600.0)
        {
            status_pc = 100;
        }
        else
        if (elementLevel>=3500.0)
        {
            status_pc = 100.0 * (elementLevel-3500.0) / (3600.0 - 3500.0);
        }

        var qual,qualfill;
        if (status_pc >= 90)
        {
            qual = "stroke-width:2;stroke:green;fill:none;"
            qualfill = "stroke-width:2;stroke:green;fill:green;"
        }
        else
        if (status_pc >= 50)
        {
            qual = "stroke-width:2;stroke:orange;fill:none;"
            qualfill = "stroke-width:2;stroke:orange;fill:orange;"
        }
        else
        {
            qual = "stroke-width:2;stroke:red;fill:none;"
            qualfill = "stroke-width:2;stroke:red;fill:red;"
        }

        if (id==0)
        {
          var newelem = document.createElementNS(svgNS,"circle");
          newelem.setAttributeNS(null,"id",elementName);
          newelem.setAttributeNS(null,"cx",elementX);
          newelem.setAttributeNS(null,"cy",elementY);
          newelem.setAttributeNS(null,"r",elementR);
          newelem.setAttributeNS(null,"style","stroke-width:2;stroke:magenta;fill:none;");
          newelem.setAttributeNS(null,"title","ID : " + elementName + "\nLevel : " + elementLevel + " mV \nAge : " + ToHMSstring(elementAge));
          svg.appendChild(newelem);

          var newelem2 = document.createElementNS(svgNS,"circle");
          newelem2.setAttributeNS(null,"id",elementName);
          newelem2.setAttributeNS(null,"cx",elementX);
          newelem2.setAttributeNS(null,"cy",elementY);
          newelem2.setAttributeNS(null,"r",elementR*0.6);
          newelem2.setAttributeNS(null,"style","stroke-width:2;stroke:magenta;fill:none;");
          newelem2.setAttributeNS(null,"title","ID : " + elementName + "\nLevel : " + elementLevel + " mv \nAge : " + ToHMSstring(elementAge));
          svg.appendChild(newelem2);
        }
        else if (id>=200)
        {
          drawRect(svg,svgNS,elementName,elementX,elementY,2*elementR,2*elementR, elementLevel,elementAge,"stroke-width:2;stroke:magenta;fill:none;");
          drawBattery(svg,svgNS,elementName,elementX,elementY, status_pc/100, elementLevel,elementAge,qual,qualfill);
        }
        else
        {
          var newelem = document.createElementNS(svgNS,"circle");
          newelem.setAttributeNS(null,"id",elementName);
          newelem.setAttributeNS(null,"cx",elementX);
          newelem.setAttributeNS(null,"cy",elementY);
          newelem.setAttributeNS(null,"r",elementR);
          newelem.setAttributeNS(null,"style","stroke-width:2;stroke:magenta;fill:none;");
          newelem.setAttributeNS(null,"title","ID : " + elementName + "\nLevel : " + elementLevel + " mV \nAge : " + ToHMSstring(elementAge));
          svg.appendChild(newelem);

          drawBattery(svg,svgNS,elementName,elementX,elementY, status_pc/100, elementLevel,elementAge,qual,qualfill);
        }
    }
    else
    {
        _logerror("Don't find position for battery id " + binfo[0]);
    }
    */
  }

  function addBattery(svg, id, level, age)
  {
    var bposition = getPositionById(id);
    if (bposition != null)
    {
        // ajout d'une batterie
        var elementID = id;
        var elementName = elementID;
        var elementR = RAYON_BATTERIE;// TODO: pour l'instant rayon des batteries => a changer en rectangle...
        var elementX = bposition.x;
        var elementY = bposition.y;
        var elementLevel = level;
        var elementAge = age;
        previousElems.push(elementName);

        // choix de la couleur de la batterie en fonction du niveau (en mV), on convertit en %
        var status_pc = 0.0;
        if (elementLevel>=3800.0)
        {
            status_pc = 100;
        }
        else
        if (elementLevel>=3500.0)
        {
            status_pc = 100.0 * (elementLevel-3500.0) / (3800.0 - 3500.0);
        }

        var qual,qualfill;
        if (status_pc >= 66)
        {
            qual = "stroke-width:2;stroke:green;fill:none;"
            qualfill = "stroke-width:2;stroke:green;fill:green;"
        }
        else
        if (status_pc >= 33)
        {
            qual = "stroke-width:2;stroke:orange;fill:none;"
            qualfill = "stroke-width:2;stroke:orange;fill:orange;"
        }
        else
        {
            qual = "stroke-width:2;stroke:red;fill:none;"
            qualfill = "stroke-width:2;stroke:red;fill:red;"
        }

        if (id==0)
        {
          var newelem = document.createElementNS(svgNS,"circle");
          newelem.setAttributeNS(null,"id",elementName);
          newelem.setAttributeNS(null,"cx",elementX);
          newelem.setAttributeNS(null,"cy",elementY);
          newelem.setAttributeNS(null,"r",elementR);
          newelem.setAttributeNS(null,"style","stroke-width:2;stroke:magenta;fill:none;");
          newelem.setAttributeNS(null,"title","ID : " + elementName + "\nLevel : " + elementLevel + " mV \nAge : " + ToHMSstring(elementAge));
          svg.appendChild(newelem);

          var newelem2 = document.createElementNS(svgNS,"circle");
          newelem2.setAttributeNS(null,"id",elementName);
          newelem2.setAttributeNS(null,"cx",elementX);
          newelem2.setAttributeNS(null,"cy",elementY);
          newelem2.setAttributeNS(null,"r",elementR*0.6);
          newelem2.setAttributeNS(null,"style","stroke-width:2;stroke:magenta;fill:none;");
          newelem2.setAttributeNS(null,"title","ID : " + elementName + "\nLevel : " + elementLevel + " mv \nAge : " + ToHMSstring(elementAge));
          svg.appendChild(newelem2);
        }
        else if (id>=200)
        {
          drawRect(svg,svgNS,elementName,elementX,elementY,2*elementR,2*elementR, elementLevel,elementAge,"stroke-width:2;stroke:magenta;fill:none;");
          drawBattery(svg,svgNS,elementName,elementX,elementY, status_pc/100, elementLevel,elementAge,qual,qualfill);
        }
        else
        {
          var newelem = document.createElementNS(svgNS,"circle");
          newelem.setAttributeNS(null,"id",elementName);
          newelem.setAttributeNS(null,"cx",elementX);
          newelem.setAttributeNS(null,"cy",elementY);
          newelem.setAttributeNS(null,"r",elementR);
          newelem.setAttributeNS(null,"style","stroke-width:2;stroke:magenta;fill:none;");
          newelem.setAttributeNS(null,"title","ID : " + elementName + "\nLevel : " + elementLevel + " mV \nAge : " + ToHMSstring(elementAge));
          svg.appendChild(newelem);

          drawBattery(svg,svgNS,elementName,elementX,elementY, status_pc/100, elementLevel,elementAge,qual,qualfill);
        }
    }
    else
    {
        _logerror("Don't find position for battery id " + binfo[0]);
    }
  }


  function addLevel(svg, senderid, receiverid, rxlevel, age) //senderid, receiverid, rxlevel, age
  {
    var sposition = getPositionById(senderid);//position du sender
    var rposition = getPositionById(receiverid);//position du receiver
    if ((sposition != null)&&(rposition != null))
    {
        // ajout d'un lien
        var elementIDSource = senderid;
        var elementIDreceiver = receiverid;
        var elementName = "Link" + elementIDSource;

        var elementS = intersect(sposition.x, sposition.y, rposition.x, rposition.y, RAYON_ACCROCHE);
        var elementR = intersect(rposition.x, rposition.y, sposition.x, sposition.y, RAYON_ACCROCHE);
        //var elementMID = intersect(sposition.x, sposition.y, rposition.x, rposition.y, distance(sposition.x, sposition.y, rposition.x, rposition.y) / 2.0);
        var elementLevel = rxlevel;
        var elementAge = age;

        // gestion de la couleur et pointillés en fonction de l'age
        // style="stroke-dasharray: 5, 5"
        var color = "red";
        var dsh = "2,6";
        if (elementAge < 400)
        {
            color = "green";
            dsh = null;
        }
        else
        if (elementAge < 900)
        {
            color = "orange";
            dsh = "6,2";
        }
        // gestion de l'epaisseur en fonction du niveau
        var width = 2;//-88 -113 -117 -124
        if (elementLevel >= -88)
        {
            width = "10";
        }
        else
        if (elementLevel >= -113)
        {
            width = "8";
        }
        else
        if (elementLevel >= -117)
        {
            width = "6";
        }
        else
        if (elementLevel >= -124)
        {
            width = "4";
        }
        previousElems.push(elementName);
        var newelem = document.createElementNS(svgNS,"line");
        //<line x1="0" y1="0" x2="200" y2="200" style="stroke:rgb(255,0,0);stroke-width:2" />
        newelem.setAttributeNS(null,"id",elementName);
        newelem.setAttributeNS(null,"stroke-width",width);
        //newelem.setAttributeNS(null,"fill","none");
        newelem.setAttributeNS(null,"stroke",color);
        if (dsh != null)
        {
            newelem.setAttributeNS(null,"stroke-dasharray",dsh);
        }
        newelem.setAttributeNS(null,"x1",elementS.x);
        newelem.setAttributeNS(null,"y1",elementS.y);
        newelem.setAttributeNS(null,"x2",elementR.x);
        newelem.setAttributeNS(null,"y2",elementR.y);

        newelem.setAttributeNS(null,"title","Level : " + elementLevel + " dB\nAge : " + ToHMSstring(elementAge));
        /*
        //var path = 'M' + elementS.x + ',' + elementS.y + 'L ' + elementMID.x + ',' + elementMID.y + ' L' + elementR.x + ',' + elementR.y;
        var newelem = document.createElementNS(svgNS,"path");
        newelem.setAttributeNS(null,"id",elementName);
        //newelem.setAttributeNS(null,"marker-mid","url(#head)");
        newelem.setAttributeNS(null,"stroke-width",width);
        newelem.setAttributeNS(null,"fill","none");
        newelem.setAttributeNS(null,"stroke",color);
        if (dsh != null)
        {
            newelem.setAttributeNS(null,"stroke-dasharray",dsh);
        }
        var path = 'M' + elementS.x + ',' + elementS.y + 'L ' + elementMID.x + ',' + elementMID.y + ' L' + elementR.x + ',' + elementR.y;
        newelem.setAttributeNS(null,"d",path);

        newelem.setAttributeNS(null,"title","Level : " + elementLevel + " dB\nAge : " + ToHMSstring(elementAge));

        */


        svg.appendChild(newelem);
    }
    else
    {
        _logerror("Don't find position for id " + linfo[0] + " and/or " + linfo[1]);
    }
  }

  function addKnownPosition(svg)
  {
    var i;
    var knownIdPositionCount = positionInfo.length;
    for(i=0;i<knownIdPositionCount;i++)
    {
        addPositionWithoutBattery(svg, positionInfo[i][0]);
    }
  }
  function updateDisplay(dataInfos)
  {
    try
    {
        // Effaçons les éléments existants
        while(previousElems.length > 0)
        {
            var lastElem = previousElems.pop();
            try
            {
                var svgelem = document.getElementById(lastElem);
                svgelem.parentElement.removeChild(svgelem);
            }
            catch(err)
            {
                _logexception(err.message);
            }
        }

        // svg pour accéder aux éléments graphiques
        var svg = document.getElementById("map");

        // Add all known position elements
        addKnownPosition(svg);

        // Et ajoutons de nouveaux éléments
        if (dataInfos.length == 2)
        {
            try
            {
                // il y a les 2 tableaux
                var batteryArray = dataInfos[0];
                var levelArray = dataInfos[1];

                var i;

                // gestion des battery
                var batteryCount = batteryArray.length;//_loginfo("Il y a " + batteryCount + " batteries");
                for(i=0;i<batteryCount;i++)
                {
                    addBattery(svg, batteryArray[i][0], batteryArray[i][1], batteryArray[i][2])// id level age
                }
                // gestion des level
                var levelCount = levelArray.length;//_loginfo("Il y a " + levelCount + " levels");
                for(i=0;i<levelCount;i++)
                {
                    addLevel(svg, levelArray[i][0], levelArray[i][1], levelArray[i][2], levelArray[i][3]); //senderid, receiverid, rxlevel, age
                }
            }
            catch(err)
            {
                _logexception(err.message);
            }
        }

        _loginfo("Display updated with " + previousElems.length + " element(s)");
    }
    catch(err)
    {
        _logexception(err.message);
        //alert(err.message);
    }

    setTimeout(getData, 10000);
  }
  </script>

  <style>
  svg {
  display:block;
  width:100%;
  height:100%;
  margin:auto;
  border:thick double navy;
  background-color:lightblue;
  }
  body {
  font-family:cursive;
  }
  </style>

  </head>
  <body onload="getData()">
  <?php
    print "<input type='button' value='".MyGetText(87)."' onclick='window.close();'> &nbsp;"; // Close button
    print "<input type='button' value='".MyGetText(72)."' onclick='ViewRadioLog();'> &nbsp;"; // View log button
    print "<input type='button' value='".MyGetText(82)."' onclick='DelRadiodata(\"".MyGetText(82)."\");'> &nbsp;"; // Clear data button
  ?>
  <svg id="map" viewbox="0 0 <?php echo $sizeX; ?> <?php echo $sizeY; ?>" xmlns="http://www.w3.org/2000/svg">
  <title>Radio quality</title>
  <defs>
    <marker id='head' orient='auto' markerWidth='4' markerHeight='4'
            refX='0.1' refY='2'>
      <path d='M0,0 V4 L2,2 Z' fill='red' />
    </marker>

  </defs>
  <g id="background">
    <image xlink:href="<?php echo $srcmap; ?>" x="0" y="0" width="100%" height="100%"/>
  </g>
  </svg>
  </body>
</html>

