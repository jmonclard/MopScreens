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
  //date_default_timezone_set('Europe/Paris');
  date_default_timezone_set('UTC');
  include_once('functions.php');
  redirectSwitchUsers();

  if (isset($_GET['rcid']))
  {
      $rcid = intval($_GET['rcid']);
  }

	include_once('lang.php');
	$_SESSION['CurrentLanguage'] = isset($_SESSION['CurrentLanguage']) ? $_SESSION['CurrentLanguage'] : autoSelectLanguage(array('fr','en','sv'),'en');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>MopScreens</title>
        <link rel="stylesheet" type="text/css" href="styles/screen.css" />

        <script type="text/javascript">
            var rcid = <?php echo $rcid; ?>;
            var arr_timer = new Array;
            arr_timer[0]=0;

            function EditScreen(rcid,sid)
            {
                location.replace("screenedit.php?rcid="+rcid+"&sid="+sid);
            }

            function ViewScreen(sid)
            {
                window.open("pages.php?p="+sid);
            }

            function refreshScreen()
            {
                if (window.XMLHttpRequest)
                {// code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp = new XMLHttpRequest();
                }
                else
                {// code for IE6, IE5
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttp.onreadystatechange = function(){
                    if((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
                    {
                        // todo
                        arr_timer = eval(xmlhttp.responseText);
                        fillTimestamps();
                    }
                }
                // dans la fonction suivante, false pour synchrone = attendre le retour de la commande
                xmlhttp.open("GET", "aj_refreshscreen.php?rcid=" + rcid , false);
                xmlhttp.send();
            }
            window.onload = function() { refreshScreen(); };

            window.setInterval(refreshScreen, 5000);


            function updateScreen(mysid)
            {
                if (window.XMLHttpRequest)
                {// code for IE7+, Firefox, Chrome, Opera, Safari
                    xmlhttp = new XMLHttpRequest();
                }
                else
                {// code for IE6, IE5
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                xmlhttp.onreadystatechange = function(){
                    if((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
                    {
                        // todo
                    }
                }
                // dans la fonction suivante, false pour synchrone = attendre le retour de la commande
                xmlhttp.open("GET", "aj_updatescreen.php?rcid=" + rcid +"&sid=" + mysid, true);
                xmlhttp.send();
            }

            function fillTimestamps()
            {
                var i = 0;
                var j;
                var delta;
                //var d = new Date();
                var now = arr_timer[0];
                var style_class = "";

                for(i=0;i<arr_timer.length;i++)
                {
                    style_class = "refresh";
                    j = 2*i + 1;
                    delta_refresh = (now - arr_timer[j]);
                    delta_redraw = (now - arr_timer[j +1]);
                    if(delta_refresh > 999)
                    {
                        delta_refresh = 999;
                    }
                    if(delta_redraw > 999)
                    {
                        delta_redraw = 999;
                    }
                    if((delta_refresh > 30) || (delta_redraw > 30))
                    {
                        style_class = "norefresh";
                    }

                    if(document.getElementById('idtimestamp' + (i+1)))
                    {
                        //d.setTime(1000 * arr_timer[j]);
                        document.getElementById('idtimestamp' + (i+1)).className = style_class;
                        document.getElementById('idtimestamp' + (i+1)).innerHTML = '<img src="img/refresh.png" alt="'+delta_refresh+'s - '+delta_redraw+'s" title="'+delta_refresh+'s - '+delta_redraw+'s" onclick="updateScreen(' + (i+1) + ')" />';//d.toLocaleTimeString();//d.toLocaleString();
                    }

                }
            }
        </script>

    </head>
    <body>
<?php
    include_once('screenfunctions.php');
    include_once('config.php');

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

      var $firstClass;

      function Panel($num)
      {
        $this->numpanel = $num;
      }
    }

    $PHP_SELF = $_SERVER['PHP_SELF'];
     $link = ConnectToDB();

    if (isset($_GET['rcid']))
    {
        //$rcid = intval($_GET['rcid']);
        $configname = GetConfigurationName($rcid, $link);
        print "<h1>$configname</h1>\n";
    }

    $panel1 = new Panel(1);
    $panel2 = new Panel(2);
    $panel3 = new Panel(3);
    $panel4 = new Panel(4);
    $panels = array($panel1,$panel2,$panel3,$panel4);

    print "<table border>\n";
    print "  <tr>\n";
    print "    <th rowspan=2 colspan=4>".MyGetText(24)."</th>\n"; //screen
    print "    <th rowspan=2>".MyGetText(9)."</th>\n"; // competition
    print "    <th rowspan=2>".MyGetText(25)."</th>\n"; // Title
    print "    <th rowspan=2>".MyGetText(26)."</th>\n"; // Subtitle
    print "    <th colspan=2>".MyGetText(27)."</th>\n"; // Picture
    print "    <th colspan=2>".MyGetText(31)." 1</th>\n"; // Panel 1
    print "    <th colspan=2>".MyGetText(31)." 2</th>\n"; // Panel 2
    print "    <th colspan=2>".MyGetText(31)." 3</th>\n"; // Panel 3
    print "    <th colspan=2>".MyGetText(31)." 4</th>\n"; // Panel 4
    print "  </tr>\n";
    print "  <tr>\n";
    print "    <th>".MyGetText(28)."</th>\n"; // left
    print "    <th>".MyGetText(29)."</th>\n"; // Right

    print "    <th>".MyGetText(32)."</th>\n"; // Type
    print "    <th>".MyGetText(33)."</th>\n"; // Content

    print "    <th>".MyGetText(32)."</th>\n"; // Type
    print "    <th>".MyGetText(33)."</th>\n"; // Content

    print "    <th>".MyGetText(32)."</th>\n"; // Type
    print "    <th>".MyGetText(33)."</th>\n"; // Content

    print "    <th>".MyGetText(32)."</th>\n"; // Type
    print "    <th>".MyGetText(33)."</th>\n"; // Content
    print "  </tr>\n";

    include_once('screenfunctions.php');

    $action = isset($_GET['action']) ? strval($_GET['action']) : "none";

   //================================== update screens configs================================================

    if ($action == "clearclasses")
    {
        $rcid = isset($_GET['rcid']) ? intval($_GET['rcid']) : 0;
        $cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
        $sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
        $panel = isset($_GET['panel']) ? intval($_GET['panel']) : 0;
        if (($rcid>0)&&($cid>0)&&($sid>0)&&($panel>0))
        {
            $sql = "DELETE FROM resultclass WHERE rcid='$rcid' AND cid='$cid' AND sid='$sid' AND panel='$panel'";
            mysqli_query($link, $sql);
        }
    }

    if ($action == "updateclasses")
    {
        $rcid = isset($_GET['rcid']) ? intval($_GET['rcid']) : 0;
        $cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
        $sid = isset($_GET['sid']) ? intval($_GET['sid']) : 0;
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
	    $now = time();
	    $str = "refresh=$now";
	    $sql = "UPDATE resultscreen SET $str WHERE rcid=$rcid AND sid=$sid";
	    $res = mysqli_query($link, $sql);
        }

    }

    if ($action==="update")
    {
        if ((isset($_GET['rcid']))&&(isset($_GET['sid'])))
        {

            $rcid = intval($_GET['rcid']);
            $sid = intval($_GET['sid']);
            $cid = intval($_GET['cid']);

            $style = isset($_GET['style']) ? stripSlashes($_GET['style']) : "co2016-04-01s.css";
            $title = isset($_GET['title']) ? stripSlashes($_GET['title']) : "no title";
            $titlesize = isset($_GET['titlesize']) ? intval($_GET['titlesize']) : 24;
            $titlecolor = isset($_GET['titlecolor']) ? stripSlashes($_GET['titlecolor']) : "000000";
            $subtitle = isset($_GET['subtitle']) ? stripSlashes($_GET['subtitle']) : "";
            $subtitlesize = isset($_GET['subtitlesize']) ? intval($_GET['subtitlesize']) : 16;
            $subtitlecolor = isset($_GET['subtitlecolor']) ? stripSlashes($_GET['subtitlecolor']) : "000000";
            $titleleftpict = isset($_GET['titleleftpict']) ? stripSlashes($_GET['titleleftpict']) : "metraware.jpg";
            $titlerightpict = isset($_GET['titlerightpict']) ? stripSlashes($_GET['titlerightpict']) : "metraware.jpg";
            $panelscount = isset($_GET['panelscount']) ? intval($_GET['panelscount']) : 2;

            for ($i=1; $i<=NB_PANEL; $i++)
            {
              $panels[$i-1]->content = isset($_GET['panel'.$i.'content']) ? intval($_GET['panel'.$i.'content']) : 2;
              $panels[$i-1]->mode = isset($_GET['panel'.$i.'mode']) ? intval($_GET['panel'.$i.'mode']) : 1;
              $panels[$i-1]->tm_count = isset($_GET['panel'.$i.'tm_count']) ? intval($_GET['panel'.$i.'tm_count']) : 2;
              $panels[$i-1]->alternate = isset($_GET['panel'.$i.'alternate']) ? intval($_GET['panel'.$i.'alternate']) : 0;
              $panels[$i-1]->pict = isset($_GET['panel'.$i.'pict']) ? stripSlashes($_GET['panel'.$i.'pict']) : "metraware.jpg";
              $panels[$i-1]->slides = isset($_GET['panel'.$i.'slides']) ? stripSlashes($_GET['panel'.$i.'slides']) : "";
              $panels[$i-1]->txt = isset($_GET['panel'.$i.'txt']) ? stripSlashes($_GET['panel'.$i.'txt']) : "Welcome";
              $panels[$i-1]->txtsize = isset($_GET['panel'.$i.'txtsize']) ? intval($_GET['panel'.$i.'txtsize']) : 16;
              $panels[$i-1]->txtcolor = isset($_GET['panel'.$i.'txtcolor']) ? stripSlashes($_GET['panel'.$i.'txtcolor']) : "000000";
              $panels[$i-1]->html = isset($_GET['panel'.$i.'html']) ? stripSlashes($_GET['panel'.$i.'html']) : "exemple.html";
              $panels[$i-1]->firstline = isset($_GET['panel'.$i.'firstline']) ? intval($_GET['panel'.$i.'firstline']) : 1;
              $panels[$i-1]->fixedlines = isset($_GET['panel'.$i.'fixedlines']) ? intval($_GET['panel'.$i.'fixedlines']) : 10;
              $panels[$i-1]->scrolledlines = isset($_GET['panel'.$i.'scrolledlines']) ? intval($_GET['panel'.$i.'scrolledlines']) : 17;
              $panels[$i-1]->scrolltime = isset($_GET['panel'.$i.'scrolltime']) ? intval($_GET['panel'.$i.'scrolltime']) : 10;
              $panels[$i-1]->scrollbeforetime = isset($_GET['panel'.$i.'scrollbeforetime']) ? intval($_GET['panel'.$i.'scrollbeforetime']) : 50;
              $panels[$i-1]->scrollaftertime = isset($_GET['panel'.$i.'scrollaftertime']) ? intval($_GET['panel'.$i.'scrollaftertime']) : 80;
              $panels[$i-1]->updateduration = isset($_GET['panel'.$i.'updateduration']) ? intval($_GET['panel'.$i.'updateduration']) : 3;
              $panels[$i-1]->radioctrl = isset($_GET['panel'.$i.'radioctrl']) ? intval($_GET['panel'.$i.'radioctrl']) : 31;
              $panels[$i-1]->displaynomprenom = isset($_GET['panel'.$i.'displaynomprenom']) ? intval($_GET['panel'.$i.'displaynomprenom']) : 0;
            }

            $title = isset($_GET['title']) ? stripSlashes($_GET['title']) : "no title";

            $chkall = isset($_GET['chkall']) ? $_GET['chkall'] : null;

            $res = mysqli_query($link,  "SELECT rcid FROM resultscreen WHERE rcid=$rcid AND sid=$sid");
            if (mysqli_num_rows($res) > 0)
            {
                $now = time();

                $str = "cid='".$cid."', ";
                $str = $str."panelscount='".$panelscount."', ";

                $str = $str."style='".addSlashes($style)."', ";
                $str = $str."title='".addSlashes($title)."', ";
                $str = $str."titlesize='".$titlesize."', ";
                $str = $str."titlecolor='".addSlashes($titlecolor)."', ";
                $str = $str."subtitle='".addSlashes($subtitle)."', ";
                $str = $str."subtitlesize='".$subtitlesize."', ";
                $str = $str."subtitlecolor='".addSlashes($subtitlecolor)."', ";
                $str = $str."titleleftpict='".addSlashes($titleleftpict)."', ";
                $str = $str."titlerightpict='".addSlashes($titlerightpict)."', ";

                for ($i=1; $i<=NB_PANEL; $i++)
                {
                  $str = $str."panel".$i."content='".$panels[$i-1]->content."', ";
                  $str = $str."panel".$i."mode='".$panels[$i-1]->mode."', ";
                  $str = $str."panel".$i."tm_count='".$panels[$i-1]->tm_count."', ";
                  $str = $str."panel".$i."alternate='".$panels[$i-1]->alternate."', ";
                  $str = $str."panel".$i."pict='".addSlashes($panels[$i-1]->pict)."', ";
                  $str = $str."panel".$i."slides='".addSlashes($panels[$i-1]->slides)."', ";
                  $str = $str."panel".$i."txt='".addSlashes($panels[$i-1]->txt)."', ";
                  $str = $str."panel".$i."txtsize='".$panels[$i-1]->txtsize."', ";
                  $str = $str."panel".$i."txtcolor='".addSlashes($panels[$i-1]->txtcolor)."', ";
                  $str = $str."panel".$i."html='".addSlashes($panels[$i-1]->html)."', ";
                  $str = $str."panel".$i."firstline='".$panels[$i-1]->firstline."', ";
                  $str = $str."panel".$i."fixedlines='".$panels[$i-1]->fixedlines."', ";
                  $str = $str."panel".$i."scrolledlines='".$panels[$i-1]->scrolledlines."', ";
                  $str = $str."panel".$i."scrolltime='".$panels[$i-1]->scrolltime."', ";
                  $str = $str."panel".$i."scrollbeforetime='".$panels[$i-1]->scrollbeforetime."', ";
                  $str = $str."panel".$i."scrollaftertime='".$panels[$i-1]->scrollaftertime."', ";
                  $str = $str."panel".$i."updateduration='".$panels[$i-1]->updateduration."', ";
                  $str = $str."panel".$i."radioctrl='".$panels[$i-1]->radioctrl."', ";
                  $str = $str."panel".$i."displaynomprenom='".$panels[$i-1]->displaynomprenom."', ";
                }
                $str = $str."refresh=$now ";

                $sql = "UPDATE resultscreen SET $str WHERE rcid=$rcid AND sid=$sid";
                $res = mysqli_query($link, $sql);

                //-------- check all management ---------

                $now = time();
                $str = "refresh=$now, ";
                if ($chkall !== null)
                {
                    foreach ($chkall as $i => $v)
                    {

                        if ($v=="cid") $str = $str."cid='".$cid."', ";
                        if ($v=="panelscount") $str = $str."panelscount='".$panelscount."', ";

                        if ($v=="style") $str = $str."style='".addSlashes($style)."', ";

                        if ($v=="title") $str = $str."title='".addSlashes($title)."', ";
                        if ($v=="title") $str = $str."titlesize='".$titlesize."', ";
                        if ($v=="title") $str = $str."titlecolor='".addSlashes($titlecolor)."', ";

                        if ($v=="subtitle") $str = $str."subtitle='".addSlashes($subtitle)."', ";
                        if ($v=="subtitle") $str = $str."subtitlesize='".$subtitlesize."', ";
                        if ($v=="subtitle") $str = $str."subtitlecolor='".addSlashes($subtitlecolor)."', ";

                        if ($v=="titleleftpict") $str = $str."titleleftpict='".addSlashes($titleleftpict)."', ";
                        if ($v=="titlerightpict") $str = $str."titlerightpict='".addSlashes($titlerightpict)."', ";

                        for ($i=1; $i<=NB_PANEL; $i++)
                        {
                          if ($v=="panel".$i."content") $str = $str."panel".$i."content='".$panels[$i-1]->content."', ";
                          if ($v=="panel".$i."mode") $str = $str."panel".$i."mode='".$panels[$i-1]->mode."', ";
                          if ($v=="panel".$i."tm_count") $str = $str."panel".$i."tm_count='".$panels[$i-1]->tm_count."', ";
                          if ($v=="panel".$i."alternate") $str = $str."panel".$i."alternate='".$panels[$i-1]->alternate."', ";
                          if ($v=="panel".$i."pict") $str = $str."panel".$i."pict='".addSlashes($panels[$i-1]->pict)."', ";
                          if ($v=="panel".$i."slides") $str = $str."panel".$i."slides='".addSlashes($panels[$i-1]->slides)."', ";
                          if ($v=="panel".$i."txt") $str = $str."panel".$i."txt='".addSlashes($panels[$i-1]->txt)."', ";
                          if ($v=="panel".$i."txt") $str = $str."panel".$i."txtsize='".$panels[$i-1]->txtsize."', ";
                          if ($v=="panel".$i."txt") $str = $str."panel".$i."txtcolor='".addSlashes($panels[$i-1]->txtcolor)."', ";
                          if ($v=="panel".$i."html") $str = $str."panel".$i."html='".addSlashes($panels[$i-1]->html)."', ";
                          if ($v=="panel".$i."firstline") $str = $str."panel".$i."firstline='".$panels[$i-1]->firstline."', ";
                          if ($v=="panel".$i."fixedlines") $str = $str."panel".$i."fixedlines='".$panels[$i-1]->fixedlines."', ";
                          if ($v=="panel".$i."scrolledlines") $str = $str."panel".$i."scrolledlines='".$panels[$i-1]->scrolledlines."', ";
                          if ($v=="panel".$i."scrolltime") $str = $str."panel".$i."scrolltime='".$panels[$i-1]->scrolltime."', ";
                          if ($v=="panel".$i."scrollbeforetime") $str = $str."panel".$i."scrollbeforetime='".$panels[$i-1]->scrollbeforetime."', ";
                          if ($v=="panel".$i."scrollaftertime") $str = $str."panel".$i."scrollaftertime='".$panels[$i-1]->scrollaftertime."', ";
                          if ($v=="panel".$i."updateduration") $str = $str."panel".$i."updateduration='".$panels[$i-1]->updateduration."', ";
                          if ($v=="panel".$i."displaynomprenom") $str = $str."panel".$i."displaynomprenom='".$panels[$i-1]->displaynomprenom."', ";
                        }

                    } // for each
                    $str = rtrim($str,", ");
                    $sql = "UPDATE resultscreen SET $str WHERE rcid=$rcid";
                    $res = mysqli_query($link, $sql);
                }  // chk all != null
            } // mysqli_num_rows>0
        } //rcid and cid defined
    } // update


   //================================== display screens configs================================================

    if (isset($_GET['rcid']))
    {
        $rcid = intval($_GET['rcid']);

        $sql = "SELECT sid,title FROM resultscreen WHERE rcid=$rcid";
        $res = mysqli_query($link, $sql);
        $n=mysqli_num_rows($res);

        if ($n < NB_SCREEN)
        {
            if ($n<1)
            {
                $n=1;
            }
            for ($i=$n; $i<=NB_SCREEN; $i++)
            {
                AddNewScreen($rcid,$i,$link);
            }
        }

        $tablecid=array();

        $sql = "SELECT * FROM resultscreen WHERE rcid=$rcid";
        $res = mysqli_query($link, $sql);
        $n=mysqli_num_rows($res);
        while ($r = mysqli_fetch_array($res))
        {
            $sid=$r['sid'];
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
              $panels[$i-1]->content = $r['panel'.$i.'content'];
              $panels[$i-1]->mode = $r['panel'.$i.'mode'];
              $panels[$i-1]->tm_count = $r['panel'.$i.'tm_count'];
              $panels[$i-1]->alternate = $r['panel'.$i.'alternate'];
              $panels[$i-1]->pict=$r['panel'.$i.'pict'];
              $panels[$i-1]->slides=$r['panel'.$i.'slides'];
              $panels[$i-1]->txt=stripslashes($r['panel'.$i.'txt']);
              $panels[$i-1]->html=$r['panel'.$i.'html'];
              $panels[$i-1]->classes=GetClassesAndEntries($rcid, $cid, $sid,$i,$link);
              $panels[$i-1]->firstClass=GetFirstClass($rcid, $cid, $sid,$i,$link);
              $panels[$i-1]->radioctrl=$r['panel'.$i.'radioctrl'];
              $panels[$i-1]->displaynomprenom=$r['panel'.$i.'displaynomprenom'];
            }

            print '<tr>';
            print '<td>'.$sid.'</td>';
            print '<td><img src="img/ecran.png" title="'.MyGetText(18).'" onclick="ViewScreen('.$sid.');"></img></td>';
            print '<td id="idtimestamp'.$sid.'" class=""></td>';
            print '<td><img src="img/edit.png" title="'.MyGetText(1).'" onclick="EditScreen('.$rcid.','.$sid.');"></img></td>';

            $sqlcname = "SELECT name FROM mopcompetition WHERE cid=$cid";
            $rescname = mysqli_query($link, $sqlcname);
	          $cname = "";
            if($rcname = mysqli_fetch_array($rescname))
            {
              $cname=$rcname['name'];
            }
            $tablecid[$cid]=$cname;

            print "<td>$cname</td>\n";
            print "<td class='screen_title'>$title</td>\n";
            print "<td>$subtitle</td>\n";
            print "<td>$titleleftpict</td>\n";
            print "<td>$titlerightpict</td>\n";

            for ($i=1;$i<=NB_PANEL; $i++)
            {
              if ($i<=$panelscount)
              {
                switch ($panels[$i-1]->content) {
                  case 1:
                      print '<td class="screen_content"><img src="img/pict.png" title="'.MyGetText(38).'"/></td>'; // picture
                      print '<td>'.$panels[$i-1]->pict.'</td>';
                      break;
                  case 2:
                      print '<td class="screen_content"><img src="img/txt.png" title="'.MyGetText(39).'"/></td>'; // text
                      print '<td>'.$panels[$i-1]->txt.'</td>';
                      break;
                  case 3:
                      print '<td class="screen_content"><img src="img/htm.png" title="'.MyGetText(40).'"/></td>'; // html
                      print '<td>'.$panels[$i-1]->html.'</td>';
                      break;
                  case 4:
                      print '<td class="screen_content"><a href=screenclasses.php?rcid='.$rcid.'&cid='.$cid.'&sid='.$sid.'&panel='.$i.'&ret=1><img src="img/start.png" title="'.MyGetText(41).'"/></a></td>'; // StartList
                      print '<td>'.$panels[$i-1]->classes.'</td>';
                      break;
                  case 5:
                      print '<td class="screen_content"><a href=screenclasses.php?rcid='.$rcid.'&cid='.$cid.'&sid='.$sid.'&panel='.$i.'&ret=1><img src="img/podium.png" title="'.MyGetText(43).'"/></a></td>'; // Results
                      print '<td>'.$panels[$i-1]->classes.'</td>';
                      break;
                  case 6:
                      print '<td class="screen_content"><a href=screenclasses.php?rcid='.$rcid.'&cid='.$cid.'&sid='.$sid.'&panel='.$i.'&ret=1><img src="img/resume.png" title="'.MyGetText(92).'"/></a></td>'; // Summary
                      print '<td>'.$panels[$i-1]->classes.'</td>';
                      break;
                  case 7:
                      print '<td class="screen_content"><img src="img/blog.png" title="'.MyGetText(93).'"/></td>'; // Blog
                      print '<td>&nbsp;</td>';
                      break;
                  case 8:
                      print '<td class="screen_content"><img src="img/slides.png" title="'.MyGetText(99).'"/></td>'; // Slides
                      print '<td>'.$panels[$i-1]->slides.'</td>';
                      break;
                  case 9:
                      print '<td class="screen_content"><a href=screenclasses.php?rcid='.$rcid.'&cid='.$cid.'&sid='.$sid.'&panel='.$i.'&ret=1><img src="img/radio.png" title="'.MyGetText(107).'"/></a></td>'; // radio
                      if(strlen($panels[$i-1]->firstClass))
                        $txt = $panels[$i-1]->firstClass.' ['.$panels[$i-1]->radioctrl.']';
                      else
                        $txt = "&nbsp;";
                      print '<td>'.$txt.'</td>';
                      break;
                  default:
                      print '<td bgcolor=LightGrey> &nbsp; </td>';
                      print '<td bgcolor=LightGrey> &nbsp; </td>';
                      break;
                }
              }
              else
              {
                print '<td bgcolor=LightGrey> &nbsp; </td>';
                print '<td bgcolor=LightGrey> &nbsp; </td>';
              }
            }
            print "</tr>\n";

        }
        print "</table>\n";


        print "<br/>\n";
        print "<a href='screenconfig.php'>".MyGetText(19)."</a>&nbsp;&nbsp;&nbsp;"; // Link to main page
        print "<br/>\n";

   //================================== display classes statistics ================================================

        foreach ($tablecid as $cid => $cname)
        {
            // determines number of entries
            $sql3 = "SELECT COUNT(*) FROM mopcompetitor WHERE cid=$cid";
            $res3 = mysqli_query($link, $sql3);
            if (mysqli_num_rows($res3) > 0)
            {
              if ($r3 = mysqli_fetch_array($res3))
              {
                $totalentry=$r3[0];
              }
            }
            if ($cid!=0)
            {
              print "<h2>$cname ($totalentry)</h2>\n"; // competition name and total entries
            }
            print "<table border>\n";

            $sql = "SELECT name,id FROM mopclass WHERE mopclass.cid=$cid ORDER BY name";
            $res = mysqli_query($link, $sql);
            if (mysqli_num_rows($res) > 0)
            {
                print "<tr>\n";
                print "<th>".MyGetText(44)."</th>\n"; // Classes
                print "<th>".MyGetText(42)."</th>\n"; // Start list
                print "<th>".MyGetText(43)."</th>\n"; // Results
                print "<th colspan=2>".MyGetText(45)."</th>\n"; // Start
                print "<th colspan=2>".MyGetText(46)."</th>\n"; // Entries
                print "<th>".MyGetText(47)."</th>\n";
                print "<th>&nbsp;</th>\n";
                print "</tr>\n";

                while ($r = mysqli_fetch_array($res)) // for all classes
                {
                    print "<tr>\n";
                    $classname=$r['name'];
                    $classid=$r['id'];
                    print "<td class='class_class'>$classname</td>\n";

                    //-- Start List
                    $displaySL="";
                    $displayR="";
                    $sql2 = "SELECT rc.sid, rc.panel, rs.panelscount, rs.panel1content, rs.panel2content, rs.panel3content, rs.panel4content FROM resultclass rc, resultscreen rs WHERE ";
                        $sql2 = $sql2."rs.rcid=rc.rcid AND ";
                        $sql2 = $sql2."rs.cid=rc.cid AND ";
                        $sql2 = $sql2."rs.sid=rc.sid AND ";
                        $sql2 = $sql2."rc.rcid=$rcid AND ";
                        $sql2 = $sql2."rc.cid=$cid AND ";
                        $sql2 = $sql2."rc.id=$classid;";
                    $res2 = mysqli_query($link, $sql2);
                    if (mysqli_num_rows($res2) > 0)
                    {
                      while ($r2 = mysqli_fetch_array($res2))
                      {
                        $screen2=$r2['sid'];
                        $panel2=$r2['panel'];
                        $panelscount2=$r2['panelscount'];


                        for ($i=1; $i<=NB_PANEL; $i++)
                        {
                          $panelcontent2[$i-1]=$r2['panel'.$i.'content'];
                        }

                        for ($i=1; $i<=NB_PANEL; $i++)
                        {
                          //-- Start List
                          if (($panel2==$i)&&($panelcontent2[$i-1]==4))
                          {
                              if (strlen($displaySL)>0)
                              {
                                $displaySL=$displaySL." ".$screen2.".".$i;
                              }
                              else
                              {
                                $displaySL=$screen2.".".$i;
                              }
                          }

                          //-- Results
                          if (($panel2==$i)&&($panelcontent2[$i-1]==5))
                          {
                              if (strlen($displayR)>0)
                              {
                                $displayR=$displayR." ".$screen2.".".$i;
                              }
                              else
                              {
                                $displayR=$screen2.".".$i;
                              }
                          }
                        }
                      }
                    }
                    print "<td>$displaySL</td>\n";
                    print "<td>$displayR</td>\n";


                    //-- 1st Start
                    $heuremin="00:00:00";
                    $heuremax="00:00:00";
                    //date_default_timezone_set('Europe/Paris');
                    date_default_timezone_set('UTC');
                    $sql2 = "SELECT MIN(st),MAX(st) FROM mopcompetitor WHERE cid=$cid AND cls=$classid";
                    $res2 = mysqli_query($link, $sql2);
                    if (mysqli_num_rows($res2) > 0)
                    {
                        if ($r2 = mysqli_fetch_array($res2))
                        {
                          $s=$r2[0]/10;
                          $heuremin=date("H:i:s",$s);
                          $s=$r2[1]/10;
                          $heuremax=date("H:i:s",$s);
                        }
                    }
                    print "<td>$heuremin</td>\n";

                    //--Last Start
                    print "<td>$heuremax</td>\n";

                    //--Entries
                    $nentry=0;
                    $sql2 = "SELECT COUNT(*) FROM mopcompetitor WHERE cid=$cid AND cls=$classid";
                    $res2 = mysqli_query($link, $sql2);
                    if (mysqli_num_rows($res2) > 0)
                    {
                        if ($r2 = mysqli_fetch_array($res2))
                        {
                          $nentry=$r2[0];
                        }
                    }
                    if($totalentry != 0)
                    {
	                    $pctentries=round(1000*$nentry/$totalentry)/10.0;
	                }
	                else
	                {
	                   $pctentries=0;
	                }
                    print "<td class='class_entries'>$nentry</td>\n";
                    print "<td class='class_entries'>$pctentries %</td>\n";

                    //-- Done
                    $n=0;
                    $sql2 = "SELECT COUNT(*) FROM mopcompetitor WHERE cid=$cid AND cls=$classid AND stat>0";
                    $res2 = mysqli_query($link, $sql2);
                    if (mysqli_num_rows($res2) > 0)
                    {
                        if ($r2 = mysqli_fetch_array($res2))
                        {
                          $ndone=$r2[0];
                        }
                    }
                    print "<td class='class_done'>$ndone</td>\n";

                    $pct=100;
                    if ($nentry>0)
                    {
                      $pct = round(100*$ndone/$nentry);
                    }
                    if ($pct>=99)
                    {
                        print "<td class='class_pct_ok'>$pct %</td>\n";
                    }
                    else
                    {
                        print "<td class='class_pct'>$pct %</td>\n";
                    }

                    print "</tr>\n";
                }
            }
            print "</table><br/>\n";
        }

    }
    print "<a href=screen.php?rcid=$rcid>".MyGetText(23)."</a>";
?>
    </body>
</html>
