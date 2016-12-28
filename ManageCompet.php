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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>CFCO 2014 Screen Configs</title>
        <link rel="stylesheet" type="text/css" href="styles/screen.css" />
        
        <script type="text/javascript">


            function DelCompetition(cid,competname)
            {
                if(confirm("Do you really want to delete "+competname+" ?"))
                {
                    location.replace("ManageCompet.php?action=delcompet&cid="+cid);
                }
            }
			
			
        </script>
    </head>
    <body>
	
<?php
	include_once("config.php");

	/** Connecto to MySQL */
	function ConnectToDB() {
	  $link =  mysqli_connect( mysqli_HOSTNAME,  mysqli_USERNAME,  mysqli_PASSWORD);
	  if (!$link) {
		die('Not connected : ' .  mysqli_error());
	  }

	  $db_selected =  mysqli_select_db( mysqli_DBNAME, $link);
	  if (!$db_selected) {
		die ("Can't use ".  mysqli_HOSTNAME. ' : ' .  mysqli_error());
	  }    
	  return $link;
	}

	$link = ConnectToDB();

	/** Remove all data from a table related to an event. */
  $action = isset($_GET['action']) ? strval($_GET['action']) : "";
  
  if ($action==="delcompet")
  {
      $cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
  		if ($cid>0)
  		{
  			$tables = array(0=>"mopcontrol", "mopclass", "moporganization", "mopcompetitor", "mopcompetition",
  						  "mopteam", "mopteammember", "mopclasscontrol", "mopradio");
  						  
  			foreach($tables as $table)
  			{
  				$sql = "DELETE FROM $table WHERE cid=$cid";
  				 mysqli_query($link , $sql);
  			} 
  		}
  	
  }

	//------------------- display competitions -------------
  print "<br/><br/><table border>\n";
  print "<tr>\n";
  print "<th>Id</th>\n";
  print "<th>Competitions</th>\n";
  print "<th>Date</th>\n";
  print "<th>Organizer</th>\n";
  print "<th>&nbsp;</th>\n";
  print "</tr>\n";
	ConnectToDB();
  $sql = "SELECT * FROM mopcompetition ORDER BY cid";
  $res =  mysqli_query($link , $sql);
  //if ( mysqli_num_rows($res) > 0)
  //{
      while ($r =  mysqli_fetch_array($res))
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
          print "<td><img src='suppr.png' title='delete' onclick='DelCompetition($cid,\"$name\");'></img></td>\n";
          print "</tr>\n";
      } 
  //}
  print "</table>\n";
	
?>        
    </body>
</html>
