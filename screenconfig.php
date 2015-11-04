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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>CFCO 2014 Screen Configs</title>
        <link rel="stylesheet" type="text/css" href="styles/screen.css" />
        
        <script type="text/javascript">
            function AddConfig(rcid)
            {
                defaultname="New conf "+rcid.toString();
                name=prompt("New configuration name : ",defaultname);
                if (name!="null")
                {
                  location.replace("screenconfig.php?action=add&rcid="+rcid+"&name="+name);
                }
            }        

            function CloneConfig(oldrcid,newrcid,oldcid)
            {
                defaultname="New conf "+newrcid.toString();
                name=prompt("New configuration name : ",defaultname);
                if (name!='null')
                {
                    location.replace("screenconfig.php?action=clone&oldrcid="+oldrcid+"&newrcid="+newrcid+"&name="+name);
                }
            }        

            function DelConfig(rcid,configname)
            {
                if(confirm("Do you really want to delete "+configname+" ?"))
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

            function DelCompetition(cid,competname)
            {
                if(confirm("Do you really want to delete "+competname+" ?"))
                {
                    location.replace("screenconfig.php?action=delcompet&cid="+cid);
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

    
    $action = isset($_GET['action']) ? strval($_GET['action']) : "";
        
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
    print "<th colspan=4>Configuration</th>";
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
        print "<td><img src='img/edit.png' title='View' onclick='ViewConfig($rcid);'></img></td>\n";
        print "<td><img src='img/play.png' title='View' onclick='PlayConfig($rcid);'></img></td>\n";
        if ($active==1)
        {
          print "<td><img src='img/run.png' title='View'></img></td>\n";
        }
        else
        {
          print "<td>&nbsp;</td>\n";
        }
        print "<td><img src='img/rename.png' title='edit' onclick='EditConfig($rcid);'></img></td>\n";
        print "<td><img src='img/clone.png' title='clone' onclick='CloneConfig($rcid,$nextrcid);'></img></td>\n";
        print "<td><img src='img/suppr.png' title='delete' onclick='DelConfig($rcid,\"$rcname\");'></img></td>\n";
        print "</tr>\n";
      } 
    }
    print "</table>\n";
    print "<input type='button' value='Add' onclick='AddConfig($nextrcid);'>";

	
    //------------------- display competitions -------------
    print "<br/><br/><table border>\n";
    print "<tr>\n";
    print "<th>Id</th>\n";
    print "<th>Competitions</th>\n";
    print "<th>Date</th>\n";
    print "<th>Organizer</th>\n";
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
        print "<td><img src='img/suppr.png' title='delete' onclick='DelCompetition($cid,\"$name\");'></img></td>\n";
        print "</tr>\n";
      } 
    }
    print "</table>\n";
	
?>        
    </body>
</html>
