<?php
  /*
  Copyright 2014-2015 Metraware
  
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

	function ECRIRE_LOG($errtxt)
	{
		$fp = fopen('log.txt','a+'); // ouvrir le fichier ou le créer
		fseek($fp,SEEK_END); // poser le point de lecture à la fin du fichier
		$nouverr=$errtxt."\r\n"; // ajouter un retour à la ligne au fichier
		fputs($fp,$nouverr); // ecrire ce texte
		fclose($fp); //fermer le fichier
	}

  function DEBUG($varname,$value)
  {
    print ("<br/>$varname=$value<br/>");
  }

  function DUMPARRAY($a)
  {
    print "<br/>\n";
    foreach ($a as $k => $v)
    {
      print "key=$k , value=$v<br/>\n";
    } 
  }

  function NumericIntList($ctrlname,$minval,$maxval,$defval)
  {
    $str="<select name='$ctrlname' id='$ctrlname' size=1>\n";
    for ($i=$minval; $i<=$maxval; $i++)
    {
      if ($i==$defval)
      {
        $str=$str."<option value=$i selected>$i</option>\n";
      }
      else
      {
        $str=$str."<option value=$i>$i</option>\n";
      }
    }
    $str=$str."</select>\n";
    return $str;
  }

  //-------- screens configuration functions ---------
  
  function GetConfigurationName($rcid)
  {
    $configname = "unknown";
    $sql = "SELECT name FROM resultconfig WHERE rcid=$rcid";
    $res = mysql_query($sql);
    if (mysql_num_rows($res)>0)
    {
      $r = mysql_fetch_array($res);
      $configname= $r['name'];
    }
    return $configname;
  }
    
  function AddNewConfiguration($rcid,$name)
  {
    $sql = "INSERT INTO resultconfig SET rcid=$rcid, name='$name'"; 
    $ret=mysql_query($sql);
  }


  function DelConfiguration($rcid)
  {
    $sql = "DELETE FROM resultconfig WHERE rcid='$rcid'";  
    mysql_query($sql);

    $sql = "DELETE FROM resultscreen WHERE rcid=$rcid";
    mysql_query($sql);

    $sql = "DELETE FROM resultclass WHERE rcid=$rcid";
    mysql_query($sql);
  }

  //--------- screen functions ----------

  function AddNewScreen($rcid,$sid)
  {
    $title="Screen #$sid";
    $sql = "INSERT INTO resultscreen SET rcid=$rcid, sid=$sid, title='$title', fullscrolltime=10, leftscrolltime=10, rightscrolltime=10"; 
    $ret=mysql_query($sql);
  }

  function CloneScreen($oldrcid,$newrcid)
  {
    $sql = "SELECT * FROM resultscreen WHERE rcid=$oldrcid";
    $res = mysql_query($sql);
    if (mysql_num_rows($res) > 0)
    {
      while ($r = mysql_fetch_array($res))
      {
        $str = "rcid=$newrcid, ";

        $sid=$r['sid'];
        $str = $str."sid=$sid, ";

        $str = $str."refresh=1, ";

        $cid=$r['cid'];
        $str = $str."cid=$cid, ";

        $screenmode=$r['screenmode'];
        $str = $str."screenmode=$screenmode, ";

        //-------------------
        
        $title=stripSlashes($r['title']);
        $str = $str."title='".addSlashes($title)."', ";

        $titlesize=$r['titlesize'];
        $str = $str."titlesize=$titlesize, ";

        $titlecolor=$r['titlecolor'];
        $str = $str."titlecolor='$titlecolor', ";

        $subtitle=stripSlashes($r['subtitle']);
        $str = $str."subtitle='".addSlashes($subtitle)."', ";

        $subtitlesize=$r['subtitlesize'];
        $str = $str."subtitlesize=$subtitlesize, ";

        $subtitlecolor=$r['subtitlecolor'];
        $str = $str."subtitlecolor='$subtitlecolor', ";

        $titleleftpict=$r['titleleftpict'];
        $str = $str."titleleftpict='$titleleftpict', ";

        $titlerightpict=$r['titlerightpict'];
        $str = $str."titlerightpict='$titlerightpict', ";

        //-------------------

        $fullcontent=$r['fullcontent'];
        $str = $str."fullcontent=$fullcontent, ";

        $fullpict=$r['fullpict'];
        $str = $str."fullpict='$fullpict', ";

        $fulltxt=stripSlashes($r['fulltxt']);
        $str = $str."fulltxt='".addSlashes($fulltxt)."', ";

        $fulltxtsize=$r['fulltxtsize'];
        $str = $str."fulltxtsize='".addSlashes($fulltxtsize)."', ";

        $fulltxtcolor=$r['fulltxtcolor'];
        $str = $str."fulltxtcolor='".addSlashes($fulltxtcolor)."', ";

        $fullhtml=$r['fullhtml'];
        $str = $str."fullhtml='$fullhtml', ";

        $fullfirstline=$r['fullfirstline'];
        $str = $str."fullfirstline='$fullfirstline', ";

        $fullfixedlines=$r['fullfixedlines'];
        $str = $str."fullfixedlines='$fullfixedlines', ";

        $fullscrolledlines=$r['fullscrolledlines'];
        $str = $str."fullscrolledlines='$fullscrolledlines', ";

        $fullscrolltime=$r['fullscrolltime'];
        $str = $str."fullscrolltime='$fullscrolltime', ";

        $fullscrollbeforetime=$r['fullscrollbeforetime'];
        $str = $str."fullscrollbeforetime='$fullscrollbeforetime', ";

        $fullscrollaftertime=$r['fullscrollaftertime'];
        $str = $str."fullscrollaftertime='$fullscrollaftertime', ";

        $fullupdateduration=$r['fullupdateduration'];
        $str = $str."fullupdateduration='$fullupdateduration', ";

        //-------------------

        $leftcontent=$r['leftcontent'];
        $str = $str."leftcontent=$leftcontent, ";

        $leftpict=$r['leftpict'];
        $str = $str."leftpict='$leftpict', ";

        $lefttxt=stripSlashes($r['lefttxt']);
        $str = $str."lefttxt='".addSlashes($lefttxt)."', ";

        $lefttxtsize=$r['lefttxtsize'];
        $str = $str."lefttxtsize='$lefttxtsize', ";

        $lefttxtcolor=$r['lefttxtcolor'];
        $str = $str."lefttxtcolor='$lefttxtcolor', ";

        $lefthtml=$r['lefthtml'];
        $str = $str."lefthtml='$lefthtml', ";

        $leftfirstline=$r['leftfirstline'];
        $str = $str."leftfirstline='$leftfirstline', ";

        $leftfixedlines=$r['leftfixedlines'];
        $str = $str."leftfixedlines='$leftfixedlines', ";

        $leftscrolledlines=$r['leftscrolledlines'];
        $str = $str."leftscrolledlines='$leftscrolledlines', ";

        $leftscrolltime=$r['leftscrolltime'];
        $str = $str."leftscrolltime='$leftscrolltime', ";

        $leftscrollbeforetime=$r['leftscrollbeforetime'];
        $str = $str."leftscrollbeforetime='$leftscrollbeforetime', ";

        $leftscrollaftertime=$r['leftscrollaftertime'];
        $str = $str."leftscrollaftertime='$leftscrollaftertime', ";

        $leftupdateduration=$r['leftupdateduration'];
        $str = $str."leftupdateduration='$leftupdateduration', ";

        //-------------------

        $rightcontent=$r['rightcontent'];
        $str = $str."rightcontent=$rightcontent, ";

        $rightpict=$r['rightpict'];
        $str = $str."rightpict='$rightpict', ";

        $righttxt=stripSlashes($r['righttxt']);
        $str = $str."righttxt='".addSlashes($righttxt)."', ";

        $righttxtsize=$r['righttxtsize'];
        $str = $str."righttxtsize='$righttxtsize', ";

        $righttxtcolor=$r['righttxtcolor'];
        $str = $str."righttxtcolor='$righttxtcolor', ";

        $righthtml=$r['righthtml'];
        $str = $str."righthtml='$righthtml', ";

        $rightfirstline=$r['rightfirstline'];
        $str = $str."rightfirstline='$rightfirstline', ";

        $rightfixedlines=$r['rightfixedlines'];
        $str = $str."rightfixedlines='$rightfixedlines', ";

        $rightscrolledlines=$r['rightscrolledlines'];
        $str = $str."rightscrolledlines='$rightscrolledlines', ";

        $rightscrolltime=$r['rightscrolltime'];
        $str = $str."rightscrolltime='$rightscrolltime', ";

        $rightscrollbeforetime=$r['rightscrollbeforetime'];
        $str = $str."rightscrollbeforetime='$rightscrollbeforetime', ";

        $rightscrollaftertime=$r['rightscrollaftertime'];
        $str = $str."rightscrollaftertime='$rightscrollaftertime', ";

        $rightupdateduration=$r['rightupdateduration'];
        $str = $str."rightupdateduration='$rightupdateduration' ";

        //-------------------

        $sql = "INSERT INTO resultscreen SET $str";

        $ret=mysql_query($sql);
      }
    }
  }


  function GetClasses($rcid, $cid, $sid, $panel)
  {
    $sqltmp = "SELECT name,ord FROM resultclass, mopclass WHERE mopclass.cid=resultclass.cid AND mopclass.id=resultclass.id AND mopclass.cid=$cid AND resultclass.rcid=$rcid AND resultclass.panel=$panel AND resultclass.sid=$sid ORDER BY ord";
    $restmp = mysql_query($sqltmp);
    if (mysql_num_rows($restmp) > 0)
    {
      $panelclasses="";
      while ($rtmp = mysql_fetch_array($restmp))
      {
        $nametmp=$rtmp['name'];
        if (strlen($panelclasses)>0)
        {
          $panelclasses=$panelclasses." , ".$nametmp;
        }
        else
        {
          $panelclasses=$nametmp;
        }
      }
    }
    return $panelclasses;            
  }
	
  function GetClassesAndEntries($rcid, $cid, $sid, $panel)
  {
    $sqltmp = "SELECT mopclass.id AS classid, name, ord FROM resultclass, mopclass WHERE ";
    $sqltmp = $sqltmp."mopclass.cid=resultclass.cid AND ";
	$sqltmp = $sqltmp."mopclass.id=resultclass.id AND ";
	$sqltmp = $sqltmp."mopclass.cid=$cid AND ";
	$sqltmp = $sqltmp."resultclass.rcid=$rcid AND ";
	$sqltmp = $sqltmp."resultclass.panel=$panel AND ";
	$sqltmp = $sqltmp."resultclass.sid=$sid ";
	$sqltmp = $sqltmp."ORDER BY ord";
    $restmp = mysql_query($sqltmp);

    $nentry=0;
    if (mysql_num_rows($restmp) > 0)
    {
      $panelclasses="";
      while ($rtmp = mysql_fetch_array($restmp))
      {
        $nametmp=$rtmp['name'];
        if (strlen($panelclasses)>0)
        {
          $panelclasses=$panelclasses." , ".$nametmp;
        }
        else
        {
          $panelclasses=$nametmp;
        }
		// determines number of entries
		$classid = intval($rtmp['classid']);
		$sql2 = "SELECT COUNT(*) FROM mopcompetitor WHERE cid=$cid AND cls=$classid";
		$res2 = mysql_query($sql2);
		if (mysql_num_rows($res2) > 0)
		{
          if ($r2 = mysql_fetch_array($res2))
		  {
		    $nentry=$nentry+$r2[0];
		  }
		}
      }
	  $panelclasses = "<b>$nentry : </b>".$panelclasses;
    }
    return $panelclasses;            
  }

  //--------- class functions ----------    
  function CloneClass($oldrcid,$newrcid)
  {
    $sql = "SELECT * FROM resultclass WHERE rcid=$oldrcid";
    $res = mysql_query($sql);
    if (mysql_num_rows($res) > 0)
    {
      while ($r = mysql_fetch_array($res))
      {
        $str = "'".$newrcid."', ";
        $str = $str."'".$r['cid']."', ";
        $str = $str."'".$r['id']."', ";
        $str = $str."'".$r['sid']."', ";
        $str = $str."'".$r['panel']."'";
        $sql2 = "INSERT INTO resultclass (rcid, cid, id, sid, panel) VALUES ($str)";
        $res2 = mysql_query($sql2);
      }
    }
  }
?>
