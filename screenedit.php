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
    if (($ipnb[0]!='192')||($ipnb[1]!='168')||($ipnb[2]!='0')||($ipnb[3]=='20'))
    {
        header("Location: http://192.168.0.10");
        die();
    }
    include_once('functions.php');
    include_once('screenfunctions.php');

    session_start();
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>CFCO 2014 Screen edit</title>
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
            
            function ManageFull(sel)
            {
                sel=parseInt(sel);
                document.getElementById('fullcontent1').disabled=true;
                document.getElementById('fullcontent1txt').disabled=true;
                document.getElementById('fullcontent1button').disabled=true;

                document.getElementById('fullcontent2').disabled=true;
                document.getElementById('fulltxtsize').disabled=true;
                document.getElementById('fulltxtcolor').disabled=true;

                document.getElementById('fullcontent3').disabled=true;
                document.getElementById('fullcontent3txt').disabled=true;
                document.getElementById('fullcontent3button').disabled=true;

				document.getElementById('fullcontent4').disabled=true;
				document.getElementById('fullresultsbutton').disabled=true;
				document.getElementById('fullfirstline').disabled=true;
				document.getElementById('fullfixedlines').disabled=true;
				document.getElementById('fullscrolledlines').disabled=true;
				document.getElementById('fullscrolltime').disabled=true;
				document.getElementById('fullscrollbeforetime').disabled=true;
				document.getElementById('fullscrollaftertime').disabled=true;
				document.getElementById('fullupdateduration').disabled=true;

                switch(sel) {
                    case 1 :
                        document.getElementById('fullcontent1').disabled=false;
                        document.getElementById('fullcontent1txt').disabled=false;
                        document.getElementById('fullcontent1button').disabled=false;
                        break;
                    case 2 :
                        document.getElementById('fullcontent2').disabled=false;
                        document.getElementById('fulltxtsize').disabled=false;
                        document.getElementById('fulltxtcolor').disabled=false;
                        break;
                    case 3 :
                        document.getElementById('fullcontent3').disabled=false;
                        document.getElementById('fullcontent3txt').disabled=false;
                        document.getElementById('fullcontent3button').disabled=false;
                        break;
                    case 4 :
                        document.getElementById('fullcontent4').disabled=false;
                        document.getElementById('fullresultsbutton').disabled=false;
						document.getElementById('fullfirstline').disabled=false;
						document.getElementById('fullfixedlines').disabled=false;
						document.getElementById('fullscrolledlines').disabled=false;
						document.getElementById('fullscrolltime').disabled=false;
						document.getElementById('fullscrollbeforetime').disabled=false;
						document.getElementById('fullscrollaftertime').disabled=false;
						document.getElementById('fullupdateduration').disabled=false;
                        break;
                }
            }
            
            function ManageLeft(sel)
            {
                sel=parseInt(sel);
                document.getElementById('leftcontent1').disabled=true;
                document.getElementById('leftcontent1txt').disabled=true;
                document.getElementById('leftcontent1button').disabled=true;
                document.getElementById('leftcontent2').disabled=true;
                document.getElementById('lefttxtsize').disabled=true;
                document.getElementById('lefttxtcolor').disabled=true;
                document.getElementById('leftcontent3').disabled=true;
                document.getElementById('leftcontent3txt').disabled=true;
                document.getElementById('leftcontent3button').disabled=true;
                document.getElementById('leftcontent4').disabled=true;
                document.getElementById('leftstartlistbutton').disabled=true;
                document.getElementById('leftcontent5').disabled=true;
                document.getElementById('leftresultsbutton').disabled=true;
				document.getElementById('leftfirstline').disabled=true;
				document.getElementById('leftfixedlines').disabled=true;
				document.getElementById('leftscrolledlines').disabled=true;
				document.getElementById('leftscrolltime').disabled=true;
				document.getElementById('leftscrollbeforetime').disabled=true;
				document.getElementById('leftscrollaftertime').disabled=true;
				document.getElementById('leftupdateduration').disabled=true;
                switch(sel) {
                    case 1 :
                        document.getElementById('leftcontent1').disabled=false;
                        document.getElementById('leftcontent1txt').disabled=false;
                        document.getElementById('leftcontent1button').disabled=false;
                        break;
                    case 2 :
                        document.getElementById('leftcontent2').disabled=false;
                        document.getElementById('lefttxtsize').disabled=false;
                        document.getElementById('lefttxtcolor').disabled=false;
                        break;
                    case 3 :
                        document.getElementById('leftcontent3').disabled=false;
                        document.getElementById('leftcontent3txt').disabled=false;
                        document.getElementById('leftcontent3button').disabled=false;
                        break;
                    case 4 :
                        document.getElementById('leftcontent4').disabled=false;
                        document.getElementById('leftstartlistbutton').disabled=false;
						document.getElementById('leftfirstline').disabled=false;
						document.getElementById('leftfixedlines').disabled=false;
						document.getElementById('leftscrolledlines').disabled=false;
						document.getElementById('leftscrolltime').disabled=false;
						document.getElementById('leftscrollbeforetime').disabled=false;
						document.getElementById('leftscrollaftertime').disabled=false;
                        break;
                    case 5 :
                        document.getElementById('leftcontent5').disabled=false;
                        document.getElementById('leftresultsbutton').disabled=false;
						document.getElementById('leftfirstline').disabled=false;
						document.getElementById('leftfixedlines').disabled=false;
						document.getElementById('leftscrolledlines').disabled=false;
						document.getElementById('leftscrolltime').disabled=false;
						document.getElementById('leftscrollbeforetime').disabled=false;
						document.getElementById('leftscrollaftertime').disabled=false;
						document.getElementById('leftupdateduration').disabled=false;
                        break;
                }
            }

            function ManageRight(sel)
            {
                sel=parseInt(sel);
                document.getElementById('rightcontent1').disabled=true;
                document.getElementById('rightcontent1txt').disabled=true;
                document.getElementById('rightcontent1button').disabled=true;
                document.getElementById('rightcontent2').disabled=true;
                document.getElementById('righttxtsize').disabled=true;
                document.getElementById('righttxtcolor').disabled=true;
                document.getElementById('rightcontent3').disabled=true;
                document.getElementById('rightcontent3txt').disabled=true;
                document.getElementById('rightcontent3button').disabled=true;
                document.getElementById('rightcontent4').disabled=true;
                document.getElementById('rightstartlistbutton').disabled=true;
                document.getElementById('rightcontent5').disabled=true;
                document.getElementById('rightresultsbutton').disabled=true;
				document.getElementById('rightfirstline').disabled=true;
				document.getElementById('rightfixedlines').disabled=true;
				document.getElementById('rightscrolledlines').disabled=true;
				document.getElementById('rightscrolltime').disabled=true;
				document.getElementById('rightscrollbeforetime').disabled=true;
				document.getElementById('rightscrollaftertime').disabled=true;
				document.getElementById('rightupdateduration').disabled=true;
                switch(sel) {
                    case 1 :
                        document.getElementById('rightcontent1').disabled=false;
                        document.getElementById('rightcontent1txt').disabled=false;
                        document.getElementById('rightcontent1button').disabled=false;
                        break;
                    case 2 :
                        document.getElementById('rightcontent2').disabled=false;
                        document.getElementById('righttxtsize').disabled=false;
                        document.getElementById('righttxtcolor').disabled=false;
                        break;
                    case 3 :
                        document.getElementById('rightcontent3').disabled=false;
                        document.getElementById('rightcontent3txt').disabled=false;
                        document.getElementById('rightcontent3button').disabled=false;
                        break;
                    case 4 :
                        document.getElementById('rightcontent4').disabled=false;
                        document.getElementById('rightstartlistbutton').disabled=false;
						document.getElementById('rightfirstline').disabled=false;
						document.getElementById('rightfixedlines').disabled=false;
						document.getElementById('rightscrolledlines').disabled=false;
						document.getElementById('rightscrolltime').disabled=false;
						document.getElementById('rightscrollbeforetime').disabled=false;
						document.getElementById('rightscrollaftertime').disabled=false;
                        break;
                    case 5 :
                        document.getElementById('rightcontent5').disabled=false;
                        document.getElementById('rightresultsbutton').disabled=false;
						document.getElementById('rightfirstline').disabled=false;
						document.getElementById('rightfixedlines').disabled=false;
						document.getElementById('rightscrolledlines').disabled=false;
						document.getElementById('rightscrolltime').disabled=false;
						document.getElementById('rightscrollbeforetime').disabled=false;
						document.getElementById('rightscrollaftertime').disabled=false;
						document.getElementById('rightupdateduration').disabled=false;
                        break;
                }
            }
            
            function OpenFile(id, idtxt)
            {
                document.getElementById(id).click();
                document.getElementById(idtxt).value=document.getElementById(id).value;
            }
            
            function UpdateFile(id, idtxt)
            {
                document.getElementById(idtxt).value=document.getElementById(id).value;
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
    ConnectToDB();

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
                mysql_query($sql);
            }
        }
        
        if ($action == "updateclasses")
        {
            $cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
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
        
        $configname = GetConfigurationName($rcid);
        print "<h2>$configname, Screen #$sid</h2>\n";
        
        $sql = "SELECT * FROM resultscreen WHERE rcid=$rcid AND sid=$sid";
        $res = mysql_query($sql);
        
        if (mysql_num_rows($res) > 0)
        {
        
            $r = mysql_fetch_array($res);
            $cid=$r['cid'];
			if (($action == "reload")||($action == "updateclasses"))
			{
				$cid = isset($_GET['cid']) ? intval($_GET['cid']) : $cid;
			}
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
            $fulltxtsize=$r['fulltxtsize'];
            $fulltxtcolor=$r['fulltxtcolor'];
            $fullhtml=$r['fullhtml'];
            $fullfirstline=$r['fullfirstline'];
            $fullfixedlines=$r['fullfixedlines'];
            $fullscrolledlines=$r['fullscrolledlines'];
            $fullscrolltime=$r['fullscrolltime'];
            $fullscrollbeforetime=$r['fullscrollbeforetime'];
            $fullscrollaftertime=$r['fullscrollaftertime'];
            $fullupdateduration=$r['fullupdateduration'];

            $leftcontent=$r['leftcontent'];
            $leftpict=$r['leftpict'];
            $lefttxt=stripslashes($r['lefttxt']);
            $lefttxtsize=$r['lefttxtsize'];
            $lefttxtcolor=$r['lefttxtcolor'];
            $lefthtml=$r['lefthtml'];
            $leftfirstline=$r['leftfirstline'];
            $leftfixedlines=$r['leftfixedlines'];
            $leftscrolledlines=$r['leftscrolledlines'];
            $leftscrolltime=$r['leftscrolltime'];
            $leftscrollbeforetime=$r['leftscrollbeforetime'];
            $leftscrollaftertime=$r['leftscrollaftertime'];
            $leftupdateduration=$r['leftupdateduration'];

            $rightcontent=$r['rightcontent'];
            $rightpict=$r['rightpict'];
            $righttxt=stripslashes($r['righttxt']);
            $righttxtsize=$r['righttxtsize'];
            $righttxtcolor=$r['righttxtcolor'];
            $righthtml=$r['righthtml'];
            $rightfixedlines=$r['rightfixedlines'];
            $rightfirstline=$r['rightfirstline'];
            $rightscrolledlines=$r['rightscrolledlines'];
            $rightscrolltime=$r['rightscrolltime'];
            $rightscrollbeforetime=$r['rightscrollbeforetime'];
            $rightscrollaftertime=$r['rightscrollaftertime'];
            $rightupdateduration=$r['rightupdateduration'];

            $fullclasses=GetClasses($rcid, $cid, $sid,1);
            $leftclasses=GetClasses($rcid, $cid, $sid,1);
            $rightclasses=GetClasses($rcid, $cid, $sid,2);

            print "<form name='screenedit' method=GET action='screen.php'>\n";
            print "<input type='hidden' name='action' value='update'>\n";
            print "<input type='hidden' name='rcid' value='$rcid'>\n";
            print "<input type='hidden' name='sid' value='$sid'>\n";

            //----------------- screen global ---------------------- 
            print "<table>\n";
            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='cid'></input></td>\n";
            print "<td>Competition :</td>\n";
            print "<td><select name='cid' id='cid' size=1 onchange='Reload(".$rcid.",".$sid.");'>";
            $sql = "SELECT name, cid FROM mopcompetition";
            $res = mysql_query($sql);
            while ($r = mysql_fetch_array($res))
            {
                $competname=$r['name'];
                $competid=$r['cid'];
                if ($competid==$cid)
                {
                    print "<option value=$competid selected>$competname</option>";
                }
                else
                {
                    print "<option value=$competid>$competname</option>";
                }
            }
            print "</select></td>\n";
            print "</tr>\n";

            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='screenmode'></input></td>\n";
            print "<td>Mode :</td>\n";
            print "<td><select name='screenmode' size=1>\n";
            switch($screenmode) {
                case 1:
                    print "<option value='1' selected>Full screen</option>\n";
                    print "<option value='2'>Two panels</option>\n";
                    break;
                case 2:
                    print "<option value='1'>Full screen</option>\n";
                    print "<option value='2' selected>Two panels</option>\n";
                    break;
            }
            print "</select></td>\n";
            print "</tr>\n";

            print "</table>\n";

            //----------------- top of the screen ---------------------- 
            print "<hr>\n";
            print "<h4>Top</h4>\n";

            print "<table>\n";
            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='title'></input></td>\n";
            print "<td>Title :</td>\n";
            print '<td><input type="text" name="title" size=64 maxlength=120 value="'.$title.'"></td>';
            $str=NumericIntList("titlesize",1,32,$titlesize);
            print "<td>$str</td>\n";
            print '<td>Color : <input type="text" name="titlecolor" class="color" size=6 value="'.$titlecolor.'"></td>';
            print "</tr>\n";
            
            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='subtitle'></input></td>\n";
            print "<td>Subtitle : </td>\n";
            print '<td><input type="text" name="subtitle" size=64 maxlength=120 value="'.$subtitle.'"></td>';
            $str=NumericIntList("subtitlesize",1,32,$subtitlesize);
            print "<td>$str</td>\n";
            print '<td>Color : <input type="text" name="subtitlecolor" class="color" size=6 value="'.$subtitlecolor.'"></td>';
            print "</tr>\n";
            
            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='titleleftpict'></input></td>\n";
            print "<td>left picture</td>\n";
            print '<td><input type="text" name="titleleftpict" size=64 id="titleleftcontenttxt" value="'.$titleleftpict.'">';
            print '<input type="file" name="titleleftpictfile" size=64 id="titleleftcontent" onchange="UpdateFile(\'titleleftcontent\',\'titleleftcontenttxt\')" style="display: none;" accept="image/*" value="'.$titleleftpict.'"></td>';
            print '<td><input type="button" name="titleleftpictbutton" size=64 id="titleleftcontentbutton" value="Open" onclick="OpenFile(\'titleleftcontent\',\'titleleftcontenttxt\')"></td>';
            print "</tr>\n";
            
            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='titlerightpict'></input></td>\n";
            print "<td>Right picture</td>\n";
            print '<td><input type="text" name="titlerightpict" size=64 id="titlerightcontenttxt" value="'.$titlerightpict.'">';
            print '<input type="file" name="titlerightpictfile" size=64 id="titlerightcontent" onchange="UpdateFile(\'titlerightcontent\',\'titlerightcontenttxt\')" style="display: none;" accept="image/*" value="'.$titlerightpict.'"></td>';
            print '<td><input type="button" name="titlerightpictbutton" size=64 id="titlerightcontentbutton" value="Open" onclick="OpenFile(\'titlerightcontent\',\'titlerightcontenttxt\')"></td>';
            print "</tr>\n";
            
            print "</table>\n";

            //----------------- full screen ---------------------- 
            print "<hr>\n";
            print "<h4>Full screen</h4>\n";

            print "<table>\n";
            print "<tr>\n";
            print "<td>&nbsp;</td>\n";
            print "<td><input type='checkbox' name='chkall[]' value='fullcontent'></input></td>\n";
            print "</tr>\n";
            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='fullpict'></input></td>\n";
            print "<td><input type='radio' name='fullcontent' value='1' id='full1'>Picture</input></td>\n";
            print '<td><input type="text" name="fullpict" size=64 id="fullcontent1txt" value="'.$fullpict.'">';
            print '<input type="file" name="fullpictfile" size=64 id="fullcontent1" onchange="UpdateFile(\'fullcontent1\',\'fullcontent1txt\')" style="display: none;" accept="image/*" value="'.$fullpict.'"></td>';
            print '<td><input type="button" name="fullpictbutton" size=64 id="fullcontent1button" value="Open" onclick="OpenFile(\'fullcontent1\',\'fullcontent1txt\')"></td>';
            print "</tr>\n";
            
            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='fulltxt'></input></td>\n";
            print "<td><input type='radio' name='fullcontent' value='2' id='full2'>Text</input></td>\n";
            print '<td><textarea name="fulltxt" cols=64 rows=4 maxlength=500 id="fullcontent2">'.$fulltxt.'</textarea></td>';
            $str=NumericIntList("fulltxtsize",1,72,$fulltxtsize);
            print "<td>$str</td>\n";
            print '<td>Color : <input type="text" name="fulltxtcolor" id="fulltxtcolor" class="color" size=6 value="'.$fulltxtcolor.'"></td>';
            print "</tr>\n";

            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='fullhtml'></input></td>\n";
            print "<td><input type='radio' name='fullcontent' value='3' id='full3'>HTML</input></td>";
            print '<td><input type="text" name="fullhtml" size=64 id="fullcontent3txt" value="'.$fullhtml.'">';
            print '<input type="file" name="fullhtmlfile" size=64 id="fullcontent3" onchange="UpdateFile(\'fullcontent3\',\'fullcontent3txt\')" style="display: none;" accept="text/html" value="'.$fullhtml.'"></td>';
            print '<td><input type="button" name="fullhtmlbutton" size=64 id="fullcontent3button" value="Open" onclick="OpenFile(\'fullcontent3\',\'fullcontent3txt\')"></td>';
            print "</tr>\n";

            print "<tr>\n";
            print "<td>&nbsp;</td>\n";
            print "<td><input type='radio' name='fullcontent' value='4' id='full4'>Results</input>&nbsp;\n";
            print '<td><input type="text" name="fullresults" size=64 id="fullcontent4" readonly value="'.$fullclasses.'"></td>';
            print '<td><input type="button" name="fullresultsbutton" size=64 id="fullresultsbutton" value="Edit" onclick="EditClassesList('.$rcid.','.$cid.','.$sid.',1)"></td>';
            print "</tr>\n";

            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='fullfirstline'></input></td>\n";
            $str=NumericIntList("fullfirstline",1,999,$fullfirstline);
            print "<td>First line</td>\n";
            print "<td>$str</td>\n";
            print "</tr>\n";

            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='fullfixedlines'></input></td>\n";
            $str=NumericIntList("fullfixedlines",0,20,$fullfixedlines);
            print "<td>Fixed lines</td>\n";
            print "<td>$str</td>\n";
            print "</tr>\n";

            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='fullscrolledlines'></input></td>\n";
            $str=NumericIntList("fullscrolledlines",0,20,$fullscrolledlines);
            print "<td>Scrolled lines</td>\n";
            print "<td>$str</td>\n";
            print "</tr>\n";

            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='fullscrolltime'></input></td>\n";
            $str=NumericIntList("fullscrolltime",1,200,$fullscrolltime);
            print "<td>Scroll time</td>\n";
            print "<td>$str 1/10°s</td> \n";
            print "</tr>\n";

            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='fullscrollbeforetime'></input></td>\n";
            $str=NumericIntList("fullscrollbeforetime",1,200,$fullscrollbeforetime);
            print "<td>Before scroll time</td>\n";
            print "<td>$str 1/10°s</td> \n";
            print "</tr>\n";

            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='fullscrollaftertime'></input></td>\n";
            $str=NumericIntList("fullscrollaftertime",1,200,$fullscrollaftertime);
            print "<td>After scroll time</td>\n";
            print "<td>$str 1/10°s</td> \n";
            print "</tr>\n";

            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='fullupdateduration'></input></td>\n";
            $str=NumericIntList("fullupdateduration",1,200,$fullupdateduration);
            print "<td>Recent highlight</td>\n";
            print "<td>$str min</td> \n";
            print "</tr>\n";



            print "</table>\n";
            
            
            //----------------- left panel ---------------------- 
            print "<hr>\n";
            print "<h4>Left panel</h4>\n";

            print "<table>\n";

            print "<tr>\n";
            print "<td>&nbsp;</td>\n";
            print "<td><input type='checkbox' name='chkall[]' value='leftcontent'></input></td>\n";
            print "</tr>\n";
            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='leftpict'></input></td>\n";
            print "<td><input type='radio' name='leftcontent' value='1' id='left1'>Picture</input></td>\n";
            print '<td><input type="text" name="leftpict" size=64 id="leftcontent1txt" value="'.$leftpict.'">';
            print '<input type="file" name="leftpictfile" size=64 id="leftcontent1" onchange="UpdateFile(\'leftcontent1\',\'leftcontent1txt\')" style="display: none;" accept="image/*" value="'.$leftpict.'"></td>';
            print '<td><input type="button" name="leftpictbutton" size=64 id="leftcontent1button" value="Open" onclick="OpenFile(\'leftcontent1\',\'leftcontent1txt\')"></td>';
            print "</tr>\n";
            
            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='lefttxt'></input></td>\n";
            print "<td><input type='radio' name='leftcontent' value='2' id='left2'>Text</input></td>\n";
            print '<td><textarea name="lefttxt" cols=64 rows=4 maxlength=500 id="leftcontent2">'.$lefttxt.'</textarea></td>';
            $str=NumericIntList("lefttxtsize",1,72,$lefttxtsize);
            print "<td>$str</td>\n";
            print '<td>Color : <input type="text" name="lefttxtcolor" id="lefttxtcolor"class="color" size=6 value="'.$lefttxtcolor.'"></td>';
            print "</tr>\n";
            
            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='lefthtml'></input></td>\n";
            print "<td><input type='radio' name='leftcontent' value='3' id='left3'>HTML</input></td>\n";
            print '<td><input type="text" name="lefthtml" size=64 id="leftcontent3txt" value="'.$lefthtml.'">';
            print '<input type="file" name="lefthtmlfile" size=64 id="leftcontent3" onchange="UpdateFile(\'leftcontent3\',\'leftcontent3txt\')" style="display: none;" accept="text/html" value="'.$lefthtml.'"></td>';
            print '<td><input type="button" name="lefthtmlbutton" size=64 id="leftcontent3button" value="Open" onclick="OpenFile(\'leftcontent3\',\'leftcontent3txt\')"></td>';
            print "</tr>\n";
            
            print "<tr>\n";
            print "<td>&nbsp;</td>\n";
            print "<td><input type='radio' name='leftcontent' value='4' id='left4'>Start list</input></td>\n"; 
            print '<td><input type="text" name="leftstartlist" size=64 id="leftcontent4" readonly value="'.$leftclasses.'"></td>';
            print '<td><input type="button" name="leftstartlistbutton" size=64 id="leftstartlistbutton" value="Edit" onclick="EditClassesList('.$rcid.','.$cid.','.$sid.',1)"></td>';
            print "</tr>\n";
            
            print "<tr>\n";
            print "<td>&nbsp;</td>\n";
            print "<td><input type='radio' name='leftcontent' value='5' id='left5'>Results</input>&nbsp;\n";
            print '<td><input type="text" name="leftresults" size=64 id="leftcontent5" readonly value="'.$leftclasses.'"></td>';
            print '<td><input type="button" name="leftresultsbutton" size=64 id="leftresultsbutton" value="Edit" onclick="EditClassesList('.$rcid.','.$cid.','.$sid.',1)"></td>';
            print "</tr>\n";
            
            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='leftfirstline'></input></td>\n";
            $str=NumericIntList("leftfirstline",1,999,$leftfirstline);
            print "<td>First line</td>\n";
            print "<td>$str</td>\n";
            print "</tr>\n";

            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='leftfixedlines'></input></td>\n";
            $str=NumericIntList("leftfixedlines",0,20,$leftfixedlines);
            print "<td>Fixed lines</td>\n";
            print "<td>$str</td>\n";
            print "</tr>\n";

            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='leftscrolledlines'></input></td>\n";
            $str=NumericIntList("leftscrolledlines",0,20,$leftscrolledlines);
            print "<td>Scrolled lines</td>\n";
            print "<td>$str</td>\n";
            print "</tr>\n";

            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='leftscrolltime'></input></td>\n";
            $str=NumericIntList("leftscrolltime",1,200,$leftscrolltime);
            print "<td>Scroll time</td>\n";
            print "<td>$str 1/10°s</td> \n";
            print "</tr>\n";

            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='leftscrollbeforetime'></input></td>\n";
            $str=NumericIntList("leftscrollbeforetime",1,200,$leftscrollbeforetime);
            print "<td>Before scroll time</td>\n";
            print "<td>$str 1/10°s</td> \n";
            print "</tr>\n";

            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='leftscrollaftertime'></input></td>\n";
            $str=NumericIntList("leftscrollaftertime",1,200,$leftscrollaftertime);
            print "<td>After scroll time</td>\n";
            print "<td>$str 1/10°s</td> \n";
            print "</tr>\n";

            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='leftupdateduration'></input></td>\n";
            $str=NumericIntList("leftupdateduration",1,200,$leftupdateduration);
            print "<td>Recent highlight</td>\n";
            print "<td>$str min</td> \n";
            print "</tr>\n";

            print "</table>\n";

            //----------------- right panel ---------------------- 
            print "<hr>\n";
            print "<h4>Right panel</h4>\n";

            print "<table>\n";

            print "<tr>\n";
            print "<td>&nbsp;</td>\n";
            print "<td><input type='checkbox' name='chkall[]' value='rightcontent'></input></td>\n";
            print "</tr>\n";
            print "<tr>\n";
            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='rightpict'></input></td>\n";
            print "<td><input type='radio' name='rightcontent' value='1' id='right1'>Picture</input></td>\n";
            print '<td><input type="text" name="rightpict" size=64 id="rightcontent1txt" value="'.$rightpict.'">';
            print '<input type="file" name="rightpictfile" size=64 id="rightcontent1" onchange="UpdateFile(\'rightcontent1\',\'rightcontent1txt\')" style="display: none;" accept="image/*" value="'.$rightpict.'"></td>';
            print '<td><input type="button" name="rightpictbutton" size=64 id="rightcontent1button" value="Open" onclick="OpenFile(\'rightcontent1\',\'rightcontent1txt\')"></td>';
            print "</tr>\n";
            
            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='righttxt'></input></td>\n";
            print "<td><input type='radio' name='rightcontent' value='2' id='right2'>Text</input></td>\n";
            print '<td><textarea name="righttxt" cols=64 rows=4 maxlength=500 id="rightcontent2">'.$righttxt.'</textarea></td>';
            $str=NumericIntList("righttxtsize",1,72,$righttxtsize);
            print "<td>$str</td>\n";
            print '<td>Color : <input type="text" name="righttxtcolor" id="righttxtcolor" class="color" size=6 value="'.$righttxtcolor.'"></td>';
            print "</tr>\n";
            
            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='righthtml'></input></td>\n";
            print "<td><input type='radio' name='rightcontent' value='3' id='right3'>HTML</input></td>\n";
            print '<td><input type="text" name="righthtml" size=64 id="rightcontent3txt" value="'.$righthtml.'">';
            print '<input type="file" name="righthtmlfile" size=64 id="rightcontent3" onchange="UpdateFile(\'rightcontent3\',\'rightcontent3txt\')" style="display: none;" accept="text/html" value="'.$righthtml.'"></td>';
            print '<td><input type="button" name="righthtmlbutton" size=64 id="rightcontent3button" value="Open" onclick="OpenFile(\'rightcontent3\',\'rightcontent3txt\')"></td>';
            print "</tr>\n";
            
            print "<tr>\n";
            print "<td>&nbsp;</td>\n";
            print "<td><input type='radio' name='rightcontent' value='4' id='right4'>Start list</input></td>\n";
            print '<td><input type="text" name="rightstartlist" size=64 id="rightcontent4" readonly value="'.$rightclasses.'"></td>';
            print '<td><input type="button" name="rightstartlistbutton" size=64 id="rightstartlistbutton" value="Edit" onclick="EditClassesList('.$rcid.','.$cid.','.$sid.',2)"></td>';
            print "</tr>\n";
            
            print "<tr>\n";
            print "<td>&nbsp;</td>\n";
            print "<td><input type='radio' name='rightcontent' value='5' id='right5'>Results</input>&nbsp;\n";
            print '<td><input type="text" name="rightresults" size=64 id="rightcontent5" readonly value="'.$rightclasses.'"></td>';
            print '<td><input type="button" name="rightresultsbutton" size=64 id="rightresultsbutton" value="Edit" onclick="EditClassesList('.$rcid.','.$cid.','.$sid.',2)"></td>';
            print "</tr>\n";
            
            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='rightfirstline'></input></td>\n";
            $str=NumericIntList("rightfirstline",1,999,$rightfirstline);
            print "<td>First line</td>\n";
            print "<td>$str</td>\n";
            print "</tr>\n";

            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='rightfixedlines'></input></td>\n";
            $str=NumericIntList("rightfixedlines",0,20,$rightfixedlines);
            print "<td>Fixed lines</td>\n";
            print "<td>$str</td>\n";
            print "</tr>\n";

            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='rightscrolledlines'></input></td>\n";
            $str=NumericIntList("rightscrolledlines",0,20,$rightscrolledlines);
            print "<td>Scrolled lines</td>\n";
            print "<td>$str</td>\n";
            print "</tr>\n";

            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='rightscrolltime'></input></td>\n";
            $str=NumericIntList("rightscrolltime",1,200,$rightscrolltime);
            print "<td>Scroll time</td>\n";
            print "<td>$str 1/10°s</td> \n";
            print "</tr>\n";

            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='rightscrollbeforetime'></input></td>\n";
            $str=NumericIntList("rightscrollbeforetime",1,200,$rightscrollbeforetime);
            print "<td>Before scroll time</td>\n";
            print "<td>$str 1/10°s</td> \n";
            print "</tr>\n";

            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='rightscrollaftertime'></input></td>\n";
            $str=NumericIntList("rightscrollaftertime",1,200,$rightscrollaftertime);
            print "<td>After scroll time</td>\n";
            print "<td>$str 1/10°s</td> \n";
            print "</tr>\n";

            print "<tr>\n";
            print "<td><input type='checkbox' name='chkall[]' value='rightupdateduration'></input></td>\n";
            $str=NumericIntList("rightupdateduration",1,200,$rightupdateduration);
            print "<td>Recent highlight</td>\n";
            print "<td>$str min</td> \n";
            print "</tr>\n";

            print "</table>\n";

            //--------------------------------------
             
            print "<hr>\n";
			print "<div align=center>\n";
            print "Checkbox checked ==> copy corresponding settings to all screens of this configuration";
            print "<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' value='OK'>&nbsp;&nbsp;&nbsp;";
            print "<input type='button' value='Cancel' onclick='GoBack($rcid);'>";
			print "</div>\n";
            print "</form>\n";
        }
    }
    
?>
        <script type="text/javascript">
            window.onload = function() 
            {
                
<?php
                print "document.getElementById('full$fullcontent').click();\n";
                print "document.getElementById('left$leftcontent').click();\n";
                print "document.getElementById('right$rightcontent').click();\n";
?>
            }

        var radiofullcontent = document.screenedit.fullcontent;
            var radiofullcontentprev = null;
            for(var i = 0; i < radiofullcontent.length; i++) {
                radiofullcontent[i].onclick = function() {
                    //(prev)? console.log("prev="+prev.value):null;
                    if(this !== radiofullcontentprev) {
                        radiofullcontentprev = this;
                    }
                    ManageFull(this.value)
                    //console.log("new="+this.value)
                };
            }

            var radioleftcontent = document.screenedit.leftcontent;
            var radioleftcontentprev = null;
            for(var i = 0; i < radioleftcontent.length; i++) {
                radioleftcontent[i].onclick = function() {
                    if(this !== radioleftcontentprev) {
                        radioleftcontentprev = this;
                    }
                    ManageLeft(this.value)
                };
            }
            
            var radiorightcontent = document.screenedit.rightcontent;
            var radiorightcontentprev = null;
            for(var i = 0; i < radiorightcontent.length; i++) {
                radiorightcontent[i].onclick = function() {
                    if(this !== radiorightcontentprev) {
                        radiorightcontentprev = this;
                    }
                    ManageRight(this.value)
                };
            }

        </script>
    </body>
</html>

