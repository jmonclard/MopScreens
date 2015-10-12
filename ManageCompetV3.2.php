<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>CFCO 2014 Screen Configs</title>
        <link rel="stylesheet" type="text/css" href="screen.css" />
        
        <script type="text/javascript">


            function DelCompetition(cid,competname)
            {
                if(confirm("Do you really want to delete "+competname+" ?"))
                {
                    location.replace("ManageCompetV3.2.php?action=delcompet&cid="+cid);
                }
            }
			
			
        </script>
    </head>
    <body>
	
<?php
  include_once("config.php");
  
  /** Connecto to MySQL */
  $link = mysql_connect(MYSQL_HOSTNAME, MYSQL_USERNAME, MYSQL_PASSWORD);
  if (!$link) {
    die('Not connected : ' . mysql_error());
  }
  
  $db_selected = mysql_select_db(MYSQL_DBNAME, $link);
  if (!$db_selected) {
    die ("Can't use ". MYSQL_HOSTNAME. ' : ' . mysql_error());
  }    


	/** Remove all data from a table related to an event. */
  $action = isset($_GET['action']) ? strval($_GET['action']) : "";
  
  if ($action==="delcompet")
  {
      $cid = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
  		if ($cid>0)
  		{
  			$tables = array(0=>"mopControl", "mopClass", "mopOrganization", "mopCompetitor", "mopCompetition",
  						  "mopTeam", "mopTeammember", "mopClasscontrol", "mopRadio");
  						  
  			foreach($tables as $table)
  			{
  				$sql = "DELETE FROM $table WHERE cid=$cid";
  				mysql_query($sql);
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
  $sql = "SELECT * FROM mopCompetition ORDER BY cid";
  $res = mysql_query($sql);
  //if (mysql_num_rows($res) > 0)
  //{
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
          print "<td><img src='suppr.png' title='delete' onclick='DelCompetition($cid,\"$name\");'></img></td>\n";
          print "</tr>\n";
      } 
  //}
  print "</table>\n";
	
?>        
    </body>
</html>
