<?php
  /*
  Copyright 2014 Metraware
  
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
  
    $ip=$_SERVER['REMOTE_ADDR'];
    $ipnb=explode('.',$ip);
    session_start();
    if (($ipnb[0]!='192')||($ipnb[1]!='168')||($ipnb[2]!='0')||($ipnb[3]=='20'))
    {
        header("Location: http://192.168.0.10");
        die();
    }
    if (isset($_GET['rcid'])) 
    {
        $rcid = intval($_GET['rcid']);
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>CFCO 2014 Screens</title>
        <link rel="stylesheet" type="text/css" href="styles/screen.css" />
        
        <script type="text/javascript">
            var rcid = <?php echo $rcid; ?>;
            var arr_timer = new Array;
            
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
                xmlhttp.onreadystatechange = function()
                {
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
                        document.getElementById('idtimestamp' + (i+1)).innerHTML = '<img src="img/refresh.png" alt="'+delta_refresh+'s - '+delta_redraw+'s" title="'+delta_refresh+'s - '+delta_redraw+'s" />';//d.toLocaleTimeString();//d.toLocaleString();
                    }
                    
                }
            }
        </script>
        
    </head>
    <body>
<?php
    include_once('functions.php');
    include_once('screenfunctions.php');
    

    $PHP_SELF = $_SERVER['PHP_SELF'];
    ConnectToDB();

    define("NB_SCREEN",12);

    if (isset($_GET['rcid'])) 
    {
        //$rcid = intval($_GET['rcid']);
        $configname = GetConfigurationName($rcid);
        print "<h1>$configname</h1>\n";
    }
?>

    <table border>
            <tr>
                <th rowspan=2 colspan=4>Screen</th>
                <th rowspan=2>Competition</th>
                <th rowspan=2>Title</th>
                <th rowspan=2>SubTitle</th>
                <th colspan=2>Pictures</th>
                <th rowspan=2>Mode</th>
                <th colspan=2>Full screen</th>
                <th colspan=2>Left pane</th>
                <th colspan=2>Right pane</th>
            </tr>
            <tr>
                <th>Left</th>
                <th>Right</th>

                <th>Type</th>
                <th>Content</th>

                <th>Type</th>
                <th>Content</th>

                <th>Type</th>
                <th>Content</th>
            </tr>
<?php
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
            mysql_query($sql);
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
            mysql_query($sql);
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
                    $res = mysql_query($sql);
                }
            }
        }

    }
   
    if ($action==="update")
    {
        if ((isset($_GET['rcid']))&&(isset($_GET['sid'])))
        {

            $rcid = intval($_GET['rcid']);
            $sid = intval($_GET['sid']);
            $cid = intval($_GET['cid']);
            
            $title = isset($_GET['title']) ? stripSlashes($_GET['title']) : "no title";
            $titlesize = isset($_GET['titlesize']) ? intval($_GET['titlesize']) : 24;
            $titlecolor = isset($_GET['titlecolor']) ? stripSlashes($_GET['titlecolor']) : "000000";
            $subtitle = isset($_GET['subtitle']) ? stripSlashes($_GET['subtitle']) : "";
            $subtitlesize = isset($_GET['subtitlesize']) ? intval($_GET['subtitlesize']) : 16;
            $subtitlecolor = isset($_GET['subtitlecolor']) ? stripSlashes($_GET['subtitlecolor']) : "000000";
            $titleleftpict = isset($_GET['titleleftpict']) ? stripSlashes($_GET['titleleftpict']) : "cfco2014.jpg";
            $titlerightpict = isset($_GET['titlerightpict']) ? stripSlashes($_GET['titlerightpict']) : "metraware.jpg";
            $screenmode = isset($_GET['screenmode']) ? intval($_GET['screenmode']) : 2;

            $fullcontent = isset($_GET['fullcontent']) ? intval($_GET['fullcontent']) : 2;
            $fullpict = isset($_GET['fullpict']) ? stripSlashes($_GET['fullpict']) : "cfco2014.jpg";
            $fulltxt = isset($_GET['fulltxt']) ? stripSlashes($_GET['fulltxt']) : "Bienvenus aux Championnats de France 2014 de Course d'Orientation";
            $fulltxtsize = isset($_GET['fulltxtsize']) ? intval($_GET['fulltxtsize']) : 16;
            $fulltxtcolor = isset($_GET['fulltxtcolor']) ? stripSlashes($_GET['fulltxtcolor']) : "000000";
            $fullhtml = isset($_GET['fullhtml']) ? stripSlashes($_GET['fullhtml']) : "exemple.html";
            $fullfixedlines = isset($_GET['fullfixedlines']) ? intval($_GET['fullfixedlines']) : 3;
            $fullscrolledlines = isset($_GET['fullscrolledlines']) ? intval($_GET['fullscrolledlines']) : 3;
            $fullscrolltime = isset($_GET['fullscrolltime']) ? intval($_GET['fullscrolltime']) : 3;
            $fullscrollbeforetime = isset($_GET['fullscrollbeforetime']) ? intval($_GET['fullscrollbeforetime']) : 3;
            $fullscrollaftertime = isset($_GET['fullscrollaftertime']) ? intval($_GET['fullscrollaftertime']) : 3;
            $fullupdateduration = isset($_GET['fullupdateduration']) ? intval($_GET['fullupdateduration']) : 3;

            $leftcontent = isset($_GET['leftcontent']) ? intval($_GET['leftcontent']) : 5;
            $leftpict = isset($_GET['leftpict']) ? stripSlashes($_GET['leftpict']) : "cfco2014.jpg";
            $lefttxt = isset($_GET['lefttxt']) ? stripSlashes($_GET['lefttxt']) : "Bienvenus aux Championnats de France 2014 de Course d'Orientation";
            $lefttxtsize = isset($_GET['lefttxtsize']) ? intval($_GET['lefttxtsize']) : 16;
            $lefttxtcolor = isset($_GET['lefttxtcolor']) ? stripSlashes($_GET['lefttxtcolor']) : "000000";
            $lefthtml = isset($_GET['lefthtml']) ? stripSlashes($_GET['lefthtml']) : "exemple.html";
            $leftfixedlines = isset($_GET['leftfixedlines']) ? intval($_GET['leftfixedlines']) : 3;
            $leftscrolledlines = isset($_GET['leftscrolledlines']) ? intval($_GET['leftscrolledlines']) : 3;
            $leftscrolltime = isset($_GET['leftscrolltime']) ? intval($_GET['leftscrolltime']) : 3;
            $leftscrollbeforetime = isset($_GET['leftscrollbeforetime']) ? intval($_GET['leftscrollbeforetime']) : 3;
            $leftscrollaftertime = isset($_GET['leftscrollaftertime']) ? intval($_GET['leftscrollaftertime']) : 3;
            $leftupdateduration = isset($_GET['leftupdateduration']) ? intval($_GET['leftupdateduration']) : 3;

            $rightcontent = isset($_GET['rightcontent']) ? intval($_GET['rightcontent']) : 5;
            $rightpict = isset($_GET['rightpict']) ? stripSlashes($_GET['rightpict']) : "cfco2014.jpg";
            $righttxt = isset($_GET['righttxt']) ? stripSlashes($_GET['righttxt']) : "Bienvenus aux Championnats de France 2014 de Course d'Orientation";
            $righttxtsize = isset($_GET['righttxtsize']) ? intval($_GET['righttxtsize']) : 16;
            $righttxtcolor = isset($_GET['righttxtcolor']) ? stripSlashes($_GET['righttxtcolor']) : "000000";
            $righthtml = isset($_GET['righthtml']) ? stripSlashes($_GET['righthtml']) : "exemple.html";
            $rightfixedlines = isset($_GET['rightfixedlines']) ? intval($_GET['rightfixedlines']) : 3;
            $rightscrolledlines = isset($_GET['rightscrolledlines']) ? intval($_GET['rightscrolledlines']) : 3;
            $rightscrolltime = isset($_GET['rightscrolltime']) ? intval($_GET['rightscrolltime']) : 3;
            $rightscrollbeforetime = isset($_GET['rightscrollbeforetime']) ? intval($_GET['rightscrollbeforetime']) : 3;
            $rightscrollaftertime = isset($_GET['rightscrollaftertime']) ? intval($_GET['rightscrollaftertime']) : 3;
            $rightupdateduration = isset($_GET['rightupdateduration']) ? intval($_GET['rightupdateduration']) : 3;

            $title = isset($_GET['title']) ? stripSlashes($_GET['title']) : "no title";
            
            $chkall = isset($_GET['chkall']) ? $_GET['chkall'] : null;

            $res = mysql_query("SELECT rcid FROM resultscreen WHERE rcid=$rcid AND sid=$sid");
            if (mysql_num_rows($res) > 0)
            {
                $now = time();
                
                $str = "refresh=$now, ";
                $str = $str."cid='".$cid."', ";
                $str = $str."screenmode='".$screenmode."', ";

                $str = $str."title='".addSlashes($title)."', ";
                $str = $str."titlesize='".$titlesize."', ";
                $str = $str."titlecolor='".addSlashes($titlecolor)."', ";
                $str = $str."subtitle='".addSlashes($subtitle)."', ";
                $str = $str."subtitlesize='".$subtitlesize."', ";
                $str = $str."subtitlecolor='".addSlashes($subtitlecolor)."', ";
                $str = $str."titleleftpict='".addSlashes($titleleftpict)."', ";
                $str = $str."titlerightpict='".addSlashes($titlerightpict)."', ";

                $str = $str."fullcontent='".$fullcontent."', ";
                $str = $str."fullpict='".addSlashes($fullpict)."', ";
                $str = $str."fulltxt='".addSlashes($fulltxt)."', ";
                $str = $str."fulltxtsize='".$fulltxtsize."', ";
                $str = $str."fulltxtcolor='".addSlashes($fulltxtcolor)."', ";
                $str = $str."fullhtml='".addSlashes($fullhtml)."', ";
                $str = $str."fullfixedlines='".$fullfixedlines."', ";
                $str = $str."fullscrolledlines='".$fullscrolledlines."', ";
                $str = $str."fullscrolltime='".$fullscrolltime."', ";
                $str = $str."fullscrollbeforetime='".$fullscrollbeforetime."', ";
                $str = $str."fullscrollaftertime='".$fullscrollaftertime."', ";
                $str = $str."fullupdateduration='".$fullupdateduration."', ";


                $str = $str."leftcontent='".$leftcontent."', ";
                $str = $str."leftpict='".addSlashes($leftpict)."', ";
                $str = $str."lefttxt='".addSlashes($lefttxt)."', ";
                $str = $str."lefttxtsize='".$lefttxtsize."', ";
                $str = $str."lefttxtcolor='".addSlashes($lefttxtcolor)."', ";
                $str = $str."lefthtml='".addSlashes($lefthtml)."', ";
                $str = $str."leftfixedlines='".$leftfixedlines."', ";
                $str = $str."leftscrolledlines='".$leftscrolledlines."', ";
                $str = $str."leftscrolltime='".$leftscrolltime."', ";
                $str = $str."leftscrollbeforetime='".$leftscrollbeforetime."', ";
                $str = $str."leftscrollaftertime='".$leftscrollaftertime."', ";
                $str = $str."leftupdateduration='".$leftupdateduration."', ";

                $str = $str."rightcontent='".$rightcontent."', ";
                $str = $str."rightpict='".addSlashes($rightpict)."', ";
                $str = $str."righttxt='".addSlashes($righttxt)."', ";
                $str = $str."righttxtsize='".$righttxtsize."', ";
                $str = $str."righttxtcolor='".addSlashes($righttxtcolor)."', ";
                $str = $str."righthtml='".addSlashes($righthtml)."', ";
                $str = $str."rightfixedlines='".$rightfixedlines."', ";
                $str = $str."rightscrolledlines='".$rightscrolledlines."', ";
                $str = $str."rightscrolltime='".$rightscrolltime."', ";
                $str = $str."rightscrollbeforetime='".$rightscrollbeforetime."', ";
                $str = $str."rightscrollaftertime='".$rightscrollaftertime."', ";
                $str = $str."rightupdateduration='".$rightupdateduration."' ";

                $sql = "UPDATE resultscreen SET $str WHERE rcid=$rcid AND sid=$sid";
                $res = mysql_query($sql);
            
                //-------- check all management ---------
                
                $now = time();
                $str = "refresh=$now, ";
                if ($chkall !== null)
                {
                    foreach ($chkall as $i => $v)
                    {
                        
                        if ($v=="cid") $str = $str."cid='".$cid."', ";
                        if ($v=="screenmode") $str = $str."screenmode='".$screenmode."', ";
        
        
                        if ($v=="title") $str = $str."title='".addSlashes($title)."', ";
                        if ($v=="title") $str = $str."titlesize='".$titlesize."', ";
                        if ($v=="title") $str = $str."titlecolor='".addSlashes($titlecolor)."', ";
                        
                        if ($v=="subtitle") $str = $str."subtitle='".addSlashes($subtitle)."', ";
                        if ($v=="subtitle") $str = $str."subtitlesize='".$subtitlesize."', ";
                        if ($v=="subtitle") $str = $str."subtitlecolor='".addSlashes($subtitlecolor)."', ";
                        
                        if ($v=="titleleftpict") $str = $str."titleleftpict='".addSlashes($titleleftpict)."', ";
                        if ($v=="titlerightpict") $str = $str."titlerightpict='".addSlashes($titlerightpict)."', ";
        
        
                        if ($v=="fullcontent") $str = $str."fullcontent='".$fullcontent."', ";
                        if ($v=="fullpict") $str = $str."fullpict='".addSlashes($fullpict)."', ";
                        if ($v=="fulltxt") $str = $str."fulltxt='".addSlashes($fulltxt)."', ";
                        if ($v=="fulltxt") $str = $str."fulltxtsize='".$fulltxtsize."', ";
                        if ($v=="fulltxt") $str = $str."fulltxtcolor='".addSlashes($fulltxtcolor)."', ";
                        if ($v=="fullhtml") $str = $str."fullhtml='".addSlashes($fullhtml)."', ";
                        if ($v=="fullfixedlines") $str = $str."fullfixedlines='".$fullfixedlines."', ";
                        if ($v=="fullscrolledlines") $str = $str."fullscrolledlines='".$fullscrolledlines."', ";
                        if ($v=="fullscrolltime") $str = $str."fullscrolltime='".$fullscrolltime."', ";
                        if ($v=="fullscrollbeforetime") $str = $str."fullscrollbeforetime='".$fullscrollbeforetime."', ";
                        if ($v=="fullscrollaftertime") $str = $str."fullscrollaftertime='".$fullscrollaftertime."', ";
                        if ($v=="fullupdateduration") $str = $str."fullupdateduration='".$fullupdateduration."', ";
        
                        if ($v=="leftcontent") $str = $str."leftcontent='".$leftcontent."', ";
                        if ($v=="leftpict") $str = $str."leftpict='".addSlashes($leftpict)."', ";
                        if ($v=="lefttxt") $str = $str."lefttxt='".addSlashes($lefttxt)."', ";
                        if ($v=="lefttxt") $str = $str."lefttxtsize='".$lefttxtsize."', ";
                        if ($v=="lefttxt") $str = $str."lefttxtcolor='".addSlashes($lefttxtcolor)."', ";
                        if ($v=="lefthtml") $str = $str."lefthtml='".addSlashes($lefthtml)."', ";
                        if ($v=="leftfixedlines") $str = $str."leftfixedlines='".$leftfixedlines."', ";
                        if ($v=="leftscrolledlines") $str = $str."leftscrolledlines='".$leftscrolledlines."', ";
                        if ($v=="leftscrolltime") $str = $str."leftscrolltime='".$leftscrolltime."', ";
                        if ($v=="leftscrollbeforetime") $str = $str."leftscrollbeforetime='".$leftscrollbeforetime."', ";
                        if ($v=="leftscrollaftertime") $str = $str."leftscrollaftertime='".$leftscrollaftertime."', ";
                        if ($v=="leftupdateduration") $str = $str."leftupdateduration='".$leftupdateduration."', ";
        
                        if ($v=="rightcontent") $str = $str."rightcontent='".$rightcontent."', ";
                        if ($v=="rightpict") $str = $str."rightpict='".addSlashes($rightpict)."', ";
                        if ($v=="righttxt") $str = $str."righttxt='".addSlashes($righttxt)."', ";
                        if ($v=="righttxt") $str = $str."righttxtsize='".$righttxtsize."', ";
                        if ($v=="righttxt") $str = $str."righttxtcolor='".addSlashes($righttxtcolor)."', ";
                        if ($v=="righthtml") $str = $str."righthtml='".addSlashes($righthtml)."', ";
                        if ($v=="rightfixedlines") $str = $str."rightfixedlines='".$rightfixedlines."', ";
                        if ($v=="rightscrolledlines") $str = $str."rightscrolledlines='".$rightscrolledlines."', ";
                        if ($v=="rightscrolltime") $str = $str."rightscrolltime='".$rightscrolltime."', ";
                        if ($v=="rightscrollbeforetime") $str = $str."rightscrollbeforetime='".$rightscrollbeforetime."', ";
                        if ($v=="rightscrollaftertime") $str = $str."rightscrollaftertime='".$rightscrollaftertime."', ";
                        if ($v=="rightupdateduration") $str = $str."rightupdateduration='".$rightupdateduration."' ";
        
                    } // for each
                    $str = rtrim($str,", ");
                    $sql = "UPDATE resultscreen SET $str WHERE rcid=$rcid";
                    $res = mysql_query($sql);
                }  // chk all != null
            } // mysql_num_rows>0
        } //rcid and cid defined
    } // update


   //================================== display screens configs================================================

    if (isset($_GET['rcid'])) 
    {
        $rcid = intval($_GET['rcid']);
    
        $sql = "SELECT sid,title FROM resultscreen WHERE rcid=$rcid";
        $res = mysql_query($sql);
        $n=mysql_num_rows($res);

        if ($n < NB_SCREEN)
        {
            print "screen:display screen config";

            //$sql = "DELETE FROM resultscreen WHERE rcid=$rcid";
            //$res = mysql_query($sql);        
            //for ($i=1; $i<=NB_SCREEN; $i++)
            if ($n<1)
            {
                $n=1;
            }
            for ($i=$n; $i<=NB_SCREEN; $i++)
            {
                AddNewScreen($rcid,$i);    
            }
        }
        
        $tablecid=array();

        $sql = "SELECT * FROM resultscreen WHERE rcid=$rcid";
        $res = mysql_query($sql);
        $n=mysql_num_rows($res);
        while ($r = mysql_fetch_array($res))
        {
            $sid=$r['sid'];
            $cid=$r['cid'];
            $title=stripslashes($r['title']);
            $titlesize=$r['titlesize'];
            $titlecolor=$r['titlecolor'];
            $subtitle=stripslashes($r['subtitle']);
            $subtitlesize=$r['subtitlesize'];
            $subtitlecolor=$r['subtitlecolor'];
            $titleleftpict=$r['titleleftpict'];
            $titlerightpict=$r['titlerightpict'];
            $screenmode=$r['screenmode'];

            $fullcontent=$r['fullcontent'];
            $fullpict=$r['fullpict'];
            $fulltxt=stripslashes($r['fulltxt']);
            $fullhtml=$r['fullhtml'];

            $leftcontent=$r['leftcontent'];
            $leftpict=$r['leftpict'];
            $lefttxt=stripslashes($r['lefttxt']);
            $lefthtml=$r['lefthtml'];

            $rightcontent=$r['rightcontent'];
            $rightpict=$r['rightpict'];
            $righttxt=stripslashes($r['righttxt']);
            $righthtml=$r['righthtml'];

            $fullclasses=GetClassesAndEntries($rcid, $cid, $sid,1);
            $leftclasses=GetClassesAndEntries($rcid, $cid, $sid,1);
            $rightclasses=GetClassesAndEntries($rcid, $cid, $sid,2);

            print "<tr>\n";
            print "<td>$sid</td>\n";
            print "<td><img src='img/ecran.png' title='View' onclick='ViewScreen($sid);'></img>";
            print "</td>\n";
            print "<td id=\"idtimestamp$sid\" class=\"\">";
            print "</td>\n";
            print "<td><img src='img/edit.png' title='Edit' onclick='EditScreen($rcid,$sid);'></img></td>\n";

            $sqlcname = "SELECT name FROM mopcompetition WHERE cid=$cid";
            $rescname = mysql_query($sqlcname);
            if($rcname = mysql_fetch_array($rescname))
            {
              $cname=$rcname['name'];
            }
            $tablecid[$cid]=$cname;
            print "<td>$cname</td>\n";
            print "<td class='screen_title'>$title</td>\n";
            print "<td>$subtitle</td>\n";
            print "<td>$titleleftpict</td>\n";
            print "<td>$titlerightpict</td>\n";
            
            switch ($screenmode) {
                case 1:
                    print "<td class='screen_screenmode'><img src='img/ecranlarge.png' title='Full screen'/></td>\n";
                    $bgcolfull="";
                    $bgcoldbl=" bgcolor=LightGrey";
                    break;
                case 2:
                    print "<td class='screen_screenmode'><img src='img/ecran.png' title='2 panels'/><img src='img/ecran.png' title='2 panels'/></td>\n";
                    $bgcolfull=" bgcolor=LightGrey";
                    $bgcoldbl="";
                    break;
            }
            
            switch ($fullcontent) {
                case 1:
                    print "<td class='screen_fullcontent' $bgcolfull><img src='img/pict.png' title='Picture'/></td>\n";
                    print "<td $bgcolfull>$fullpict</td>\n";
                    break;
                case 2:
                    print "<td class='screen_fullcontent' $bgcolfull><img src='img/txt.png' title='Text'/></td>\n";
                    print "<td $bgcolfull>$fulltxt</td>\n";
                    break;
                case 3:
                    print "<td class='screen_fullcontent' $bgcolfull><img src='img/htm.png' title='HTML'/></td>\n";
                    print "<td $bgcolfull>$fullhtml</td>\n";
                    break;
                case 4:
                    print "<td class='screen_fullcontent' $bgcolfull><img src='img/podium.png' title='Relay Results'/></td>\n";
                    print "<td $bgcolfull>$fullclasses</td>\n";
                    break;
            }

            switch ($leftcontent) {
                case 1:
                    print "<td class='screen_leftcontent' $bgcoldbl><img src='img/pict.png' title='Picture'/></td>\n";
                    print "<td $bgcoldbl>$leftpict</td>\n";
                    break;
                case 2:
                    print "<td class='screen_leftcontent' $bgcoldbl><img src='img/txt.png' title='Text'/></td>\n";
                    print "<td $bgcoldbl>$lefttxt</td>\n";
                    break;
                case 3:
                    print "<td class='screen_leftcontent' $bgcoldbl><img src='img/htm.png' title='HTML'/></td>\n";
                    print "<td $bgcoldbl>$lefthtml</td>\n";
                    break;
                case 4:
                    print "<td class='screen_leftcontent' $bgcoldbl><a href=screenclasses.php?rcid=$rcid&cid=$cid&sid=$sid&panel=1&ret=1><img src='img/start.png' title='Start'/></a></td>\n";
                    print "<td $bgcoldbl>$leftclasses</td>\n";
                    break;
                case 5:
                    print "<td class='screen_leftcontent' $bgcoldbl><a href=screenclasses.php?rcid=$rcid&cid=$cid&sid=$sid&panel=1&ret=1><img src='img/podium.png' title='Results'/></a></td>\n";
                    print "<td $bgcoldbl>$leftclasses</td>\n";
                    break;
            }

            switch ($rightcontent) {
                case 1:
                    print "<td class='screen_rightcontent' $bgcoldbl><img src='img/pict.png' title='Picture'/></td>\n";
                    print "<td $bgcoldbl>$rightpict</td>\n";
                    break;
                case 2:
                    print "<td class='screen_rightcontent' $bgcoldbl><img src='img/txt.png' title='Text'/></td>\n";
                    print "<td $bgcoldbl>$righttxt</td>\n";
                    break;
                case 3:
                    print "<td class='screen_rightcontent' $bgcoldbl><img src='img/htm.png' title='HTML'/></td>\n";
                    print "<td $bgcoldbl>$righthtml</td>\n";
                    break;
                case 4:
                    print "<td class='screen_rightcontent' $bgcoldbl><a href=screenclasses.php?rcid=$rcid&cid=$cid&sid=$sid&panel=2&ret=1><img src='img/start.png' title='Start'/></a></td>\n";
                    print "<td $bgcoldbl>$rightclasses</td>\n";
                    break;
                case 5:
                    print "<td class='screen_rightcontent' $bgcoldbl><a href=screenclasses.php?rcid=$rcid&cid=$cid&sid=$sid&panel=2&ret=1><img src='img/podium.png' title='Results'/></a></td>\n";
                    print "<td $bgcoldbl>$rightclasses</td>\n";
                    break;
            }

            print "</tr>\n";
        } 
        print "</table>\n";

   //================================== display classes statistics ================================================
        
        print "<br/>\n";
        print "<a href='screenconfig.php'>Back to main page</a>&nbsp;&nbsp;&nbsp;";
        print "<br/>\n";
        foreach ($tablecid as $cid => $cname)
        {
			// determines number of entries
			$sql3 = "SELECT COUNT(*) FROM mopcompetitor WHERE cid=$cid";
			$res3 = mysql_query($sql3);
			if (mysql_num_rows($res3) > 0)
			{
				if ($r3 = mysql_fetch_array($res3))
				{
				  $totalentry=$r3[0];
				}
			}
				
            print "<h2>$cname ($totalentry)</h2>\n";
            print "<table border>\n";

            $sql = "SELECT name,id FROM mopclass WHERE mopclass.cid=$cid ORDER BY name";
            $res = mysql_query($sql);
            if (mysql_num_rows($res) > 0)
            {
                print "<tr>\n";
                print "<th>Class</th>\n";
                print "<th>Start List</th>\n";
                print "<th>Results</th>\n";
                print "<th colspan=2>Starts</th>\n";
                print "<th colspan=2>Entries</th>\n";
                print "<th>Done</th>\n";
                print "<th>&nbsp;</th>\n";
                print "</tr>\n";
                
                while ($r = mysql_fetch_array($res))
                {
                    print "<tr>\n";
                    $classname=$r['name'];
                    $classid=$r['id'];
                    print "<td class='class_class'>$classname</td>\n";

                    //-- Start List
                    $displaySL="";
                    $displayR="";
                    $sql2 = "SELECT rc.sid, rc.panel, rs.screenmode, rs.leftcontent, rs.rightcontent FROM resultclass rc, resultscreen rs WHERE ";
                        $sql2 = $sql2."rs.rcid=rc.rcid AND ";
                        $sql2 = $sql2."rs.cid=rc.cid AND ";
                        $sql2 = $sql2."rs.sid=rc.sid AND ";
                        $sql2 = $sql2."rc.rcid=$rcid AND ";
                        $sql2 = $sql2."rc.cid=$cid AND ";
                        $sql2 = $sql2."rc.id=$classid;";
                        $res2 = mysql_query($sql2);
                    if (mysql_num_rows($res2) > 0)
                    {
                        while ($r2 = mysql_fetch_array($res2))
                        {
                          $screen2=$r2['sid'];
                          $panel2=($r2['panel']==1?"L":"R");
                          $screenmode2=$r2['screenmode'];
                          $leftcontent2=$r2['leftcontent'];
                          $rightcontent2=$r2['rightcontent'];
                          //-- Start List panel L
                          if (($screenmode2==2)&&($panel2=="L")&&($leftcontent2==4))
                          {
                              if (strlen($displaySL)>0)
                              {
                                $displaySL=$displaySL." ".$screen2.$panel2;
                              }
                              else
                              {
                                $displaySL=$screen2.$panel2;
                              }
                          }
                          //-- Start List panel R
                          if (($screenmode2==2)&&($panel2=="R")&&($rightcontent2==4))
                          {
                              if (strlen($displaySL)>0)
                              {
                                $displaySL=$displaySL." ".$screen2.$panel2;
                              }
                              else
                              {
                                $displaySL=$screen2.$panel2;
                              }
                          }

                          //-- Results panel L
                          if (($screenmode2==2)&&($panel2=="L")&&($leftcontent2==5))
                          {
                              if (strlen($displayR)>0)
                              {
                                $displayR=$displayR." ".$screen2.$panel2;
                              }
                              else
                              {
                                $displayR=$screen2.$panel2;
                              }
                          }
                          //-- Start List panel R
                          if (($screenmode2==2)&&($panel2=="R")&&($rightcontent2==5))
                          {
                              if (strlen($displayR)>0)
                              {
                                $displayR=$displayR." ".$screen2.$panel2;
                              }
                              else
                              {
                                $displayR=$screen2.$panel2;
                              }
                          }

                        }
                    }
                    print "<td>$displaySL</td>\n";
                    //-- Results
                    print "<td>$displayR</td>\n";


                    //-- 1st Start
                    $heuremin="00:00:00";
                    $heuremax="00:00:00";
                    date_default_timezone_set('UTC');
                    $sql2 = "SELECT MIN(st),MAX(st) FROM mopcompetitor WHERE cid=$cid AND cls=$classid";
                    $res2 = mysql_query($sql2);
                    if (mysql_num_rows($res2) > 0)
                    {
                        if ($r2 = mysql_fetch_array($res2))
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
                    $res2 = mysql_query($sql2);
                    if (mysql_num_rows($res2) > 0)
                    {
                        if ($r2 = mysql_fetch_array($res2))
                        {
                          $nentry=$r2[0];
                        }
                    }
					$pctentries=round(1000*$nentry/$totalentry)/10.0;
                    print "<td class='class_entries'>$nentry</td>\n";
                    print "<td class='class_entries'>$pctentries %</td>\n";

                    //-- Done
                    $n=0;
                    $sql2 = "SELECT COUNT(*) FROM mopcompetitor WHERE cid=$cid AND cls=$classid AND tstat>0";
                    $res2 = mysql_query($sql2);
                    if (mysql_num_rows($res2) > 0)
                    {
                        if ($r2 = mysql_fetch_array($res2))
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
    print "<a href=screen.php?rcid=$rcid>Refresh</a>";
?>
    </body>
</html>
