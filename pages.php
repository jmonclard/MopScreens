<?php
  /*
  Copyright 2014-2019 J. MONCLARD

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
  //date_default_timezone_set('Europe/Paris');
  include_once('functions.php');
  include_once('lang.php');
  redirectSwitchUsers();

  include_once('screenfunctions.php');
  include_once('config.php');

	$_SESSION['CurrentLanguage'] = isset($_SESSION['CurrentLanguage']) ? $_SESSION['CurrentLanguage'] : autoSelectLanguage(array('fr','en','sv'),'en');


  $PHP_SELF = $_SERVER['PHP_SELF'];
  $link = ConnectToDB();

  $screenIndex = isset($_GET['p']) ? intval($_GET['p']) : 1;

  //--------------- Panel class -------------------
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
    var $displaynomprenom;

    function Panel($num)
    {
      $this->numpanel = $num;
    }

    function Initialise($r,$cls)
    {
      $this->content=$r['panel'.$this->numpanel.'content'];
      $this->mode=$r['panel'.$this->numpanel.'mode'];
      $this->tm_count=$r['panel'.$this->numpanel.'tm_count'];
      $this->alternate=$r['panel'.$this->numpanel.'alternate'];
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
      $this->displaynomprenom=$r['panel'.$this->numpanel.'displaynomprenom'];

      $this->classes = $cls;
    }
  }

  //-----------------------------------------------------------------------------------

  $panel1 = new Panel(1);
  $panel2 = new Panel(2);
  $panel3 = new Panel(3);
  $panel4 = new Panel(4);
  $panels = array($panel1,$panel2,$panel3,$panel4);

  $arr_cls = array();
  $sql = "SELECT rcid FROM resultconfig WHERE active=1";
  $res = mysqli_query($link, $sql);
  if (mysqli_num_rows($res) > 0)
  {
    $r = mysqli_fetch_array($res);
    $rcid=$r['rcid'];

    $sql = "SELECT * FROM resultscreen WHERE rcid=$rcid AND sid=$screenIndex";
    $res = mysqli_query($link, $sql);
    if (mysqli_num_rows($res) > 0)
    {
      // Recall user configuration
      $r = mysqli_fetch_array($res);
      $cid=$r['cid'];
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
        $panels[$i-1]->Initialise($r, null);
      }

      $classPanels = array();
      $classNamePanels = array();
      $sql_classes = array(-1);

      for($i=0; $i<$panelscount; $i++)
      {
        if(($panels[$i]->content == CST_CONTENT_RESULT) || ($panels[$i]->content == CST_CONTENT_SUMMARY) || ($panels[$i]->content == CST_CONTENT_RADIO) || ($panels[$i]->content == CST_CONTENT_START))
        {
          //-----------------------------------------------------------------
          // Class recollection
          $sql = "SELECT id FROM resultclass WHERE cid=$cid AND rcid=$rcid AND sid=$screenIndex AND panel=".($i+1);
          $res = mysqli_query($link, $sql);
          if (mysqli_num_rows($res) > 0)
          {
            while ($r = mysqli_fetch_array($res))
            {
              $myid = $r['id'];
              $classPanels[$i][] = $myid;

              $sql = "SELECT name FROM mopclass WHERE cid=$cid AND id=$myid";
              $resname = mysqli_query($link, $sql);
              if (mysqli_num_rows($resname) > 0)
              {
                if ($rname = mysqli_fetch_array($resname))
                {
                  $classNamePanels[$i][] = $rname['name'];
                }
                else
                {
                  $classNamePanels[$i][] = $myid;
                }
              }
              else
              {
                  $classNamePanels[$i][] = $myid;
              }
            }
          }
          if((isset($classPanels[$i])) && (is_array($classPanels[$i])))
          {
            $sql_classes = array_merge($classPanels[$i], $sql_classes);
          }
        }
      }

      $sql = 'SELECT cls, COUNT(*) AS nb FROM mopcompetitor WHERE cid='.$cid.' AND cls IN('.implode(', ', $sql_classes).') GROUP BY cid, cls';
      $res = mysqli_query($link, $sql);
      while($data = mysqli_fetch_array($res))
      {
        $arr_cls[$data['cls']] = $data['nb'];
      }
    }
  }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="initial-scale=1.25">

    <title>CO (page <?php print $screenIndex; ?>)</title>
    <!--	<link rel="stylesheet" type="text/css" href="styles/<?php print $style; ?>?timestamp=<?php print time(); ?>" /> -->
    <link rel="stylesheet" type="text/css" href="styles/<?php print $style; ?>" />

    <script type="text/javascript">
      <!--
<?php
  //------------- Javascript constants definition from php --------
  print "var NB_PANEL = ".NB_PANEL.";\n";
?>
      var screenIndex;
      var displayScrollIndex = [1,1,1,1];
      var rcid;
      var phpTitle;
      var phpcls;
      var phpcmpId;
      var phpleg;
      var phpord;
      var phpmode;
      var phpradio;
      var phpnumbercls;
      var phpscrolltime;
      var phpradioctrl;
      var phpupdateduration;
      var categorieIndex = [0, 0, 0, 0];
      var after_decrement_counter = [5, 5, 5, 5];
      var before_decrement_counter = [5, 5, 5, 5];
      var tm_count;
      var nbradio = <?php
      switch($panelscount)
      {
        case 1:
          echo CST_NBRADIO_ONEPANEL;
        break;
        case 2:
          echo CST_NBRADIO_TWOPANELS;
        break;
        default:
          echo CST_NBRADIO_PANELS;
        break;
      }
      ?>;

      window.onload = function()
      {
<?php
  defineVariableArrNx("phpTitle", $classNamePanels, $panelscount);
  defineVariableArrNx("phpcls", $classPanels, $panelscount);

  defineVariableArr("phpfirstline", $panels[0]->firstline, $panels[1]->firstline, $panels[2]->firstline, $panels[3]->firstline);
  defineVariableArr("phpfixedlines", $panels[0]->fixedlines, $panels[1]->fixedlines, $panels[2]->fixedlines, $panels[3]->fixedlines);
  defineVariableArr("phpscrolledlines", $panels[0]->scrolledlines, $panels[1]->scrolledlines, $panels[2]->scrolledlines, $panels[3]->scrolledlines);
  defineVariableArr("phpscrolltime", $panels[0]->scrolltime, $panels[1]->scrolltime, $panels[2]->scrolltime, $panels[3]->scrolltime);
  defineVariableArr("phpscrollaftertime", $panels[0]->scrollaftertime, $panels[1]->scrollaftertime, $panels[2]->scrollaftertime, $panels[3]->scrollaftertime);
  defineVariableArr("phpscrollbeforetime", $panels[0]->scrollbeforetime, $panels[1]->scrollbeforetime, $panels[2]->scrollbeforetime, $panels[3]->scrollbeforetime);
  defineVariableArr("phpupdateduration", $panels[0]->updateduration, $panels[1]->updateduration, $panels[2]->updateduration, $panels[3]->updateduration);

  defineVariableArr("phpcontent", $panels[0]->content, $panels[1]->content, $panels[2]->content, $panels[3]->content);

  defineVariableArr("phpmode", $panels[0]->mode, $panels[1]->mode, $panels[2]->mode, $panels[3]->mode);
  defineVariableArr("phpradioctrl", $panels[0]->radioctrl, $panels[1]->radioctrl, $panels[2]->radioctrl, $panels[3]->radioctrl);

  defineVariableArr("phpdisplaynomprenom", $panels[0]->displaynomprenom, $panels[1]->displaynomprenom, $panels[2]->displaynomprenom, $panels[3]->displaynomprenom);


  defineVariableArr("phpalternate", $panels[0]->alternate, $panels[1]->alternate, $panels[2]->alternate, $panels[3]->alternate);

  defineVariableArr("phpcmpId", $cid, $cid, $cid, $cid);
  defineVariableArr("phpleg", 1, 1, 1, 1);
  defineVariableArr("phpord", 0, 0, 0, 0);
  defineVariableArr("phpradio", 'finish', 'finish', 'finish', 'finish');

  defineVariableArrFromArr("phpnumbercls", $arr_cls);

  defineVariable("screenIndex", $screenIndex);
  defineVariable("rcid", $rcid);

  defineVariable("tm_count", $panels[0]->tm_count);
?>
        tm_count = parseInt(tm_count, 10);
        var panel = 0;
        for(panel=0;panel<<?php echo NB_PANEL; ?>;panel++)
        {
          phpmode[panel] = parseInt(phpmode[panel], 10);
          phpdisplaynomprenom[panel] = parseInt(phpdisplaynomprenom[panel], 10);
          phpfirstline[panel] = parseInt(phpfirstline[panel], 10);
          phpscrolledlines[panel] = parseInt(phpscrolledlines[panel], 10);
          phpfixedlines[panel] = parseInt(phpfixedlines[panel], 10);
          phpscrolltime[panel] = parseInt(phpscrolltime[panel], 10);
          phpscrollbeforetime[panel] = parseInt(phpscrollbeforetime[panel], 10);
          phpscrollaftertime[panel] = parseInt(phpscrollaftertime[panel], 10);
          if(phpscrolltime[panel] <= 0)
            phpscrolltime[panel] = 10;
          if(phpscrollaftertime[panel] <= 0)
            phpscrollaftertime[panel] = 50;
          if(phpscrollbeforetime[panel] <= 0)
            phpscrollbeforetime[panel] = 50;

          after_decrement_counter[panel] = phpscrollaftertime[panel] / phpscrolltime[panel];
          before_decrement_counter[panel] = phpscrollbeforetime[panel] / phpscrolltime[panel];
        }

        displayScrollIndex = [phpfixedlines[0] + phpfirstline[0] - 1, phpfixedlines[1] + phpfirstline[1] - 1, phpfixedlines[2] + phpfirstline[2] - 1, phpfixedlines[3] + phpfirstline[3] - 1];

<?php

  $bRefreshTable = false;
  $bRefreshStart = false;
  $bRefreshPage = false;
  $bRefreshRelay = false;
  $bRefreshShowo = false;
  $bRefreshSummary = false;
  $bRefreshMultistage = false;

  $bRefreshBlog = false;
  $bRefreshRadio = false;
  $bRefreshSlide = false;
?>
      updatePage();
<?php
  for($i=0;$i<$panelscount;$i++)
  {
    switch($panels[$i]->content)
    {
      case CST_CONTENT_PICTURE:
      break;
      case CST_CONTENT_TEXT:
      break;
      case CST_CONTENT_HTML:
      break;
      case CST_CONTENT_START:
        switch($panels[$i]->mode)
        {
          case CST_MODE_INDIVIDUAL:
            echo 'updateDisplayStart'.($i+1).'();'."\n";
            $bRefreshStart = true;
          break;
        }
      break;
      case CST_CONTENT_RESULT:
        switch($panels[$i]->mode)
        {
          case CST_MODE_INDIVIDUAL:
            echo 'updateDisplay'.($i+1).'();'."\n";
            $bRefreshTable = true;
          break;
          case CST_MODE_RELAY:
            if($i == 0)
            {
              ?>
              updateRelay();
              <?php
              $bRefreshRelay = true;
            }
          break;
          case CST_MODE_SHOWO:
            if($i == 0)
            {
              ?>
              updateShowO(0);
              <?php
              $bRefreshShowo = true;
            }
          break;
          case CST_MODE_MULTISTAGE:
              ?>
              updateMultistage(<?php echo $i; ?>);
              <?php
              $bRefreshMultistage = true;
          break;
        }
      break;
      case CST_CONTENT_SUMMARY:
        switch($panels[$i]->mode)
        {
          case CST_MODE_INDIVIDUAL:
            echo 'updateDisplaySummaries'.($i+1).'();'."\n";
            $bRefreshSummary = true;
          break;
          case CST_MODE_RELAY:
            if($i == 0)
            {
              ?>
              updateRelay();
              <?php
              $bRefreshSummary = true;
            }
          break;
        }
      break;
      case CST_CONTENT_BLOG:
        echo 'updateBlog('.$i.');'."\n";
        $bRefreshBlog = true;
      break;
      case CST_CONTENT_SLIDES:
        $bRefreshSlide = true;
      break;
      case CST_CONTENT_RADIO:
        echo 'updateRadio('.$i.');'."\n";
        $bRefreshRadio = true;
      break;
      default:
        // not supported
      break;
    }
  }
  if($bRefreshTable)
  {
    echo 'updateTables();'."\n";
    echo 'create_refresh_table();'."\n";
  }
  if($bRefreshStart)
  {
    echo 'updateStarts();'."\n";
    echo 'create_refresh_start();'."\n";
  }
  if($bRefreshRelay)
  {
    echo 'create_refresh_relay();'."\n";
  }
  if($bRefreshShowo)
  {
    echo 'create_refresh_showO();'."\n";
  }
  if($bRefreshSummary)
  {
    echo 'create_refresh_summary();'."\n";
  }
  if($bRefreshMultistage)
  {
    echo 'updateMultistages();'."\n";
   	echo 'create_refresh_multistage();'."\n";
  }

?>
        create_refresh_display();
        create_refresh_page();

      } // onload

      var ATTENTE_BASE_s = 5;
      var ATTENTE_PAGE_s = 10;

	var pos_tm_finish = new Array();

      var dataArray = new Array(4);
      var data2Array = new Array(4);
      data2Array[0] = new Array(16);
      data2Array[1] = new Array(16);
      data2Array[2] = new Array(16);
      data2Array[3] = new Array(16);
      var tableUpdated = new Array(4);

      var bfirst = true;
      var mytime = 0;
      var nowtime = 0;

      function isUndefined(o)
      {
          return typeof o == "undefined";
      }

      function updatePage()
      {
        var xmlhttp = null;
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function()
        {
          if((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
          {
            var temp = eval(xmlhttp.responseText)
            if((!bfirst) && (parseInt(temp, 10) != mytime))
            {
              window.location = self.location;
            }
            else
            {
              mytime = parseInt(temp[0], 10);
              nowtime = parseInt(temp[1], 10);
              bfirst = false;
            }
          }
        }
        xmlhttp.open("GET", "aj_refreshpage.php?rcid=" + rcid + "&sid=" + screenIndex, false);
        xmlhttp.send();
      }

      function updateTable(panelIndex)
      {
        if  (panelIndex < NB_PANEL)
        {
          var xmlhttp = null;
          if (window.XMLHttpRequest)
          {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
          }
          else
          {// code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
          }
          xmlhttp.onreadystatechange = function()
          {
            if((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
            {
              dataArray[panelIndex] = eval(xmlhttp.responseText);
              data2Array[panelIndex][categorieIndex[panelIndex]] = dataArray[panelIndex];
              tableUpdated[panelIndex] = true;
            }
          }
          var mylimit = ((phpcontent[panelIndex] == <?php echo CST_CONTENT_SUMMARY; ?>) ? phpfixedlines[panelIndex] : 99999);
          xmlhttp.open("GET", "aj_refreshtable.php?cls=" + phpcls[panelIndex][categorieIndex[panelIndex]] +
                              "&cmpId=" + phpcmpId[panelIndex] +
                              "&leg=" + phpleg[panelIndex] +
                              "&ord=" + phpord[panelIndex] +
                              "&radio=" + phpradio[panelIndex] +
                              "&rcid=" + rcid +
                              "&sid=" + screenIndex +
                              "&limit=" + mylimit +
                              "&nbradio=" + nbradio, false);
          xmlhttp.send();
        }
      }

      function updateStart(panelIndex)
      {
        if  (panelIndex < NB_PANEL)
        {
          var xmlhttp = null;
          if (window.XMLHttpRequest)
          {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
          }
          else
          {// code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
          }
          xmlhttp.onreadystatechange = function()
          {
            if((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
            {
              //alert(xmlhttp.responseText);
              dataArray[panelIndex] = eval(xmlhttp.responseText);
              tableUpdated[panelIndex] = true;
            }
          }
          xmlhttp.open("GET", "aj_refreshstart.php?cls=" + phpcls[panelIndex][categorieIndex[panelIndex]] +
                              "&cmpId=" + phpcmpId[panelIndex] +
                              "&leg=" + phpleg[panelIndex] +
                              "&ord=" + phpord[panelIndex] +
                              "&radio=" + phpradio[panelIndex] +
                              "&rcid=" + rcid +
                              "&sid=" + screenIndex, false);
          xmlhttp.send();
        }
      }

      function updateRelay()
      {
        var panelIndex = 0;
        var xmlhttp = null;
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp = new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function()
        {
          if((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
          {
            //alert(xmlhttp.responseText);
            dataArray[panelIndex] = eval(xmlhttp.responseText);
            data2Array[panelIndex][categorieIndex[panelIndex]] = dataArray[panelIndex];
            //alert(dataArray[0]);
            tableUpdated[panelIndex] = true;
          }
        }

        var mylimit = ((phpcontent[panelIndex] == <?php echo CST_CONTENT_SUMMARY; ?>) ? phpfixedlines[panelIndex] : 99999);
        xmlhttp.open("GET", "aj_refreshrelay.php?cls=" + phpcls[panelIndex][categorieIndex[panelIndex]] +
                            "&cmpId=" + phpcmpId[panelIndex] +
                            "&leg=" + phpleg[panelIndex] +
                            "&ord=" + phpord[panelIndex] +
                            "&radio=" + phpradio[panelIndex] +
                            "&rcid=" + rcid +
                            "&sid=" + screenIndex +
                            "&limit=" + mylimit, false);
        xmlhttp.send();
      }

      function updateMultistage(panelIndex)
      {
      	if  (panelIndex < NB_PANEL)
        {
        	var xmlhttp = null;
        	if (window.XMLHttpRequest)
        	{// code for IE7+, Firefox, Chrome, Opera, Safari
          	xmlhttp = new XMLHttpRequest();
        	}
        	else
        	{// code for IE6, IE5
          	xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        	}
        	xmlhttp.onreadystatechange = function()
        	{
          	if((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
          	{
            	//alert(xmlhttp.responseText);
            	dataArray[panelIndex] = eval(xmlhttp.responseText);
            	data2Array[panelIndex][categorieIndex[panelIndex]] = dataArray[panelIndex];
            	//alert(dataArray[0]);
            	tableUpdated[panelIndex] = true;
          	}
        	}

        	var mylimit = ((phpcontent[panelIndex] == <?php echo CST_CONTENT_SUMMARY; ?>) ? phpfixedlines[panelIndex] : 99999);
        	xmlhttp.open("GET", "aj_refreshmultistages.php?cls=" + phpcls[panelIndex][categorieIndex[panelIndex]] +
                            	"&cmpId=" + phpcmpId[panelIndex] +
                            	"&leg=" + phpleg[panelIndex] +
                            	"&ord=" + phpord[panelIndex] +
                            	"&radio=" + phpradio[panelIndex] +
                            	"&rcid=" + rcid +
                            	"&sid=" + screenIndex +
                            	"&limit=" + mylimit +
                              "&nbradio=" + nbradio +
                              "&alternate=" + phpalternate[panelIndex], false);
        	xmlhttp.send();
        }
      }

	  function updateShowO(panelIndex)
      {
        var xmlhttp = null;
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp = new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function()
        {
          if((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
          {
            //alert(xmlhttp.responseText);
            dataArray[panelIndex] = eval(xmlhttp.responseText);
            //alert(dataArray[0]);
            tableUpdated[panelIndex] = true;
          }
        }

        var mylimit = ((phpcontent[panelIndex] == <?php echo CST_CONTENT_SUMMARY; ?>) ? phpfixedlines[panelIndex] : 99999);
        var myqualif = ((tm_count > 1) ? "1" : "0");
        xmlhttp.open("GET", "aj_refreshshowo.php?cls=" + phpcls[panelIndex][categorieIndex[panelIndex]] +
                            "&cmpId=" + phpcmpId[panelIndex] +
                            "&rcid=" + rcid +
                            "&sid=" + screenIndex +
                            "&limit=" + mylimit +
                            "&qualif=" + myqualif, false);
        xmlhttp.send();
      }

      function updateTables()
      {
<?php
  for ($i=0; $i<$panelscount; $i++)
  {
    if((($panels[$i]->content == CST_CONTENT_SUMMARY) && ($panels[$i]->mode == CST_MODE_INDIVIDUAL)) ||
        (($panels[$i]->content == CST_CONTENT_RESULT) && ($panels[$i]->mode == CST_MODE_INDIVIDUAL)))
    {
      print 'updateTable('.$i.');'."\n";
    }
  }
?>
      }

      function updateMultistages()
      {
<?php
  for ($i=0; $i<$panelscount; $i++)
  {
    if(($panels[$i]->mode == CST_MODE_MULTISTAGE) && ((($panels[$i]->content == CST_CONTENT_SUMMARY) && ($panels[$i]->mode == CST_MODE_INDIVIDUAL)) ||
        (($panels[$i]->content == CST_CONTENT_RESULT) && ($panels[$i]->mode == CST_MODE_INDIVIDUAL))))
    {
      print 'updateMultistage('.$i.');'."\n";
    }
  }
?>
      }

	  function updateSummaries()
      {
<?php
  for ($i=0; $i<$panelscount; $i++)
  {
    print 'updateDisplaySummaries'.($i+1).'();'."\n";
  }
?>
      }

      function updateBlogs()
      {
<?php
  for ($i=0; $i<$panelscount; $i++)
  {
    if($panels[$i]->content == CST_CONTENT_BLOG)
    {
      print 'updateBlog('.$i.');'."\n";
    }
  }
?>
      }

      function updateRadios()
      {
<?php
  for ($i=0; $i<$panelscount; $i++)
  {
    if($panels[$i]->content == CST_CONTENT_RADIO)
    {
      print 'updateRadio('.$i.');'."\n";
    }
  }
?>
      }

      function updateStarts()
      {
<?php
  for ($i=0; $i<$panelscount; $i++)
  {
    if($panels[$i]->content == CST_CONTENT_START)
    {
      print 'updateStart('.$i.');'."\n";
    }
  }
?>
      }

      function updateShowOs()
      {
<?php
  for ($i=0; $i<$panelscount; $i++)
  {
    if(($panels[$i]->content == CST_CONTENT_RESULT) && ($panels[$i]->mode == CST_MODE_SHOWO))
    {
      print 'updateShowO('.$i.');'."\n";
    }
  }
?>
      }


      function generateRelayCells(line, prefix_class, pos_min_displayable, pos_max_displayable, cells_for_tm,tmcount,bFoundRadio,pos_recherche, alternate, panelIndex)
      {
        r = '';
        for(var e in line)
        {
          cell = ((line[e] === '') ? "&nbsp;" : line[e]);
          if(e > pos_min_displayable)
          {
            if(e < pos_max_displayable)
            {
           		datacol = e - pos_min_displayable-1;
           		data_cols_per_tm = 7;
		      		switch(tmcount)
		      		{
		      			case 1: /* useless */
		      	  		break;

		      			case 2:
		      	  		switch (datacol % data_cols_per_tm)
		      	  		{
		      	    		case 0:
		      	      		r += '<td class="td2relay2">'+ cell + '</td>\r\n';
		      	      		break;
		      	    		case 1:
		      	      		r += '<td class="td2relay3">' + cell + '</td>\r\n';
		      	      		break;
		      	    		case 2:
		      	      		r += '<td class="td2relay4">' + cell + '</td>\r\n';
		      	      		break;
		      	    		case 3:
		      	      		break;
		      	    		case 4:
		      	      		r += '<td class="td2relay5finish2">' + cell +'</td>\r\n';
		      	      		break;
		      	    		case 5:
		      	      		r += '<td class="td2relay6">' + cell + '</td>\r\n';
		      	      		break;
		      	    		case 6:
		      	      		r += '<td class="td2relay6">' + cell + '</td>\r\n';
		      	      		break;
		      	  		}
		      	  		break;

		      				case 3:
		      	  			switch (datacol % data_cols_per_tm)
		      	  			{
		      	    			case 0:
		      	      			r += '<td class="td3relay2">'+ cell + '</td>\r\n';
		      	      			break;
		      	    			case 1:
		      	      			r += '<td class="td3relay3">' + cell + '</td>\r\n';
		      	      			break;
		      	    			case 2:
		      	      			r += '<td class="td3relay4">' + cell + '</td>\r\n';
		      	      			break;
		      	    			case 3:
		      	      			break;
		      	    			case 4:
		      	      			r += '<td class="td3relay5finish3">' + cell +'</td>\r\n';
		      	      			break;
		      	    			case 5:
		      	      			r += '<td class="td3relay6">' + cell + '</td>\r\n';
		      	      			break;
		      	    			case 6:
		      	      			r += '<td class="td3relay6">' + cell + '</td>\r\n';
		      	      			break;
		      	  			}
		      	  			break;

     							case 4:
		      	  			switch (datacol % data_cols_per_tm)
		      	  			{
		      	    			case 0:
		      	      			r += '<td class="td4relay2">'+ cell + '</td>\r\n';
		      	      			break;
		      	    			case 1:
		      	      			r += '<td class="td4relay3">' + cell + '</td>\r\n';
		      	      			break;
		      	    			case 2:
		      	      			r += '<td class="td4relay4">' + cell + '</td>\r\n';
		      	      			break;
		      	    			case 3:
		      	      			break;
		      	    			case 4:
		      	      			r += '<td class="td4relay5finish4">' + cell +'</td>\r\n';
		      	      			break;
		      	    			case 5:
		      	      			break;
		      	    			case 6:
		      	      			r += '<td class="td4relay6">' + cell + '</td>\r\n';
		      	      			break;
		      	  			}
		      	  			break;

		      				case 5:
		      	  			switch (datacol % data_cols_per_tm)
		      	  			{
		      	    			case 0:
		      	      			r += '<td class="td5relay2">' + cell + '</td>\r\n';
		      	      			break;
		      	    			case 1:
		      	      			r += '<td class="td5relay3">' + cell + '</td>\r\n';
		      	      			break;
		      	    			case 2:
		      	    			case 3:
		      	      			break;
		      	    			case 4:
		      	      			r += '<td class="td5relay5finish5">' + cell +'</td>\r\n';
		      	      			break;
		      	    			case 5:
		      	      			break;
		      	    			case 6:
		      	      			r += '<td class="td5relay6">' + cell + '</td>\r\n';
		      	      			break;
		      	  			}
		      	  			break;

			      			case 6:
		      					if (alternate == 1) /* radios at end of line */
		{
		      	  				switch (datacol % data_cols_per_tm)
			{
		      	    				case 0:
		      	    				case 1:
		      	    				case 2:
		      	    				case 3:
		      	      				break;
		      	    				case 4:
		      	      				r += '<td class="td6relay5finish6">' + cell +'</td>\r\n';
		      	      				break;
		      	    				case 5:
		      	      				break;
		      	    				case 6:
		      	      				r += '<td class="td6relay6">' + cell + '</td>\r\n';
		      	      				break;
		      	  				}
			}
		      					else /* radio for each team members */
		      					{
		      	  				switch (datacol % data_cols_per_tm)
			{
		      	    				case 0:
		      	      				r += '<td class="td6relay2">' + cell + '</td>\r\n';
		      	      				break;
		      	    				case 1:
		      	    				case 2:
		      	    				case 3:
		      	      				break;
		      	    				case 4:
		      	      				r += '<td class="td6relay5finish6">' + cell +'</td>\r\n';
		      	      				break;
		      	    				case 5:
		      	      				break;
		      	    				case 6:
		      	      				r += '<td class="td6relay6">' + cell + '</td>\r\n';
		      	      				break;
			}
		}
		      	  			break;

		      				case 7:
		      	  			switch (datacol % data_cols_per_tm)
		      	  			{
		      	    			case 0:
		      	      			r += '<td class="td7relay2">' + cell + '</td>\r\n';
		      	      			break;
		      	    			case 1:
		      	    			case 2:
		      	    			case 3:
		      	      			break;
		      	    			case 4:
		      	      			r += '<td class="td7relay5finish7">' + cell +'</td>\r\n';
		      	      			break;
		      	    			case 5:
		      	      			break;
		      	    			case 6:
		      	      			r += '<td class="td7relay6">' + cell + '</td>\r\n';
		      	      			break;
            }
		      	  			break;

		      				case 8:
		      	  			switch (datacol % data_cols_per_tm)
		      	  			{
		      	    			case 0:
		      	    			case 1:
		      	    			case 2:
		      	    			case 3:
		      	      			break;
		      	    			case 4:
		      	      			r += '<td class="td8relay5finish8">' + cell +'</td>\r\n';
		      	      			break;
		      	    			case 5:
		      	      			break;
		      	    			case 6:
		      	      			r += '<td class="td8relay6">' + cell + '</td>\r\n';
		      	      			break;
          }
		      	  			break;

		      				case 9:
		      	  			switch (datacol % data_cols_per_tm)
		      	  			{
		      	    			case 0:
		      	    			case 1:
		      	    			case 2:
		      	    			case 3:
		      	      			break;
		      	    			case 4:
		      	      			r += '<td class="td9relay5finish9">' + cell +'</td>\r\n';
		      	      			break;
		      	    			case 5:
		      	      			break;
		      	    			case 6:
		      	      			r += '<td class="td9relay6">' + cell + '</td>\r\n';
		      	      			break;
        }
		      	  			break;

		      				case 10:
		      	  			switch (datacol % data_cols_per_tm)
		      	  			{
		      	    			case 0:
		      	    			case 1:
		      	    			case 2:
		      	    			case 3:
		      	      			break;
		      	    			case 4:
		      	      			r += '<td class="td10relay5finish10">' + cell +'</td>\r\n';
		      	      			break;
		      	    			case 5:
		      	      			break;
		      	    			case 6:
		      	      			r += '<td class="td10relay6">' + cell + '</td>\r\n';
		      	      			break;
		      	  			}
		      	  			break;

		      			}/* switch tmcount */

       			} /* if */
          } /* if */
        } /* for e in line... */

        /* in some display schemes, radios are at the end of the line */
        if((tmcount == 8) || ((alternate == 1) && (tmcount == 6)))
        {
          if((alternate == 1) && (tmcount == 6))
            r += '<td class="radio0_6alt">&nbsp;</td>\r\n';
          else if(tmcount == 8)
            r += '<td class="radio0_8">&nbsp;</td>\r\n';
          else /* should never happen */
            r += '<td class="radio0">&nbsp;</td>\r\n';
          if(bFoundRadio)
          {
            r += '<td class="radio1">' + ((line[pos_recherche] === '') ? "&nbsp;" : line[pos_recherche]) + '</td>\r\n';
            r += '<td class="radio2">' + ((line[pos_recherche+1] === '') ? "&nbsp;" : line[pos_recherche+1]) + '</td>\r\n';
            r += '<td class="radio3">' + ((line[pos_recherche+2] === '') ? "&nbsp;" : line[pos_recherche+2]) + '</td>\r\n';
            r += '<td class="radio4">' + ((line[pos_recherche+3] === '') ? "&nbsp;" : line[pos_recherche+3]) + '</td>\r\n';
          }
          else
          {
            r += '<td class="radio1">&nbsp;</td>\r\n';
            r += '<td class="radio2">&nbsp;</td>\r\n';
            r += '<td class="radio3">&nbsp;</td>\r\n';
            r += '<td class="radio4">&nbsp;</td>\r\n';
          }
        }

        return r;
      }

      function UrlExists(url)
      {
    	var http = new XMLHttpRequest();
    	http.open('HEAD', "http://192.168.0.10/cfco/" + url, false);
    	http.send();
    	//alert("http://192.168.0.10/cfco/" + url + " ----" + http.status);
    	return http.status!=404;
      }

      function formatName(text, numpanel)
      {
      	var arrstr = text.split(" ");
      	var names = "";
      	var firstnames = "";
      	var i = 0;
      	var result = "";
      	for(i=0;i<arrstr.length;i++)
      	{
      		if((arrstr[i].length > 1) && (arrstr[i].toUpperCase() == arrstr[i]))
      		{
      			names += arrstr[i] + " ";
      		}
      		else
      		{
	      		firstnames += arrstr[i] + " ";
      		}
      	}
      	switch(phpdisplaynomprenom[numpanel])
      	{
      		case 0:
      		case 3:
      			result = text;
      		break;
      		case 1:
      		case 4:
      			result = names + firstnames;
      		break;
      		case 2:
      		case 5:
      			result = firstnames + names;
      		break;
      	}
      	return result;
      }

      function addFlag(celltxt, numpanel)
      {
      	result = celltxt;
        hackflag = result.split("/", 2);
        if(hackflag.length == 2)
        {
        	{
        		if(phpdisplaynomprenom[numpanel] >= 3)
        		{
        			result = '<span style="background:url(img/flags-mini/' + hackflag[1] + '.png);background-size:contain;background-repeat:no-repeat;background-position:center;" class="countryflag">&nbsp;</span>' + formatName(hackflag[0], numpanel);
            	}
            	else
            	{
            		result = formatName(hackflag[0], numpanel);
            	}
        	}
        }
        return result;
      }

      function generateMultistageCells(line, count, prefix_class,panelscount,alternate, panelIndex)
      {
        r = '';
        switch (panelscount)
        {
          case 1:
            for(var e in line)
            {
              cell = ((line[e] === '') ? "&nbsp;" : line[e]);
              cell = addFlag(cell, panelIndex);
              if(e > 1)
              {
                {
                  r += '<td class="'+ prefix_class + (e-2) +'">' + cell + '</td>\r\n';
                }
              }
            }
            break;
          case 2:
            for(var e in line)
            {
              cell = ((line[e] === '') ? "&nbsp;" : line[e]);
              cell = addFlag(cell, panelIndex);
              if(e > 1)
              {
                {
                  r += '<td class="'+ prefix_class + (e-2) +'">' + cell + '</td>\r\n';
                }
              }
            }
            break;
          case 3:
            for(var e in line)
            {
              cell = ((line[e] === '') ? "&nbsp;" : line[e]);
              cell = addFlag(cell, panelIndex);
              if(e > 1)
              {
                if ((e<5)||(e>8)) // do not display radio
                {
                  {
                    r += '<td class="'+ prefix_class + (e-2) +'">' + cell + '</td>\r\n';
                  }
                }
              }
            }
            break;
          case 4:
            for(var e in line)
            {
              cell = ((line[e] === '') ? "&nbsp;" : line[e]);
              cell = addFlag(cell, panelIndex);
              if(e > 1)
              {
                if ((e<4)||(e>8)) // do not display club and radio
                {
                  {
                    r += '<td class="'+ prefix_class + (e-2) +'">' + cell + '</td>\r\n';
                  }
                }
              }
            }
            break;
        }
        return r;
      }

      function generateResultCells(line, count, prefix_class,panelscount,alternate, panelIndex)
      {
        r = '';
        switch (panelscount)
        {
          case 1:
            for(var e in line)
            {
              cell = ((line[e] === '') ? "&nbsp;" : line[e]);
              cell = addFlag(cell, panelIndex);
              if(e > 1)
              {
                if(e == (count - 1))
                {
                  r += '<td class="tdtimediff">' + cell + '</td>\r\n';
                }
                else
                if(e == (count - 2))
                {
                  r += '<td class="tdtimeresult">' + cell + '</td>\r\n';
                }
                else
                {
                  r += '<td class="'+ prefix_class + (e-2) +'">' + cell + '</td>\r\n';
                }
              }
            }
            break;
          case 2: // col 4 = club, col 5 to 10 = radios
            if (alternate==1)  // Do not display club, display 6 radios
            {
              for(var e in line)
              {
                cell = ((line[e] === '') ? "&nbsp;" : line[e]);
                cell = addFlag(cell, panelIndex);
                if((e > 1) && (e!=4))
                {
                  if(e == (count - 1))
                  {
                    r += '<td class="tdtimediff">' + cell + '</td>\r\n';
                  }
                  else
                  if(e == (count - 2))
                  {
                    r += '<td class="tdtimeresult">' + cell + '</td>\r\n';
                  }
                  else
                  {
                    r += '<td class="'+ prefix_class + (e-2) +'">' + cell + '</td>\r\n';
                  }
                }
              }
            }
            else // Display club and 4 radios
            {
              for(var e in line)
              {
                cell = ((line[e] === '') ? "&nbsp;" : line[e]);
                cell = addFlag(cell, panelIndex);
                if((e > 1) && (e!=9) && (e!=10))
                {
                  if(e == (count - 1))
                  {
                    r += '<td class="tdtimediff">' + cell + '</td>\r\n';
                  }
                  else
                  if(e == (count - 2))
                  {
                    r += '<td class="tdtimeresult">' + cell + '</td>\r\n';
                  }
                  else
                  {
                    r += '<td class="'+ prefix_class + (e-2) +'">' + cell + '</td>\r\n';
                  }
                }
              }
            }
            break;
          case 3:
            if(alternate == 1)
            {
              for(var e in line)
              {
                cell = ((line[e] === '') ? "&nbsp;" : line[e]);
                cell = addFlag(cell, panelIndex);
                if(e > 1)
                {
                  if ((e == 2) || (e == 3) || (e == 5) || (e == 6) || (e > 8)) // do not display club, display 2 radios
                  {
                    if(e == (count - 1))
                    {
                      r += '<td class="tdtimediff">' + line[e] + '</td>\r\n';
                    }
                    else
                    if(e == (count - 2))
                    {
                      r += '<td class="tdtimeresult">' + cell + '</td>\r\n';
                    }
                    else
                    if(e == 3)
                    {
                      r += '<td class="'+ prefix_class + (e-2) +'alt">' + cell + '</td>\r\n';
                    }
                    else
                    {
                      r += '<td class="'+ prefix_class + (e-2) +'">' + cell + '</td>\r\n';
                    }
                  }
                }
              }
            }
            else
            {
              for(var e in line)
              {
                cell = ((line[e] === '') ? "&nbsp;" : line[e]);
                cell = addFlag(cell, panelIndex);
                if(e > 1)
                {
                  if ((e<5)||(e>8)) // do not display radio
                  {
                    if(e == (count - 1))
                    {
                      r += '<td class="tdtimediff">' + line[e] + '</td>\r\n';
                    }
                    else
                    if(e == (count - 2))
                    {
                      r += '<td class="tdtimeresult">' + cell + '</td>\r\n';
                    }
                    else
                    {
                      r += '<td class="'+ prefix_class + (e-2) +'">' + cell + '</td>\r\n';
                    }
                  }
                }
              }
            }
            break;
          case 4:
            for(var e in line)
            {
              cell = ((line[e] === '') ? "&nbsp;" : line[e]);
              cell = addFlag(cell, panelIndex);
              if(e > 1)
              {
                if ((e<4)||(e>8)) // do not display club and radio
                {
                  if(e == (count - 1))
                  {
                    r += '<td class="tdtimediff">' + cell + '</td>\r\n';
                  }
                  else
                  if(e == (count - 2))
                  {
                    r += '<td class="tdtimeresult">' + cell + '</td>\r\n';
                  }
                  else
                  {
                    r += '<td class="'+ prefix_class + (e-2) +'">' + cell + '</td>\r\n';
                  }
                }
              }
            }
            break;
        }
        return r;
      }

	  function generateShowoCells(line, count, prefix_class,panelscount, tmcount, panelIndex)
      {
        r = '';
        switch (panelscount)
        {
          case 1:
            for(var e in line)
            {
              cell = ((line[e] === '') ? "&nbsp;" : line[e]);
              if((e == (count - 1)) && (tmcount > 1))
              {
                r += '<td class="tdtimeresult1showo">' + cell + '</td>\r\n';
              }
              else
              {
                r += '<td class="'+ prefix_class + (e) +'">' + cell + '</td>\r\n';
              }
            }
            break;
          case 2:
            ee = 0;
            for(var e in line)
            {
              cell = ((line[e] === '') ? "&nbsp;" : line[e]);
              if(e == 2)
              {
                //
              }
              else
              if((e == (count - 1)) && (tmcount > 1))
              {
                r += '<td class="tdtimeresult2showo">' + cell + '</td>\r\n';
              }
              else
              {
                r += '<td class="'+ prefix_class + (e) +'">' + cell + '</td>\r\n';
              }
            }
            break;
          case 3:
            for(var e in line)
            {
              cell = ((line[e] === '') ? "&nbsp;" : line[e]);
              if((e == (count - 1)) && (tmcount > 1))
              {
                r += '<td class="tdtimeresult3showo">' + cell + '</td>\r\n';
              }
              else
              {
                r += '<td class="'+ prefix_class + (e) +'">' + cell + '</td>\r\n';
              }
            }
            break;
          case 4:
            for(var e in line)
            {
              cell = ((line[e] === '') ? "&nbsp;" : line[e]);
              if((e == (count - 1)) && (tmcount > 1))
              {
                r += '<td class="tdtimeresult4showo">' + cell + '</td>\r\n';
              }
              else
              {
                r += '<td class="'+ prefix_class + (e) +'">' + cell + '</td>\r\n';
              }
            }
            break;
        }
        return r;
      }

      function generateOthersCells(line, prefix_class, panelscount, alternate, panelIndex)
      {
        r = '';
        //parametre alternate pour start: chronologique = 0, alphabetique = 1
        for(var e in line)
        {
          switch(panelscount)
          {
            case 3:
              if((alternate == 1) && (e > 2))
              {
                r += '<td class="'+ prefix_class + e +'">' + ((line[e] === '') ? "&nbsp;" : line[e]) + '</td>\r\n';
              }
              else
              if((alternate === 0) && (e < 3))
              {
                r += '<td class="'+ prefix_class + e +'">' + ((line[e] === '') ? "&nbsp;" : line[e]) + '</td>\r\n';
              }
            break;
            case 4:
              if((alternate == 1) && (e > 2))
              {
                r += '<td class="'+ prefix_class + e +'">' + ((line[e] === '') ? "&nbsp;" : line[e]) + '</td>\r\n';
              }
              else
              if((alternate === 0) && (e < 3))
              {
                r += '<td class="'+ prefix_class + e +'">' + ((line[e] === '') ? "&nbsp;" : line[e]) + '</td>\r\n';
              }
            break;
            default:
              r += '<td class="'+ prefix_class + e +'">' + ((line[e] === '') ? "&nbsp;" : line[e]) + '</td>\r\n';
            break;
          }
        }
        return r;
      }


      function generateCells(identifiant,line, count, prefix_class, pos_min_displayable, pos_max_displayable, cells_for_tm,tmcount,bFoundRadio,pos_recherche,panelscount, alternate, panelIndex)
      {
        if(identifiant === 'result')
        {
          r = generateResultCells(line, count, prefix_class,panelscount, alternate, panelIndex);
        }
        else
        if(identifiant === 'multistage')
        {
          r = generateMultistageCells(line, count, prefix_class,panelscount, alternate, panelIndex);
        }
        else
        if(identifiant === 'showo')
        {
          r = generateShowoCells(line, count, prefix_class,panelscount, tmcount, panelIndex);
        }
        else
        if(identifiant === 'relay')
        {
          r = generateRelayCells(line, prefix_class, pos_min_displayable, pos_max_displayable, cells_for_tm,tmcount,bFoundRadio,pos_recherche, alternate, panelIndex)
        }
        else
        {
          r = generateOthersCells(line, prefix_class, panelscount, alternate, panelIndex);
        }
        return r;
      }

      function generateRelaySummaryCells(line, prefix_class, pos_min_displayable, pos_max_displayable, cells_for_tm,tmcount,bFoundRadio,pos_recherche, alternate, panelIndex)
      {
        r = '';
        for(var e in line)
        {
          cell = ((line[e] === '') ? "&nbsp;" : line[e]);
          if(e > pos_min_displayable)
          {
            if(e < pos_max_displayable)
            {
              mycellnum = (e - pos_min_displayable + 1);
		if((cells_for_tm == 6)
                || ((cells_for_tm == 5) && ((mycellnum)%mymodulo != 0) && ((mycellnum)%mymodulo != 5))
                || ((cells_for_tm == 4) && ((mycellnum)%mymodulo != 0) && ((mycellnum)%mymodulo != 5) && ((mycellnum)%mymodulo != 4))
                || ((cells_for_tm == 3) && ((mycellnum)%mymodulo != 0) && ((mycellnum)%mymodulo != 5) && ((mycellnum)%mymodulo != 4) && ((mycellnum)%mymodulo != 3))
                || ((cells_for_tm == 2) && ((mycellnum)%mymodulo != 0) && ((mycellnum)%mymodulo != 5) && ((mycellnum)%mymodulo != 4) && ((mycellnum)%mymodulo != 3) && ((mycellnum)%mymodulo != 2))
                )
		{
                		r += '<td class="'+ prefix_class + mycellnum +'">' + cell + '</td>\r\n';
			}
		}
            }
          }

        if((tmcount == 8) || ((alternate == 1) && (tmcount == 6)))
        {
          if((alternate == 1) && (tmcount == 6))
            r += '<td class="radio0_6alt">&nbsp;</td>\r\n';
	  else
	  if(tmcount == 8)
	    r += '<td class="radio0_8">&nbsp;</td>\r\n';
          else
            r += '<td class="radio0">&nbsp;</td>\r\n';
          if(bFoundRadio)
          {
            r += '<td class="radio1">' + ((line[pos_recherche] === '') ? "&nbsp;" : line[pos_recherche]) + '</td>\r\n';
            r += '<td class="radio2">' + ((line[pos_recherche+1] === '') ? "&nbsp;" : line[pos_recherche+1]) + '</td>\r\n';
            r += '<td class="radio3">' + ((line[pos_recherche+2] === '') ? "&nbsp;" : line[pos_recherche+2]) + '</td>\r\n';
            r += '<td class="radio4">' + ((line[pos_recherche+3] === '') ? "&nbsp;" : line[pos_recherche+3]) + '</td>\r\n';
          }
          else
          {
            r += '<td class="radio1">&nbsp;</td>\r\n';
            r += '<td class="radio2">&nbsp;</td>\r\n';
            r += '<td class="radio3">&nbsp;</td>\r\n';
            r += '<td class="radio4">&nbsp;</td>\r\n';
          }
        }

        return r;
      }

      function generateResultSummaryCells(line, count, prefix_class, panelscount, panelIndex)
      {
        r = '';
        for(var e in line)
        {
          cell = ((line[e] === '') ? "&nbsp;" : line[e]);
          if(e > 1)
          {
            if((e == (count - 1)) && (panelscount < 3))
            {
              r += '<td class="tdsumtimediff">' + cell + '</td>\r\n';
            }
            else
            if(e == (count - 2))
            {
              r += '<td class="tdsumtimeresult">' + cell + '</td>\r\n';
            }
            else
            if((e == 2) || (e == 3) || (e == 4))
            {
              r += '<td class="'+ prefix_class + (e-2) +'">' + cell + '</td>\r\n';
            }
          }
        }

        return r;
      }

      function generateSummaryCells(identifiant,line, count, prefix_class, pos_min_displayable, pos_max_displayable, cells_for_tm,tmcount,bFoundRadio,pos_recherche,panelscount, alternate, panelIndex)
      {
        if(identifiant === 'result')
        {
          r = generateResultSummaryCells(line, count, prefix_class,panelscount, panelIndex);
        }
        else
        if(identifiant === 'relay')
        {
          r = generateRelaySummaryCells(line, prefix_class, pos_min_displayable, pos_max_displayable, cells_for_tm,tmcount,bFoundRadio,pos_recherche, alternate, panelIndex);
        }
        return r;
      }

      function initVarForRelay(tmcount, alternate)
      {
          switch(tmcount)
          {
            case 1:
              count = 13; // names excluded
                pos_rank = 4;
                pos_team_name = 5;
              pos_tm_rank = Array(11);
              pos_tm_name = Array(13);
		pos_tm_finish = Array(8);
                pos_min_displayable = 5;
                pos_max_displayable = count;
                cells_for_tm = 6;
                tm_colspan = 4;
            break;
            case 2:
              count = 21; // names excluded
                pos_rank = 5;
                pos_team_name = 6;
              pos_tm_rank = Array(12, 19);
              pos_tm_name = Array(21, 22);
		pos_tm_finish = Array(8,15);
                pos_min_displayable = 6;
                pos_max_displayable = count;
                cells_for_tm = 6;
                tm_colspan = 4;
            break;
            case 3:
              count = 29; // names excluded
                pos_rank = 6;
                pos_team_name = 7;
              pos_tm_rank = Array(13, 20, 27);
              pos_tm_name = Array(29, 30, 31);
		pos_tm_finish = Array(8,15,22);
                pos_min_displayable = 7;
                pos_max_displayable = count;
                cells_for_tm = 6;
                tm_colspan = 4;
            break;
            case 4:
              count = 37; // names excluded
                pos_rank = 7;
                pos_team_name = 8;
              pos_tm_rank = Array(14, 21, 28, 35);
              pos_tm_name = Array(37, 38, 39, 40);
		pos_tm_finish = Array(8,15,22,29);
                pos_min_displayable = 8;
                pos_max_displayable = count;
                cells_for_tm = 5;
                tm_colspan = 4;
            break;
            case 5:
              count = 45; // names excluded
                pos_rank = 8;
                pos_team_name = 9;
              pos_tm_rank = Array(15, 22, 29, 36, 43);
              pos_tm_name = Array(45, 46, 47, 48, 49);
		pos_tm_finish = Array(8,15,22,29,36);
                pos_min_displayable = 9;
                pos_max_displayable = count;
                cells_for_tm = 4;
                tm_colspan = 3;
            break;
            case 6:
              count = 53; // names excluded
                pos_rank = 9;
                pos_team_name = 10;
              pos_tm_rank = Array(16, 23, 30, 37, 44, 51);
              pos_tm_name = Array(53, 54, 55, 56, 57, 58);
		pos_tm_finish = Array(8,15,22,29,36,43);
                pos_min_displayable = 10;
                pos_max_displayable = count;

				if(alternate == 1)
				{
					cells_for_tm = 2;
					tm_colspan = 1;
				}
				else
				{
					cells_for_tm = 3;
					tm_colspan = 2;
				}
            break;
            case 7:
              count = 61; // names excluded
                pos_rank = 10;
                pos_team_name = 11;
              pos_tm_rank = Array(17, 24, 31, 38, 45, 52, 59);
              pos_tm_name = Array(61, 62, 63, 64, 65, 66, 67);
		pos_tm_finish = Array(8,15,22,29,36,43,50);
                pos_min_displayable = 11;
                pos_max_displayable = count;
                cells_for_tm = 3;
                tm_colspan = 2;
            break;
            case 8:
		count = 69; // names excluded
                pos_rank = 11;
                pos_team_name = 12;
                pos_tm_rank = Array(18, 25, 32, 39, 46, 53, 60, 67);
                pos_tm_name = Array(69, 70, 71, 72, 73, 74, 75, 76);
		pos_tm_finish = Array(8,15,22,29,36,43,50,57);
                pos_min_displayable = 12;
                pos_max_displayable = count;
                cells_for_tm = 2;
                tm_colspan = 1;
            break;
            case 9:
              count = 77; // names excluded
                pos_rank = 12;
                pos_team_name = 13;
              pos_tm_rank = Array(19, 26, 33, 40, 47, 54, 61, 68, 75);
              pos_tm_name = Array(77, 78, 79, 80, 81, 82, 83, 84, 85);
		pos_tm_finish = Array(8,15,22,29,36,43,50,57,64);
                pos_min_displayable = 13;
                pos_max_displayable = count;
                cells_for_tm = 2;
                tm_colspan = 1;
            break;
            case 10:
              count = 85; // names excluded
                pos_rank = 13;
                pos_team_name = 14;
              pos_tm_rank = Array(20, 27, 34, 41, 48, 55, 62, 69, 76, 83);
              pos_tm_name = Array(85, 86, 87, 88, 89, 90, 91, 92, 93, 94);
		pos_tm_finish = Array(8,15,22,29,36,43,50,57,64,71);
                pos_min_displayable = 14;
                pos_max_displayable = count;
                cells_for_tm = 2;
                tm_colspan = 1;
            break;
            default:
                count = 26; // names excluded
                pos_rank = 6;
                pos_team_name = 7;
                pos_tm_rank = Array(12, 18, 24);
                pos_tm_name = Array(26, 27, 28);
                pos_min_displayable = 7;
                pos_max_displayable = count;
                cells_for_tm = 6;
                tm_colspan = 4;
            break;
          } // end switch
          return [count, pos_rank, pos_team_name, pos_tm_rank, pos_tm_name, pos_min_displayable, pos_max_displayable, cells_for_tm, tm_colspan];
      }


      function ConvertToSummaryHtmlTableRow(panelIndex, identifiant, startline, tmcount, panelscount, num1, num2, alternate)
      {
<?php
  print "var relay_header_text = '".MyGetText(94)."';\n"; // Relay n
  print "var relay_after_header_text = '".MyGetText(95)."';\n"; // After Relay n
?>
        var r = "";
        var ee = 0;
        var bUpdateNeeded = false;
        var pos_min_displayable = 0;
        var pos_max_displayable = 0;
        var cells_for_tm = 0;
        var bFoundRadio = false;
        var pos_recherche = 0;

        var prefix_class = 'td_'+panelscount + '_';
        var position = startline - 1;
        if(position <= 0)
          position = 0;
        var count = 5;
        var cells_count = count;
        if(tmcount === undefined)
          tmcount = 2;

        var isSummary = false;
        if(identifiant === 'resultsummary')
        {
          identifiant = 'result';
          isSummary = true;
        }

        if(identifiant === 'relaysummary')
        {
          identifiant = 'relay';
          isSummary = true;
        }

        if(identifiant === 'result')
        {
          prefix_class = 'tdsum_'+panelscount + '_';
          count = 11;
        }
        else
        if(identifiant === 'relay')
        {
          prefix_class = 'tdsum' + tmcount + 'relay';
          relayTable = initVarForRelay(tmcount, alternate);
          count = relayTable[0];
          pos_rank = relayTable[1];
          pos_team_name = relayTable[2];
          pos_tm_rank = relayTable[3];
          pos_tm_name = relayTable[4];
          pos_min_displayable = relayTable[5];
          pos_max_displayable = relayTable[6];
          cells_for_tm = relayTable[7];
          tm_colspan = relayTable[8];
          cells_count = tmcount * cells_for_tm + 2;
        }

        //------ display tabs over result table ----------

        r += '<table class="fixedTable" cellspacing="0" cellpadding="0">\r\n';
        // affichage du header
        r += '<thead id="fixedHeader' + panelIndex + '" class="fixedHeader">\r\n';
        r += '<tr class="normalRow">\r\n';

        var c;

        var txt_nb = '';
        var length=0;
        if (tableUpdated[panelIndex])
        {
          if(data2Array[panelIndex][num1])
            length = data2Array[panelIndex][num1].length;
          else
            length = 0;
        }
		if (typeof (phpnumbercls[phpcls[panelIndex][num1]]) !== 'undefined')
			txt_nb = ' / ' + phpnumbercls[phpcls[panelIndex][num1]];
		  else
			txt_nb = length;

		r += '<th class="activeOnglet">' + phpTitle[panelIndex][num1] + ' <span class="number_class">' + txt_nb + '</span></th>\r\n';

        r += '</tr>\r\n';
        r += '</thead>\r\n';


        //----------- display table header for relays ------------------------
        var nf = '';

        if (length > 0)
        {
          r +='<tbody class="scrollContent">';

          // lignes fixes
          var endPosition = phpfixedlines[panelIndex] + startline - 1;
          if (length - startline + 1 < (phpfixedlines[panelIndex] + phpscrolledlines[panelIndex])) // if (length < (phpfixedlines[panelIndex] + phpscrolledlines[panelIndex]))
          {
              endPosition = length;
          }
          var line = eval(data2Array[panelIndex][num1][0]);
          //count = line.length;

          if(identifiant === 'relay')
          {
            r += '<tr>\r\n';
            r += '<th class="entete_sumrelay" rowspan="2">&nbsp;</th>\r\n';
            r += '<th class="entete_sumrelay" rowspan="2">&nbsp;</th>\r\n';
            r += '<th class="entete_sumrelay" colspan="' + tm_colspan + '">'+relay_header_text+' 1</th>\r\n';

            r += '<th class="entete_sumrelay" colspan="' + (cells_for_tm - tm_colspan) + '" rowspan="2">'+relay_after_header_text+'1</th>\r\n';
            if(tmcount > 1)
            {
              for(ind=2;ind<=tmcount;ind++)
              {
                r += '<th class="entete_sumrelay" colspan="' + tm_colspan + '">'+relay_header_text+' ' + ind + '</th>\r\n';
                r += '<th class="entete_sumrelay" colspan="' + (cells_for_tm - tm_colspan) + '" rowspan="2">'+relay_after_header_text+ ind + '</th>\r\n';
              }
              if((tmcount == 8) || ((alternate == 1) && (tmcount == 6)))
              {
                if((alternate == 1) && (tmcount == 6))
                  r += '<th class="entete_radio0_6alt" rowspan="2">&nbsp;</th>\r\n';
                else if(tmcount == 8)
                  r += '<th class="entete_radio0_8" rowspan="2">&nbsp;</th>\r\n';
		else
                  r += '<th class="entete_radio0" rowspan="2">&nbsp;</th>\r\n';
                r += '<th class="entete_radio1" rowspan="2">Radio1</th>\r\n';
                r += '<th class="entete_radio2" rowspan="2">Radio2</th>\r\n';
                r += '<th class="entete_radio3" rowspan="2">Radio3</th>\r\n';
                r += '<th class="entete_radio4" rowspan="2">Radio4</th>\r\n';
              }
            }
            r += '</tr>\r\n';
            r += '<tr>\r\n';
            if(tm_colspan > 1)
              r += '<th class="entete_relay_radio">Radio1</th>\r\n';
            if(tm_colspan > 2)
              r += '<th class="entete_relay_radio">Radio2</th>\r\n';
            if(tm_colspan > 3)
              r += '<th class="entete_relay_radio">Radio3</th>\r\n';
            r += '<th class="entete_relay_radio">Finish</th>\r\n';
            if(tmcount > 1)
            {
              for(ind=2;ind<=tmcount;ind++)
              {
                if(tm_colspan > 1)
                  r += '<th class="entete_relay_radio">Radio1</th>\r\n';
                if(tm_colspan > 2)
                  r += '<th class="entete_relay_radio">Radio2</th>\r\n';
                if(tm_colspan > 3)
                  r += '<th class="entete_relay_radio">Radio3</th>\r\n';
                r += '<th class="entete_relay_radio">Finish</th>\r\n';
              }
            }

            r += '</tr>\r\n';
          } // end relay


          //---------------- Fixed part -------------------------------
          while((position < endPosition) && (position < length))
          {
            line = eval(data2Array[panelIndex][num1][position++]);
            if(line != null)
            {
              if(identifiant === 'result')
              {
                if(line[0] < 1)
                {
                  nf = ' nonfini';
                }
                else
                {
                  nf = '';
                }
                if(line[1] > nowtime - (60 * phpupdateduration[panelIndex]))
                {
                  nf += ' updated';
                }
              }

              var cl = ((position % 2) ? 'alternateRow' : 'normalRow');

              r += '<tr class="' + cl + nf + '">\r\n';
              if(identifiant === 'relay')
              {
                r += '<td class="'+ prefix_class + '0' +'">' + ((line[pos_rank] === '') ? "&nbsp;" : line[pos_rank]) + '</td>\r\n';
                r += '<td class="'+ prefix_class + '1' +'">' + ((line[pos_team_name] === '') ? "&nbsp;" : line[pos_team_name]) + '</td>\r\n';

                if((tmcount == 8) || ((alternate == 1) && (tmcount == 6)))
                {
                  iFound = tmcount;
                  bFoundRadio = false;
                  pos_recherche = 0;
                  while((iFound > 0) && (bFoundRadio == false))
                  {
                    pos_recherche = pos_min_displayable + 1 + 7 * (iFound-1);
                    if((line[pos_recherche] != '') || (line[pos_recherche+1] != ''))
                    {
                      bFoundRadio = true;
                    }
                    else
                    {
                      iFound--;
                    }
                  }
                }

              }
              r += generateSummaryCells(identifiant,line, count, prefix_class, pos_min_displayable, pos_max_displayable, cells_for_tm,tmcount,bFoundRadio,pos_recherche,panelscount,alternate, panelIndex);
              r += '</tr>\r\n';
            }
          } // end fixed part

        }
        else // length=0 => empty lines ------------------
        {
          r +='<tbody class="scrollContent">';
          position = 0;
          nf = '';
          if(identifiant === 'result')
          {
            count = count;
          }
          else
          if(identifiant === 'relay')
          {
            count = cells_count;
          }
          while(position < (phpfixedlines[panelIndex]))
          {
            position++;
            var cl = ((position % 2) ? 'alternateRow' : 'normalRow');
            r += '<tr class="' + cl + nf + '">\r\n';
            var emptyarr = Array();
            var maxempty = count;
            if(identifiant == 'relay')
              maxempty = 2 + count * tmcount;
            for(var inc=0;inc<maxempty;inc++)
              emptyarr.push("&nbsp;");
            if(identifiant === 'relay')
            {  // first of two relay lines

              r += '<td rowspan="2" class="'+ prefix_class + '0' +'">' + "&nbsp;" + '</td>\r\n';
              r += '<td rowspan="2" class="'+ prefix_class + '1' +'">' + "&nbsp;" + '</td>\r\n';
              for(ind=0;ind<tmcount;ind++)
              {
                r += '<td colspan="' + cells_for_tm + '" class="relay_tm_name">' +  "&nbsp;" + '</td>\r\n';
              }
              if((tmcount == 8) || ((alternate == 1) && (tmcount == 6)))
              {
                if((alternate == 1) && (tmcount == 6))
                  r += '<td class="radio0_6alt">&nbsp;</td>\r\n';
		            else if(tmcount == 8)
                  r += '<td class="radio0_8">&nbsp;</td>\r\n';
		else
                  r += '<td class="radio0">&nbsp;</td>\r\n';
                r += '<td colspan="4" class="radio_tm_name">&nbsp;</td>\r\n';
              }
              r += '</tr>\r\n';
              r += '<tr class="' + cl + nf + '">\r\n';
            }  // end first relay line
            r += generateSummaryCells(identifiant,emptyarr, count, prefix_class, pos_min_displayable, pos_max_displayable, cells_for_tm,tmcount,bFoundRadio,pos_recherche,panelscount,alternate, panelIndex);
            r += '</tr>\r\n';
          }
          r += "</tbody>\r\n";
          r += '</table>\r\n';
          after_decrement_counter[panelIndex] = 0;
        }

        after_decrement_counter[panelIndex]--;
        if(after_decrement_counter[panelIndex] <= 0)
        {
          displayScrollIndex[panelIndex] = phpfixedlines[panelIndex] + startline - 1;
          categorieIndex[panelIndex] = (categorieIndex[panelIndex] + 1) % phpcls[panelIndex].length;
          tableUpdated[panelIndex] = false;

          if(identifiant === 'result')
          {
              updateTable(panelIndex);
          }
          else
          if(identifiant === 'relay')
          {
            updateRelay();
          }

          after_decrement_counter[panelIndex] = phpscrollaftertime[panelIndex] / phpscrolltime[panelIndex];
        }

        return r;
      } // summaryhtmltablerow

      function ConvertToNiceHtmlTableRow(panelIndex, identifiant, startline, tmcount, panelscount, alternate)
      {

<?php
  print "var relay_header_text = '".MyGetText(94)."';\n"; // Relay n
  print "var relay_after_header_text = '".MyGetText(95)."';\n"; // After Relay n
?>
        var r = "";
        var ee = 0;
        var bUpdateNeeded = false;
        var pos_min_displayable = 0;
        var pos_max_displayable = 0;
        var cells_for_tm = 0;
        var bFoundRadio = false;
        var pos_recherche = 0;

        var prefix_class = 'td';
        var position = startline - 1;
        if(position <= 0)
          position = 0;
        var count = 5;
        var cells_count = count;
        if(tmcount === undefined)
          tmcount = 2;
        if(identifiant === 'start')
        {
          prefix_class = 'tdi_'+panelscount + '_';
          count = 6;
        }
        else
        if(identifiant === 'result')
        {
          prefix_class = 'td_'+panelscount + '_';
          count = 7+nbradio;
        }
        else
        if(identifiant === 'multistage')
        {
          prefix_class = 'td_m'+panelscount + '_';
          count = 7+nbradio;
        }
        else
        if(identifiant === 'showo')
        {
          prefix_class = 'td_showo'+panelscount + '_';
          count = 4 + 3 * tmcount;
        }
        else
        if(identifiant === 'relay')
        {
          prefix_class = 'td' + tmcount + 'relay';
          relayTable = initVarForRelay(tmcount, alternate);
          count = relayTable[0];
          pos_rank = relayTable[1];
          pos_team_name = relayTable[2];
          pos_tm_rank = relayTable[3];
          pos_tm_name = relayTable[4];
          pos_min_displayable = relayTable[5];
          pos_max_displayable = relayTable[6];
          cells_for_tm = relayTable[7];
          tm_colspan = relayTable[8];
          cells_count = tmcount * cells_for_tm + 2;
        }

        //------ display tabs over result table ----------

        r += '<table class="fixedTable" cellspacing="0" cellpadding="0">\r\n';
        // affichage du header
        r += '<thead id="fixedHeader' + panelIndex + '" class="fixedHeader">\r\n';
        r += '<tr class="normalRow">\r\n';

        var c;

        var txt_nb = '';
        var length=0;
        if (tableUpdated[panelIndex])
        {
          if(dataArray[panelIndex])
            length = dataArray[panelIndex].length;
          else
            length = 0;
        }

        for(c=0;c<phpTitle[panelIndex].length;c++)
        {
          if(categorieIndex[panelIndex] == c)
          {
            if((identifiant === 'result') || (identifiant === 'multistage'))
            {
              if (typeof (phpnumbercls[phpcls[panelIndex][c]]) !== 'undefined')
                txt_nb = length + ' / ' + phpnumbercls[phpcls[panelIndex][c]];
              else
                txt_nb = length;
            }
            r += '<th class="activeOnglet">' + phpTitle[panelIndex][c] + ' <span class="number_class">' + txt_nb + '</span></th>\r\n';
          }
          else
          {
            if((identifiant === 'result') || (identifiant === 'relay') || (identifiant === 'multistage'))
            {
              r += '<th class="inactiveOnglet">' + phpTitle[panelIndex][c] + '</th>\r\n';
            }
            else
            if(identifiant === 'start')
            {
              r += '<th class="inactiveOnglet">' + phpTitle[panelIndex][c] + ' <span class="number_class">(' + phpnumbercls[phpcls[panelIndex][c]] + ')</span>' + '</th>\r\n';
            }
          }
        } // end for
        r += '</tr>\r\n';
        r += '</thead>\r\n';


        //----------- display table header for relays ------------------------
        var nf = '';

        if (length > 0)
        {
          r +='<tbody class="scrollContent">';

          // lignes fixes
          var endPosition = phpfixedlines[panelIndex] + startline - 1;
          if (length - startline + 1 < (phpfixedlines[panelIndex] + phpscrolledlines[panelIndex]))
          {
              endPosition = length;
          }
          var line = eval(dataArray[panelIndex][0]);

          if(identifiant === 'relay')
          {
            r += '<tr>\r\n';
            r += '<th class="entete_relay" rowspan="2">&nbsp;</th>\r\n';
            r += '<th class="entete_relay" rowspan="2">&nbsp;</th>\r\n';
            r += '<th class="entete_relay" colspan="' + tm_colspan + '">'+relay_header_text+' 1</th>\r\n';

            r += '<th class="entete_relay" colspan="' + (cells_for_tm - tm_colspan) + '" rowspan="2">'+relay_after_header_text+'1</th>\r\n';
            if(tmcount > 1)
            {
              for(ind=2;ind<=tmcount;ind++)
              {
                r += '<th class="entete_relay" colspan="' + tm_colspan + '">'+relay_header_text+' ' + ind + '</th>\r\n';
                r += '<th class="entete_relay" colspan="' + (cells_for_tm - tm_colspan) + '" rowspan="2">'+relay_after_header_text+ ind + '</th>\r\n';
              }
              if((tmcount == 8) || ((alternate == 1) && (tmcount == 6)))
              {
                if((alternate == 1) && (tmcount == 6))
                  r += '<th class="entete_radio0_6alt" rowspan="2">&nbsp;</th>\r\n';
		            else if(tmcount == 8)
                  r += '<th class="entete_radio0_8" rowspan="2">&nbsp;</th>\r\n';
		else
                  r += '<th class="entete_radio0" rowspan="2">&nbsp;</th>\r\n';
                r += '<th class="entete_radio1" rowspan="2">Radio1</th>\r\n';
                r += '<th class="entete_radio2" rowspan="2">Radio2</th>\r\n';
                r += '<th class="entete_radio3" rowspan="2">Radio3</th>\r\n';
                r += '<th class="entete_radio4" rowspan="2">Radio4</th>\r\n';
              }
            }
            r += '</tr>\r\n';
            r += '<tr>\r\n';
            if(tm_colspan > 1)
              r += '<th class="entete_relay_radio">Radio1</th>\r\n';
            if(tm_colspan > 2)
              r += '<th class="entete_relay_radio">Radio2</th>\r\n';
            if(tm_colspan > 3)
              r += '<th class="entete_relay_radio">Radio3</th>\r\n';
            r += '<th class="entete_relay_radio">Finish</th>\r\n';
            if(tmcount > 1)
            {
              for(ind=2;ind<=tmcount;ind++)
              {
                if(tm_colspan > 1)
                  r += '<th class="entete_relay_radio">Radio1</th>\r\n';
                if(tm_colspan > 2)
                  r += '<th class="entete_relay_radio">Radio2</th>\r\n';
                if(tm_colspan > 3)
                  r += '<th class="entete_relay_radio">Radio3</th>\r\n';
                r += '<th class="entete_relay_radio">Finish</th>\r\n';
              }
            }

            r += '</tr>\r\n';
          } // end relay
          else
          if(identifiant === 'showo')
          {
            r += '<tr>\r\n';
            r += '<th class="entete_showo" rowspan="2">&nbsp;</th>\r\n'; // classement
            r += '<th class="entete_showo" rowspan="2">&nbsp;</th>\r\n'; // nom
            if(panelscount == 1)
              r += '<th class="entete_showo" rowspan="2">&nbsp;</th>\r\n'; // club
            if(tmcount == 1)
            {
              r += '<th class="entete_showo" colspan="3">&nbsp;</th>\r\n'; // tempsN
            }
            else
            {
              for(ind=0;ind<tmcount;ind++)
              {
                r += '<th class="entete_showo" colspan="3">' + (ind+1) + '</th>\r\n'; // tempsN
              }
            }
            if(tmcount == 1)
              r += '<th class="entete_showo" rowspan="2">Diff</th>\r\n'; // temps total avec penalty ou ecart
            else
              r += '<th class="entete_showo" rowspan="2">Total</th>\r\n'; // temps total avec penalty ou ecart
            r += '</tr>\r\n';

            r += '<tr>\r\n';
            if(tmcount == 1)
            {
                r += '<th class="entete_showo">Chrono</th>\r\n'; // tempsN
                r += '<th class="entete_showo">P</th>\r\n'; // tempsN
                r += '<th class="entete_showo">Temps</th>\r\n'; // tempsN
            }
            else
            {
              for(ind=0;ind<tmcount;ind++)
              {
                r += '<th class="entete_showo">Tps</th>\r\n'; // tempsN
                r += '<th class="entete_showo">P</th>\r\n'; // tempsN
                r += '<th class="entete_showo">Res</th>\r\n'; // tempsN
              }
            }
            r += '</tr>\r\n';
          } // end showo


          //---------------- Fixed part -------------------------------
          while((position < endPosition) && (position < length))
          {
            line = eval(dataArray[panelIndex][position++]);
            if(line != null)
            {
              if((identifiant === 'result') || (identifiant === 'multistage'))
              {
                if(line[0] < 1)
                {
                  nf = ' nonfini';
                }
                else
                {
                  nf = '';
                }
                if(line[1] > nowtime - (60 * phpupdateduration[panelIndex]))
                {
                  nf += ' updated';
                }
              }

              var cl = ((position % 2) ? 'alternateRow' : 'normalRow');
              if(identifiant === 'relay')
              {  // first of two relay lines
                if(line[1] > nowtime - (60 * phpupdateduration[panelIndex]))
                {
                  nf = ' updated';
                }
                else
                {
                  nf = '';
                }
                r += '<tr class="' + cl + nf + '">\r\n';
                r += '<td rowspan="2" class="'+ prefix_class + '0' +'">' + ((line[pos_rank] === '') ? "&nbsp;" : line[pos_rank]) + '</td>\r\n';
                r += '<td rowspan="2" class="'+ prefix_class + '1' +'">' + ((line[pos_team_name] === '') ? "&nbsp;" : line[pos_team_name]) + '</td>\r\n';
                for(ind=0;ind<tmcount;ind++)
                {
                  r += '<td colspan="' + cells_for_tm + '" class="relay_tm_name">' + ((line[pos_tm_rank[ind]].length) ? '(' + line[pos_tm_rank[ind]]+ ') ' : '') + ((line[pos_tm_name[ind]] === '') ? "&nbsp;" : line[pos_tm_name[ind]]) + '</td>\r\n';
                }
                if((tmcount == 8) || ((alternate == 1) && (tmcount == 6)))
                {
                  iFound = tmcount;
                  bFoundRadio = false;
                  pos_recherche = 0;
                  while((iFound > 0) && (bFoundRadio == false))
                  {
                    pos_recherche = pos_min_displayable + 1 + 7 * (iFound-1);
                    if((line[pos_recherche] != '') || (line[pos_recherche+1] != ''))
                    {
                      bFoundRadio = true;
                    }
                    else
                    {
                      iFound--;
                    }
                  }

                  if((alternate == 1) && (tmcount == 6))
                    r += '<td class="radio0_6alt">&nbsp;</td>\r\n';
                  else
                  if(tmcount == 8)
                    r += '<td class="radio0_8">&nbsp;</td>\r\n';
                  else
                    r += '<td class="radio0">&nbsp;</td>\r\n';
                  if(bFoundRadio)
                  {
                    r += '<td colspan="4" class="radio_tm_name">' + ((line[pos_tm_name[iFound-1]] === '') ? "&nbsp;" : line[pos_tm_name[iFound-1]]) + '</td>\r\n';
                  }
                  else
                  {
                    r += '<td colspan="4" class="radio_tm_name">&nbsp;</td>\r\n';
                  }
                }


                r += '</tr>\r\n';
              }  // end first relay line

              r += '<tr class="' + cl + nf + '">\r\n';
              r += generateCells(identifiant,line, count, prefix_class, pos_min_displayable, pos_max_displayable, cells_for_tm,tmcount,bFoundRadio,pos_recherche,panelscount, alternate, panelIndex);
              r += '</tr>\r\n';
            }
          } // end fixed part

          //---------------------------- scrolling part -------------------
          if (length - startline + 1 >= (phpfixedlines[panelIndex] + phpscrolledlines[panelIndex]))
          {
            if((identifiant === 'result') || (identifiant === 'relay') || (identifiant === 'multistage'))
            {
              r += "</tbody>\r\n";
              r += '</table>\r\n';

              r += '<hr />';
              r += '<table class="scrollTable" cellspacing="0" cellpadding="0">\r\n';
              r += '<tbody class="scrollContent">';
            }

            var startPosition = displayScrollIndex[panelIndex];
            var endPosition = startPosition + phpscrolledlines[panelIndex];
            nf = '';

            for(position = startPosition; position < endPosition; position++)
            {
              if (position < length)
              {
                var line = eval(dataArray[panelIndex][position]);
                if(line != null)
                {
                  var cl = ((position % 2) ? 'alternateRow' : 'normalRow');

                  if((identifiant === 'result') || (identifiant === 'multistage'))
                  {
                    if(line[0] < 1)
                    {
                      nf = ' nonfini';
                    }
                    else
                    {
                      nf = '';
                    }
                    if(line[1] > nowtime - (60 * phpupdateduration[panelIndex]))
                    {
                      nf += ' updated';
                    }
                  }
                  else
                  if(identifiant === 'relay')
                  {  //----- first of two lines -----
                    if(line[1] > nowtime - (60 * phpupdateduration[panelIndex]))
                    {
                      nf = ' updated';
                    }
                    else
                    {
                      nf = '';
                    }

                    r += '<tr class="' + cl + nf + '">\r\n';
                    r += '<td rowspan="2" class="'+ prefix_class + '0' +'">' + ((line[pos_rank] === '') ? "&nbsp;" : line[pos_rank]) + '</td>\r\n';
                    r += '<td rowspan="2" class="'+ prefix_class + '1' +'">' + ((line[pos_team_name] === '') ? "&nbsp;" : line[pos_team_name]) + '</td>\r\n';
                    for(ind=0;ind<tmcount;ind++)
                    {
                      r += '<td colspan="' + cells_for_tm + '" class="relay_tm_name">' + ((line[pos_tm_rank[ind]].length) ? '(' + line[pos_tm_rank[ind]]+ ') ' : '') + ((line[pos_tm_name[ind]] === '') ? "&nbsp;" : line[pos_tm_name[ind]]) + '</td>\r\n';
                    }

                    //------ when team member count = 8, radios are on the right
                    if((tmcount == 8) || ((alternate == 1) && (tmcount == 6)))
                    {
                      iFound = tmcount;
                      bFoundRadio = false;
                      pos_recherche = 0;
                      while((iFound > 0) && (bFoundRadio == false))
                      {
                        pos_recherche = pos_min_displayable + 1 + 7 * (iFound-1);
                        if((line[pos_recherche] != '') || (line[pos_recherche+1] != ''))
                        {
                          bFoundRadio = true;
                        }
                        else
                        {
                          iFound--;
                        }
                      }
                      if((alternate == 1) && (tmcount == 6))
                        r += '<td class="radio0_6alt">&nbsp;</td>\r\n';
                  else
                  if(tmcount == 8)
                    r += '<td class="radio0_8">&nbsp;</td>\r\n';
                  else
                        r += '<td class="radio0">&nbsp;</td>\r\n';
                      if(bFoundRadio)
                      {
                        r += '<td colspan="4" class="radio_tm_name">' + ((line[pos_tm_name[iFound-1]] === '') ? "&nbsp;" : line[pos_tm_name[iFound-1]]) + '</td>\r\n';
                      }
                      else
                      {
                        r += '<td colspan="4" class="radio_tm_name">&nbsp;</td>\r\n';
                      }
                    }
                    r += '</tr>\r\n';


                  } // end relay

                  r += '<tr class="' + cl + nf + '">\r\n';
                  r += generateCells(identifiant,line, count, prefix_class, pos_min_displayable, pos_max_displayable, cells_for_tm,tmcount,bFoundRadio,pos_recherche,panelscount,alternate, panelIndex);
                  r += '</tr>\r\n';
                } // end line!=null
              }
              else // position >= end ==> empty lines
              {
                var cl = ((position % 2) ? 'alternateRow' : 'normalRow');
                nf = '';
                r += '<tr class="' + cl + nf + '">\r\n';
                if(identifiant === 'result')
                {
                  count1 = count;
                }
                else
                if(identifiant === 'multistage')
                {
	                count1 = count + 2;
                }
                else
                if(identifiant === 'relay')
                {
                  count1 = cells_count;
                }
                else
                {
                  count1 = count;
                }
                var emptyarr = Array();
                var maxempty = count;
                if(identifiant == 'relay')
                  maxempty = 2 + count * tmcount;
                else
                if(identifiant === 'multistage')
                  maxempty = 2 + count;
                for(var inc=0;inc<maxempty;inc++)
                  emptyarr.push("&nbsp;");
                if(identifiant === 'relay')
                {  // first of two relay lines

                  r += '<td rowspan="2" class="'+ prefix_class + '0' +'">' + "&nbsp;" + '</td>\r\n';
                  r += '<td rowspan="2" class="'+ prefix_class + '1' +'">' + "&nbsp;" + '</td>\r\n';
                  for(ind=0;ind<tmcount;ind++)
                  {
                    r += '<td colspan="' + cells_for_tm + '" class="relay_tm_name">' +  "&nbsp;" + '</td>\r\n';
                  }
                  if((tmcount == 8) || ((alternate == 1) && (tmcount == 6)))
                  {
                    if((alternate == 1) && (tmcount == 6))
                      r += '<td class="radio0_6alt">&nbsp;</td>\r\n';
                    else
                  if(tmcount == 8)
                    r += '<td class="radio0_8">&nbsp;</td>\r\n';
                  else
                      r += '<td class="radio0">&nbsp;</td>\r\n';
                    r += '<td colspan="4" class="radio_tm_name">&nbsp;</td>\r\n';
                  }
                  r += '</tr>\r\n';
                  r += '<tr class="' + cl + nf + '">\r\n';
                }  // end first relay line
                r += generateCells(identifiant,emptyarr, count1, prefix_class, pos_min_displayable, pos_max_displayable, cells_for_tm,tmcount,bFoundRadio,pos_recherche,panelscount,alternate, panelIndex);
                r += '</tr>\r\n';
              }
            } // end for

            if(before_decrement_counter[panelIndex] <= 0)
            {
              displayScrollIndex[panelIndex]++;
            }
            else
            {
              before_decrement_counter[panelIndex]--;
            }
            if (displayScrollIndex[panelIndex] > length - (phpscrolledlines[panelIndex]-2))
            {
              before_decrement_counter[panelIndex] = phpscrollbeforetime[panelIndex] / phpscrolltime[panelIndex];
              bUpdateNeeded = true;
            }
            r += "</tbody>\r\n";
            r += '</table>\r\n';
          } // end of scrolling part
          else
          {
            bUpdateNeeded = true;
            nf = '';
            if(identifiant === 'result')
            {
              count = count;
            }
            else
            if(identifiant === 'multistage')
            {
	            count = count + 2;
            }
            else
            if(identifiant === 'relay')
            {
              count = cells_count;
            }
            else
            if(identifiant === 'showo')
            {
              count = count;
            }
            while(position < startline - 1 + (phpfixedlines[panelIndex] + phpscrolledlines[panelIndex]))
            {
              position++;
              var cl = ((position % 2) ? 'alternateRow' : 'normalRow');
              r += '<tr class="' + cl + nf + '">\r\n';
              var emptyarr = Array();
              var maxempty = count;
              if(identifiant == 'relay')
                maxempty = 2 + count * tmcount;
              for(var inc=0;inc<maxempty;inc++)
                emptyarr.push("&nbsp;");
              if(identifiant === 'relay')
              {  // first of two relay lines

                r += '<td rowspan="2" class="'+ prefix_class + '0' +'">' + "&nbsp;" + '</td>\r\n';
                r += '<td rowspan="2" class="'+ prefix_class + '1' +'">' + "&nbsp;" + '</td>\r\n';
                for(ind=0;ind<tmcount;ind++)
                {
                  r += '<td colspan="' + cells_for_tm + '" class="relay_tm_name">' +  "&nbsp;" + '</td>\r\n';
                }
                if((tmcount == 8) || ((alternate == 1) && (tmcount == 6)))
                {
                  if((alternate == 1) && (tmcount == 6))
                    r += '<td class="radio0_6alt">&nbsp;</td>\r\n';
                  else
                  if(tmcount == 8)
                    r += '<td class="radio0_8">&nbsp;</td>\r\n';
                  else
                    r += '<td class="radio0">&nbsp;</td>\r\n';
                  r += '<td colspan="4" class="radio_tm_name">&nbsp;</td>\r\n';
                }
                r += '</tr>\r\n';
                r += '<tr class="' + cl + nf + '">\r\n';
              }  // end first relay line
              r += generateCells(identifiant,emptyarr, count, prefix_class, pos_min_displayable, pos_max_displayable, cells_for_tm,tmcount,bFoundRadio,pos_recherche,panelscount,alternate, panelIndex);
              r += '</tr>\r\n';
            }
            r += "</tbody>\r\n";
            r += '</table>\r\n';
          }
        }
        else // length=0 => empty lines ------------------
        {
          r +='<tbody class="scrollContent">';
          position = 0;
          nf = '';
          if(identifiant === 'result')
          {
            count = count;
          }
          else if(identifiant === 'multistage')
          {
          	count = count + 2;
          }
          else if(identifiant === 'relay')
          {
            count = cells_count;
          }
          else if(identifiant === 'showo')
          {
            count = count;
          }

          while(position < (phpfixedlines[panelIndex] + phpscrolledlines[panelIndex]))
          {
            position++;
            var cl = ((position % 2) ? 'alternateRow' : 'normalRow');
            r += '<tr class="' + cl + nf + '">\r\n';
            var emptyarr = Array();
            var maxempty = count;
            if(identifiant == 'relay')
              maxempty = 2 + count * tmcount;
            for(var inc=0;inc<maxempty;inc++)
              emptyarr.push("&nbsp;");
            if(identifiant === 'relay')
            {  // first of two relay lines

              r += '<td rowspan="2" class="'+ prefix_class + '0' +'">' + "&nbsp;" + '</td>\r\n';
              r += '<td rowspan="2" class="'+ prefix_class + '1' +'">' + "&nbsp;" + '</td>\r\n';
              for(ind=0;ind<tmcount;ind++)
              {
                r += '<td colspan="' + cells_for_tm + '" class="relay_tm_name">' +  "&nbsp;" + '</td>\r\n';
              }
              if((tmcount == 8) || ((alternate == 1) && (tmcount == 6)))
              {
                if((alternate == 1) && (tmcount == 6))
                  r += '<td class="radio0_6alt">&nbsp;</td>\r\n';
                else
                  if(tmcount == 8)
                    r += '<td class="radio0_8">&nbsp;</td>\r\n';
                  else
                  r += '<td class="radio0">&nbsp;</td>\r\n';
                r += '<td colspan="4" class="radio_tm_name">&nbsp;</td>\r\n';
              }
              r += '</tr>\r\n';
              r += '<tr class="' + cl + nf + '">\r\n';
            }  // end first relay line
            r += generateCells(identifiant,emptyarr, count, prefix_class, pos_min_displayable, pos_max_displayable, cells_for_tm,tmcount,bFoundRadio,pos_recherche,panelscount,alternate, panelIndex);
            r += '</tr>\r\n';
          }
          r += "</tbody>\r\n";
          r += '</table>\r\n';
          bUpdateNeeded = true;
          after_decrement_counter[panelIndex] = 0;
        }

        if(bUpdateNeeded)
        {
          after_decrement_counter[panelIndex]--;
          if(after_decrement_counter[panelIndex] <= 0)
          {
            displayScrollIndex[panelIndex] = phpfixedlines[panelIndex] + startline - 1;
            categorieIndex[panelIndex] = (categorieIndex[panelIndex] + 1) % phpcls[panelIndex].length;
            tableUpdated[panelIndex] = false;
            if(identifiant === 'start')
            {
              updateStart(panelIndex);
            }
            else
            if(identifiant === 'result')
            {
              updateTable(panelIndex);
            }
            else
            if(identifiant === 'relay')
            {
              updateRelay();
            }
            else
            if(identifiant === 'showo')
            {
              updateShowO(panelIndex);
            }
            else
            if(identifiant === 'multistage')
            {
	            updateMultistage(panelIndex);
            }
            after_decrement_counter[panelIndex] = phpscrollaftertime[panelIndex] / phpscrolltime[panelIndex];
          }
        }

        return r;
      }
<?php
  for($i=0;$i<NB_PANEL;$i++)
  {
?>
        function updateDisplayStart<?php echo ($i+1); ?>()
        {
            if(document.getElementById("tableContainer<?php echo ($i); ?>"))
            {
                document.getElementById("tableContainer<?php echo ($i); ?>").innerHTML = ConvertToNiceHtmlTableRow(<?php echo ($i); ?>, 'start', <?php echo $panels[$i]->firstline; ?>,tm_count,<?php echo $panelscount; ?>, <?php echo $panels[$i]->alternate; ?>);
            }
        }

        function updateDisplay<?php echo ($i+1); ?>()
        {
            if(document.getElementById("tableContainer<?php echo ($i); ?>"))
            {
<?php
              $default_identifier = 'result';
              if($panels[$i]->mode == CST_MODE_SHOWO)
                $default_identifier = 'showo';
              else
              if($panels[$i]->mode == CST_MODE_MULTISTAGE)
                $default_identifier = 'multistage';
?>
                document.getElementById("tableContainer<?php echo ($i); ?>").innerHTML = ConvertToNiceHtmlTableRow(<?php echo ($i); ?>, '<?php echo $default_identifier; ?>', <?php echo $panels[$i]->firstline; ?>,tm_count,<?php echo $panelscount; ?>, <?php echo $panels[$i]->alternate; ?>);
            }
        }


		function updateDisplaySummaries<?php echo ($i+1); ?>()
		{
<?php
      if((isset($classPanels[$i])) && ($classPanels[$i] != null))
			{
				foreach($classPanels[$i] as $k => $v)
				{
          if($panels[$i]->mode == CST_MODE_RELAY)
          {
?>
					updateDisplayRelaySummary(<?php echo $i ?>, <?php echo $k; ?>, <?php echo $v; ?>);
<?php
          }
          else
          {
?>
					updateDisplaySummary(<?php echo $i; ?>, <?php echo $k; ?>, <?php echo $v; ?>);
<?php
          }
				}
			}
?>
		}
<?php
  }
?>
        function updateDisplayRelays()
        {
<?php
        for($i=0;$i<$panelscount;$i++)
        {
          if($panels[$i]->mode == CST_MODE_RELAY)
          {
            echo 'updateDisplayRelay('.$i.');'."\n";
          }
        }
?>
        }

        function updateDisplayRelay(panel)
        {
            if(document.getElementById("tableContainer" + panel))
            {
                document.getElementById("tableContainer" + panel).innerHTML = ConvertToNiceHtmlTableRow(panel, 'relay', phpfirstline[panel], tm_count,<?php echo $panelscount; ?>, phpalternate[panel]);
            }
        }

        function updateDisplayRelaySummary(panel, num1, num2)
        {
            if(document.getElementById("tableContainer" + panel + "_" + num1 + "_" + num2))
            {
                document.getElementById("tableContainer" + panel + "_" + num1 + "_" + num2).innerHTML = ConvertToSummaryHtmlTableRow(panel, 'relay', 1, tm_count,<?php echo $panelscount; ?>, num1, num2, phpalternate[panel]);
            }
        }

        function updateDisplaySummary(panel, num1, num2)
        {
            if(document.getElementById("tableContainer" + panel + "_" + num1 + "_" + num2))
            {
                document.getElementById("tableContainer" + panel + "_" + num1 + "_" + num2).innerHTML = ConvertToSummaryHtmlTableRow(panel, 'resultsummary', 1,tm_count,<?php echo $panelscount; ?>, num1, num2, phpalternate[panel]);
            }
        }

        function create_refresh_display()
        {
          <?php
          for($i=0;$i<$panelscount;$i++)
          {
            switch($panels[$i]->content)
            {
              case CST_CONTENT_PICTURE:
              break;
              case CST_CONTENT_TEXT:
              break;
              case CST_CONTENT_HTML:
              break;
              case CST_CONTENT_START:
                switch($panels[$i]->mode)
                {
                  default:
                    ?>
                    window.setInterval(updateDisplayStart<?php echo ($i+1); ?>, phpscrolltime[<?php echo ($i); ?>]*100);
                    <?php
                  break;
                }
              break;
              case CST_CONTENT_RESULT:
                switch($panels[$i]->mode)
                {
                  case CST_MODE_INDIVIDUAL:
                    ?>
                    window.setInterval(updateDisplay<?php echo ($i+1); ?>, phpscrolltime[<?php echo ($i); ?>]*100);
                    <?php
                  break;
                  case CST_MODE_RELAY:
                    if($i == 0)
                    {
                      ?>
                      window.setInterval(updateDisplayRelays, phpscrolltime[<?php echo ($i); ?>]*100);
                      <?php
                    }
                  break;
                  case CST_MODE_SHOWO:
                    ?>
                    window.setInterval(updateDisplay<?php echo ($i+1); ?>, phpscrolltime[<?php echo ($i); ?>]*100);
                    <?php
                  break;
                  case CST_MODE_MULTISTAGE:
                    ?>
                    window.setInterval(updateDisplay<?php echo ($i+1); ?>, phpscrolltime[<?php echo ($i); ?>]*100);
                    <?php
                  break;
                }
              break;
              case CST_CONTENT_SUMMARY:
                switch($panels[$i]->mode)
                {
                  case CST_MODE_INDIVIDUAL:
                    ?>
                    window.setInterval(updateDisplaySummaries<?php echo ($i+1); ?>,  ATTENTE_BASE_s*1000);
                    <?php
                  break;
                  case CST_MODE_RELAY:
                    ?>
                    window.setInterval(updateDisplaySummaries<?php echo ($i+1); ?>, ATTENTE_BASE_s*1000);
                    <?php
                  break;
                }
              break;
              case CST_CONTENT_BLOG:
              break;
              case CST_CONTENT_SLIDES:
              break;
              case CST_CONTENT_RADIO:
              break;
              default:
                // not supported
              break;
            }
          }
          ?>
        }
        function create_refresh_table()
        {
            window.setInterval(updateTables, ATTENTE_BASE_s*1000);
        }
        function create_refresh_multistage()
        {
            window.setInterval(updateMultistages, ATTENTE_BASE_s*1000);
        }
        function create_refresh_start()
        {
            window.setInterval(updateStarts, ATTENTE_BASE_s*1000);
        }
        function create_refresh_page()
        {
            window.setInterval(updatePage, ATTENTE_PAGE_s*1000);
        }
        function create_refresh_relay()
        {
            window.setInterval(updateRelay, ATTENTE_BASE_s*1000);
        }
        function create_refresh_showO()
        {
            window.setInterval(updateShowOs, ATTENTE_BASE_s*1000);
        }
        function create_refresh_summary()
        {
            window.setInterval(updateSummaries, ATTENTE_BASE_s*1000);
        }
<?php
  if($bRefreshBlog)
  {
?>
      function blogView(mytab, panel)
      {
        var content = '';
        if(document.getElementById('bloglist' + panel))
        {
          for(i=0;i<mytab.length;i++)
          {
            if(mytab[i][0] < (phpupdateduration[panel] * 60))
            {
              content += '<li class="blogrecent"><span class="blogtime">' + mytab[i][2] + '</span>' + mytab[i][1] + '</li>';
            }
            else
            {
              content += '<li><span class="blogtime">' + mytab[i][2] + '</span>' + mytab[i][1] + '</li>';
            }
          }
          document.getElementById('bloglist' + panel).innerHTML = content;
        }
      }
      function updateBlog(panel)
      {
          var xmlhttp = null;
          if (window.XMLHttpRequest)
          {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
          }
          else
          {// code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
          }
          xmlhttp.onreadystatechange = function()
          {
            if((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
            {
              myblog = eval(xmlhttp.responseText);
              blogView(myblog, panel);
            }
          }
          xmlhttp.open("GET", "aj_refreshblog.php?rcid=" + rcid +
                              "&sid=" + screenIndex +
                              "&limit=" + phpfixedlines[panel], false);
          xmlhttp.send();
      }
      function create_refresh_blog()
      {
          window.setInterval(updateBlogs, ATTENTE_PAGE_s*1000);
      }
      create_refresh_blog();
<?php
  }

  if($bRefreshSlide)
  {
    for($i=0;$i<$panelscount;$i++)
    {
      if($panels[$i]->content == CST_CONTENT_SLIDES)
      {
?>

      var myIndex_<?php echo $i; ?> = 0;
      function carousel<?php echo $i; ?>()
      {
          var i;
          var x = document.getElementsByClassName("mySlides<?php echo $i; ?>");
          for (i = 0; i < x.length; i++)
          {
             x[i].style.display = "none";
          }
          myIndex_<?php echo $i; ?>++;
          if (myIndex_<?php echo $i; ?> > x.length)
          {
            myIndex_<?php echo $i; ?> = 1;
          }
          x[myIndex_<?php echo $i; ?> - 1].style.display = "table-cell";
          setTimeout(carousel<?php echo $i; ?>, <?php echo $panels[$i]->scrolltime; ?> * 100); // Change image
      }
<?php
      }
    }
?>
    function computeSizeImg(imgElem, or_width, or_height)
    {

      var panelCnt = <?php echo $panelscount; ?>;

      ratio_device = 1.25;
      screenw = (screen.height - 70) / panelCnt / ratio_device;
      screenh = (screen.width - 140) / ratio_device;
      ratio_h = or_height / screenh;
      ratio_w = or_width / screenw;
      ratio = Math.max(ratio_h, ratio_w);
      h = or_height / ratio;
      w = or_width / ratio;

      imgElem.width = w;
      imgElem.height = h;
    }
<?php
  }

  if($bRefreshRadio)
  {
?>
      function radioView(mytab, panel, clsTitle, ctrlid, panelscount)
      {
        var content = '';
        if(document.getElementById('radiolist' + panel))
        {
          content += '<tr>';
          content += '<th colspan="3" class="entete_listradio">' + clsTitle + ' [<?php echo MyGetText(111); ?> ' + ctrlid + ']</th>';
          content += '</tr>';
          for(i=0;i<mytab.length;i++)
          {
            var cl = ((i % 2) ? 'alternateRow' : 'normalRow');
            if(mytab[i][0] < (phpupdateduration[panel] * 60))
            {
              content += '<tr class="radiorecent ' + cl + '">';
            }
            else
            {
              content += '<tr class="' + cl + '">';
            }
            content += '<td class="radiotime_' + panelscount + '">' + mytab[i][3] + '</td>';
            content += '<td class="radiorunnername_' + panelscount + '">'+ mytab[i][1] + '</td>';
            content += '<td class="radioorgname_' + panelscount + '">' + mytab[i][2] + '</td>';
            content += '</tr>';
          }
          document.getElementById('radiolist' + panel).innerHTML = content;
        }
      }
      function updateRadio(panelIndex)
      {
          var xmlhttp = null;
          if (window.XMLHttpRequest)
          {// code for IE7+, Firefox, Chrome, Opera, Safari
            xmlhttp = new XMLHttpRequest();
          }
          else
          {// code for IE6, IE5
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
          }
          xmlhttp.onreadystatechange = function()
          {
            if((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
            {
              myradio = eval(xmlhttp.responseText);
              radioView(myradio, panelIndex, phpTitle[panelIndex][categorieIndex[panelIndex]], phpradioctrl[panelIndex], <?php echo $panelscount; ?>);
            }
          }
          xmlhttp.open("GET", "aj_refreshradio.php?rcid=" + rcid +
                              "&cls=" + phpcls[panelIndex][categorieIndex[panelIndex]] +
                              "&cmpId=" + phpcmpId[panelIndex] +
                              "&sid=" + screenIndex +
                              "&radioid=" + phpradioctrl[panelIndex] +
                              "&mode=" + phpmode[panelIndex] +
                              "&limit=" + phpfixedlines[panelIndex], false);
          xmlhttp.send();
      }
      function create_refresh_radio()
      {
          window.setInterval(updateRadios, ATTENTE_PAGE_s*1000);
      }
      create_refresh_radio();
<?php
  }
?>
          -->
        </script>

    </head>
    <body>
<?php
    $hauteur = 40;
?>
        <div style="float:right;height:<?php print $hauteur; ?>px;display:block;width:15%;text-align:right;">
<?php
            print displayTopPicture($titlerightpict, $hauteur);
?>
        </div>
        <div style="float:left;height:<?php print $hauteur; ?>px;display:block;width:15%;text-align:left;">
<?php
            print displayTopPicture($titleleftpict, $hauteur);
?>
        </div>
        <div style="float:left;height:<?php print $hauteur; ?>px;display:block;width:70%;text-align:center;">
<?php
            print '<div style="padding:0;margin:0;vertical-align:top;font-size:'.$titlesize.'px;color:#'.$titlecolor.';">'.$title.'</div>';
            print '<div style="padding:0;margin:0;vertical-align:top;font-size:'.$subtitlesize.'px;color:#'.$subtitlecolor.';">'.$subtitle.'</div>';
?>
        </div>

        <div style="clear:both;"></div>
<?php
        print '<div style="padding:0;margin:0;display:block;width:100%">'."\n";
        $wpanel = 99.0 / $panelscount;
        for($i=0;$i<$panelscount;$i++)
        {
          echo '<div class="panel'.$i.'" style="float:left;display:inline;min-width:'.$wpanel.'%;width:'.$wpanel.'%;">'."\n";
          switch($panels[$i]->content)
          {
            case CST_CONTENT_PICTURE:
              print displayContentPicture($panels[$i]->pict, $i, $panelscount);
            break;
            case CST_CONTENT_TEXT:
              print displayContentText($panels[$i]->txt, $panels[$i]->txtsize, $panels[$i]->txtcolor);
            break;
            case CST_CONTENT_HTML:
              print displayContentHtml($panels[$i]->html);
            break;
            case CST_CONTENT_START:
              echo '<div style="float:left;display:inline;min-width:100%;width:100%;" id="tableContainer'.$i.'" class="tableContainer">'."\n";
              echo '</div>'."\n";
            break;
            case CST_CONTENT_RESULT:
              echo '<div style="float:left;display:inline;min-width:100%;width:100%;" id="tableContainer'.$i.'" class="tableContainer">'."\n";
              echo '</div>'."\n";
            break;
            case CST_CONTENT_SUMMARY:
              print '<div style="padding:0;margin:0;display:block;width:100%">'."\n";
              if((isset($classPanels[$i]))  && ($classPanels[$i] != null))
              {
                foreach($classPanels[$i] as $k => $v)
                {
                  print '    <div style="float:left;display:inline;min-width:100%;width:100%;" id="tableContainer'.$i.'_'.$k.'_'.$v.'" class="tableContainer">'."\n";
                  print '    </div>'."\n";
                }
              }
              print '</div>';
            break;
            case CST_CONTENT_BLOG:
              print displayContentBlog($rcid, $panels[$i]->fixedlines, $panels[$i]->updateduration, $i);
            break;
            case CST_CONTENT_SLIDES:
              print '<div style="display:table;overflow:hidden;margin:auto;">'."\n";
              print '<div style="padding:2px;display:table-cell;vertical-align:middle;text-align:center;">'."\n";
              $slidesfilelist= array();
              $tmp_slidesfilelist=array_diff(scandir("./slides/".$panels[$i]->slides), array('..', '.','index.php','index.html'));
              foreach ($tmp_slidesfilelist as $name)
              {
                $slidesfilelist[$name]=$name;
              }
              if($slidesfilelist != null)
              {
                foreach($slidesfilelist as $key => $val)
                {
                  $arr_img = getimagesize ('./slides/'.$panels[$i]->slides.'/'.$val);
                  echo '<img class="mySlides'.$i.'" src="./slides/'.$panels[$i]->slides.'/'.$val.'" style="" onload="computeSizeImg(this, '.$arr_img[0].', '.$arr_img[1].');">'."\n";
                }
              }
              print '</div>'."\n";
              print '</div>'."\n";
              print '<script type="text/javascript">carousel'.$i.'();</script>'."\n";
            break;
            case CST_CONTENT_RADIO:
              print displayContentRadio($rcid, $panels[$i]->fixedlines, $panels[$i]->updateduration, $i);
            break;
            default:
              echo 'Format non supported yet';
            break;
          }
          echo '</div>'."\n";
        }
        print '</div>'."\n";
?>
    </body>
</html>
