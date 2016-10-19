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
    
  session_start();
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
        <title>Screen Configs</title>
        <link rel="stylesheet" type="text/css" href="styles/screen.css" />
        
        <script type="text/javascript">
            function AddConfig(prompt_text,rcid)
            {
                defaultname="New conf "+rcid.toString();
                name=prompt(prompt_text,defaultname);
                if (name!="null")
                {
                  location.replace("screenconfig.php?action=add&rcid="+rcid+"&name="+name);
                }
            }        

            function CloneConfig(prompt_text,oldrcid,newrcid,oldcid)
            {
                defaultname="New conf "+newrcid.toString();
                name=prompt(prompt_text,defaultname);
                if (name!='null')
                {
                    location.replace("screenconfig.php?action=clone&oldrcid="+oldrcid+"&newrcid="+newrcid+"&name="+name);
                }
            }        

            function DelConfig(prompt_text,rcid,configname)
            {
                if(confirm(prompt_text+configname+" ?"))
                {
                    location.replace("screenconfig.php?action=del&rcid="+rcid);
                }
            }

            function EditConfig(rcid)
            {
                location.replace("screenconfigedit.php?rcid="+rcid);
            }

            function ViewConfig(rcid)
            {
                location.replace("screen.php?rcid="+rcid);
            }
        
            function PlayConfig(rcid)
            {
                location.replace("screenconfig.php?action=play&rcid="+rcid);
            }

            function DelCompetition(prompt_text,cid,competname)
            {
                if(confirm(prompt_text+competname+" ?"))
                {
                    location.replace("screenconfig.php?action=delcompet&cid="+cid);
                }
            }

            function SetLanguage()
            {
              lng = document.getElementById("lang").value;
              location.replace("screenconfig.php?action=setlang&lng="+lng);
            }
			
        </script>
    </head>
    <body>
    
<?php
    
    

    $PHP_SELF = $_SERVER['PHP_SELF'];
    ConnectToDB();

    
    $action = isset($_GET['action']) ? strval($_GET['action']) : "";

    
    if ($action==="serverip")
    {
        $ip1 = isset($_GET['ip1']) ? intval($_GET['ip1']) : 192;
        $ip2 = isset($_GET['ip2']) ? intval($_GET['ip2']) : 168;
        $ip3 = isset($_GET['ip3']) ? intval($_GET['ip3']) : 0;
        $ip4 = isset($_GET['ip4']) ? intval($_GET['ip4']) : 56;
        $serverip = $ip1.".".$ip2.".".$ip3.".".$ip4."\n";
        $severipfile = fopen('pictures/serverip.txt', 'w');
        fputs($severipfile, $serverip);
        fclose($severipfile);
    }
        

    if ($action==="setlang")
    {
        $lng = isset($_GET['lng']) ? strval($_GET['lng']) : "en";
        $_SESSION['CurrentLanguage']=$lng;
    }
        
    if ($action==="add")
    {
        if (isset($_GET['rcid']))
        {
            $rcid = intval($_GET['rcid']);
            $name = isset($_GET['name']) ? $_GET['name'] : "New configuration";
            AddNewConfiguration($rcid,$name);
        }
    }
    
    if ($action==="del")
    {
        $rcid = isset($_GET['rcid']) ? intval($_GET['rcid']) : 0;
        DelConfiguration($rcid);
    }

    if ($action==="update")
    {
        if (isset($_GET['rcid']))
        {
            $rcid = intval($_GET['rcid']);
            $newname = urldecode(isset($_GET['configname']) ? $_GET['configname'] : "no name");

            $res = mysql_query("SELECT rcid FROM resultconfig WHERE rcid=$rcid");

            if (mysql_num_rows($res) > 0)
            {
                $sql = "UPDATE resultconfig SET name='$newname' WHERE rcid=$rcid";
                $res = mysql_query($sql);
            }
        }
        mysql_query($sql);
  
    }

    if ($action==="clone")
    {
        if (isset($_GET['oldrcid']) && isset($_GET['newrcid']))
        {
            $oldrcid = intval($_GET['oldrcid']);
            $newrcid = intval($_GET['newrcid']);
            $name = isset($_GET['name']) ? $_GET['name'] : "New configuration";
            AddNewConfiguration($newrcid,$name);
            CloneScreen($oldrcid,$newrcid);
            CloneClass($oldrcid,$newrcid);
        }
    }

    if ($action==="play")
    {
        if (isset($_GET['rcid']))
        {
            $rcid = intval($_GET['rcid']);

            $res = mysql_query("SELECT rcid FROM resultconfig WHERE rcid=$rcid");
            if (mysql_num_rows($res) > 0)
            {
                $sql = "UPDATE resultconfig SET active=0";
                $res = mysql_query($sql);
                $sql = "UPDATE resultconfig SET active=1 WHERE rcid=$rcid";
                $res = mysql_query($sql);
            }
        }
    }
	
    if ($action==="delcompet")
    {
      $cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
      if ($cid>0)
      {
        $tables = array(0=>"mopcontrol", "mopclass", "moporganization", "mopcompetitor", "mopcompetition",
						  "mopteam", "mopteammember", "mopclasscontrol", "mopradio", "resultclass");
						  
        foreach($tables as $table)
        {
          $sql = "DELETE FROM $table WHERE cid=$cid";
          mysql_query($sql);
        } 
      }
    }
	
  

    //-- Determine next available rcid for add or clone operations
    
    $sql = "SELECT rcid FROM resultconfig";
    $res = mysql_query($sql);
    $nextrcid=1;
    if (mysql_num_rows($res) > 0)
    {
        while ($r = mysql_fetch_array($res))
        {
            $rcid=$r['rcid'];
            if ($rcid>=$nextrcid)
            {
                $nextrcid = $rcid+1;
            }
        }
    }
    print "<table border>\n";
    print "<tr>";
    print "<th colspan=4>".MyGetText(0)."</th>";
    print "<th colspan=3>&nbsp;</th>";
    print "</tr>";
	
    $sql = "SELECT * FROM resultconfig";
    $res = mysql_query($sql);
    if (mysql_num_rows($res) > 0)
    {
      while ($r = mysql_fetch_array($res))
      {
        $rcid=$r['rcid'];
        $name=$r['name'];
        $active = $r['active'];
        print "<tr>\n";
        print "<td><a href='screen.php?rcid=$rcid'>$name</a></td>\n";
        print "<td><img src='img/edit.png' title='".MyGetText(1)."' onclick='ViewConfig($rcid);'></img></td>\n";
        print "<td><img src='img/play.png' title='".MyGetText(2)."' onclick='PlayConfig($rcid);'></img></td>\n";
        if ($active==1)
        {
          print "<td><img src='img/run.png' title='".MyGetText(3)."'></img></td>\n";
        }
        else
        {
          print "<td>&nbsp;</td>\n";
        }
        print "<td><img src='img/rename.png' title='".MyGetText(4)."' onclick='EditConfig($rcid);'></img></td>\n";
        print "<td><img src='img/clone.png' title='".MyGetText(5)."' onclick='CloneConfig(\"".MyGetText(13)."\",$rcid,$nextrcid);'></img></td>\n";
        print "<td><img src='img/suppr.png' title='".MyGetText(6)."' onclick='DelConfig(\"".MyGetText(14)."\",$rcid,\"$rcname\");'></img></td>\n";
        print "</tr>\n";
      } 
    }
    print "</table>\n";
    print "<input type='button' value='".MyGetText(7)."' onclick='AddConfig(\"".MyGetText(13)."\",$nextrcid);'>"; // New button

	
    //------------------- display competitions -------------
    print "<br/><br/><table border>\n";
    print "<tr>\n";
    print "<th>".MyGetText(8)."</th>\n";  // Id
    print "<th>".MyGetText(9)."</th>\n";  // Competitions
    print "<th>".MyGetText(10)."</th>\n"; // Date
    print "<th>".MyGetText(11)."</th>\n"; // Organizers
    print "<th>&nbsp;</th>\n";
    print "</tr>\n";
	
    $sql = "SELECT * FROM mopcompetition ORDER BY cid";
    $res = mysql_query($sql);
    if (mysql_num_rows($res) > 0)
    {
      while ($r = mysql_fetch_array($res))
      {
        $cid=$r['cid'];
        $name=$r['name'];
        $date=$r['date'];
        $organizer=$r['organizer'];
        print "<tr>\n";
        print "<td class=class_competid>$cid</td>\n";
        print "<td>$name</td>\n";
        print "<td>$date</td>\n";
        print "<td>$organizer</td>\n";
        print "<td><img src='img/suppr.png' title='".MyGetText(6)."' onclick='DelCompetition(\"".MyGetText(14)."\",$cid,\"$name\");'></img></td>\n"; // Delete
        print "</tr>\n";
      } 
    }
    print "</table>\n";
	  print "<br/>\n";
    print "<hr/>\n";
    print "<b>".MyGetText(69)."</b><br/>\n";// Resource files
    print "<img src='img/pict.png' title='Picture'/>\n";
    print "&nbsp;\n";
    print "<img src='img/htm.png' title='Picture'/>\n";
    print "&nbsp;\n";
    print "<a href=screenfiles.php>".MyGetText(12)."</a>";
    print "<hr/>\n";
    print "<b>".MyGetText(68)."</b><br/>\n";// documentation
    print "<img src='img/htm.png'></img>&nbsp;<a href=readme.html target='_blank'>README (english)</a><br/>\n";
    $docfilename= file_exists("MopScreens-".$_SESSION['CurrentLanguage'].".pdf") ? "MopScreens-".$_SESSION['CurrentLanguage'].".pdf" : "MopScreens-en.pdf";
    print "<img src='img/pdf.png'></img>&nbsp;<a href=".$docfilename.">".MyGetText(67)."</a><br/>\n";

	  print "<hr/>\n";
    print "<b>".MyGetText(71)."</b><br>\n";     // Settings
    print MyGetText(36)." : ";
    print "<select name='lang' id='lang' size=1 onchange='SetLanguage();'>";
    foreach (GetLanguages() as $lng)
    {
        $code=$lng[0];
        $name=$lng[1];
        if ($code===$_SESSION['CurrentLanguage'])
        {
            print "<option value=$code selected>$name</option>";
        }
        else
        {
            print "<option value=$code>$name</option>";
        }
    }
    print "</select>\n";
    print "<br/>\n";
    print "<a href=\"screenserverip.php\">".MyGetText(70)."</a><br/>\n" ;
    
    print "<a href=\"screenradioconfig.php\">".MyGetText(86)."</a><br/>\n" ;
    print "<br/>\n";
    print "<a href=\"screenblog.php\">".MyGetText(108)."</a><br/>\n" ;
	  print "<hr/>\n";

	  print "<br/>\n";
	  print "<br/>\n";
  
?>        
  </body>
</html>
