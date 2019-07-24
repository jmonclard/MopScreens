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

  include_once('functions.php');
  redirectSwitchUsers();

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

  function GetConfigurationName($rcid, $link)
  {
    $configname = "unknown";
    $sql = "SELECT name FROM resultconfig WHERE rcid=$rcid";
    $res = mysqli_query($link, $sql);
    if (mysqli_num_rows($res)>0)
    {
      $r = mysqli_fetch_array($res);
      $configname= $r['name'];
    }
    return $configname;
  }

  function AddNewConfiguration($rcid,$name)
  {
    $link = ConnectToDB();
    $sql = "INSERT INTO resultconfig SET rcid=$rcid, name='$name'";
    $ret=mysqli_query($link, $sql);
  }


  function DelConfiguration($rcid)
  {
    $link = ConnectToDB();
    $sql = "DELETE FROM resultconfig WHERE rcid='$rcid'";
    mysqli_query($link, $sql);

    $sql = "DELETE FROM resultscreen WHERE rcid=$rcid";
    mysqli_query($link, $sql);

    $sql = "DELETE FROM resultclass WHERE rcid=$rcid";
    mysqli_query($link, $sql);
  }

  //--------- screen functions ----------

  function AddNewScreen($rcid,$sid,$link)
  {
    $title="Screen #$sid";
    $sql = "INSERT INTO resultscreen SET rcid=$rcid, sid=$sid, title='$title'";
    $ret=mysqli_query($link, $sql);
  }


  function CloneScreen($oldrcid,$newrcid)
  {
    $link = ConnectToDB();
    $sql = "SELECT * FROM resultscreen WHERE rcid=$oldrcid";
    $res = mysqli_query($link, $sql);
    if (mysqli_num_rows($res) > 0)
    {
      while ($r = mysqli_fetch_array($res))
      {
        $str = "rcid=$newrcid, ";

        $sid=$r['sid'];
        $str = $str."sid=$sid, ";

        $str = $str."refresh=1, ";

        $cid=$r['cid'];
        $str = $str."cid=$cid, ";

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

        for ($i=1; $i<=NB_PANEL; $i++)
        {
          $prefix='panel'.$i;

          $content=$r[$prefix.'content'];
          $str = $str.$prefix.'content='.$content.', ';

          $pict=$r[$prefix.'pict'];
          $str = $str.$prefix.'pict="'.addSlashes($pict).'", ';

          $txt=stripSlashes($r[$prefix.'txt']);
          $str = $str.$prefix.'txt="'.addSlashes($txt).'", ';

          $txtsize=$r[$prefix.'txtsize'];
          $str = $str.$prefix.'txtsize="'.addSlashes($txtsize).'", ';

          $txtcolor=$r[$prefix.'txtcolor'];
          $str = $str.$prefix.'txtcolor="'.addSlashes($txtcolor).'", ';

          $html=$r[$prefix.'html'];
          $str = $str.$prefix.'html="'.addSlashes($html).'", ';

          $firstline=$r[$prefix.'firstline'];
          $str = $str.$prefix.'firstline='.$firstline.', ';

          $fixedlines=$r[$prefix.'fixedlines'];
          $str = $str.$prefix.'fixedlines='.$fixedlines.', ';

          $scrolledlines=$r[$prefix.'scrolledlines'];
          $str = $str.$prefix.'scrolledlines='.$scrolledlines.', ';

          $scrolltime=$r[$prefix.'scrolltime'];
          $str = $str.$prefix.'scrolltime='.$scrolltime.', ';

          $scrollbeforetime=$r[$prefix.'scrollbeforetime'];
          $str = $str.$prefix.'scrollbeforetime='.$scrollbeforetime.', ';

          $scrollaftertime=$r[$prefix.'scrollaftertime'];
          $str = $str.$prefix.'scrollaftertime='.$scrollaftertime.', ';

          $updateduration=$r[$prefix.'updateduration'];
          $str = $str.$prefix.'updateduration='.$updateduration.', ';

          $tmcount=$r[$prefix.'tm_count'];
	      $str = $str.$prefix.'tm_count='.$tmcount.', ';

	      $displaynomprenom=$r[$prefix.'displaynomprenom'];
	      $str = $str.$prefix.'displaynomprenom='.$displaynomprenom.', ';
        }
        //-------------------

        $panelscount=$r['panelscount'];
        $str = $str.'panelscount='.$panelscount.' ';



        //-------------------

        $sql = "INSERT INTO resultscreen SET $str";


        $ret=mysqli_query($link, $sql);
      }
    }
  }


  function GetClasses($rcid, $cid, $sid, $panel)
  {
    $link = ConnectToDB();
    $sqltmp = "SELECT name,ord FROM resultclass, mopclass WHERE mopclass.cid=resultclass.cid AND mopclass.id=resultclass.id AND mopclass.cid=$cid AND resultclass.rcid=$rcid AND resultclass.panel=$panel AND resultclass.sid=$sid ORDER BY ord";
    $restmp = mysqli_query($link, $sqltmp);
    $panelclasses="";

    if (mysqli_num_rows($restmp) > 0)
    {
      while ($rtmp = mysqli_fetch_array($restmp))
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

    function GetFirstClass($rcid, $cid, $sid, $panel, $link)
  {
    $sqltmp = "SELECT mopclass.id AS classid, name, ord FROM resultclass, mopclass WHERE ";
    $sqltmp = $sqltmp."mopclass.cid=resultclass.cid AND ";
    $sqltmp = $sqltmp."mopclass.id=resultclass.id AND ";
    $sqltmp = $sqltmp."mopclass.cid=$cid AND ";
    $sqltmp = $sqltmp."resultclass.rcid=$rcid AND ";
    $sqltmp = $sqltmp."resultclass.panel=$panel AND ";
    $sqltmp = $sqltmp."resultclass.sid=$sid ";
    $sqltmp = $sqltmp."ORDER BY ord LIMIT 1";
    $restmp = mysqli_query($link, $sqltmp);

    $nentry=0;
    $panelclasses="";

    if (mysqli_num_rows($restmp) > 0)
    {
      $rtmp = mysqli_fetch_array($restmp);
      $panelclasses=$rtmp['name'];
    }
    return $panelclasses;
  }


  function GetClassesAndEntries($rcid, $cid, $sid, $panel, $link)
  {
    $sqltmp = "SELECT mopclass.id AS classid, name, ord FROM resultclass, mopclass WHERE ";
    $sqltmp = $sqltmp."mopclass.cid=resultclass.cid AND ";
    $sqltmp = $sqltmp."mopclass.id=resultclass.id AND ";
    $sqltmp = $sqltmp."mopclass.cid=$cid AND ";
    $sqltmp = $sqltmp."resultclass.rcid=$rcid AND ";
    $sqltmp = $sqltmp."resultclass.panel=$panel AND ";
    $sqltmp = $sqltmp."resultclass.sid=$sid ";
    $sqltmp = $sqltmp."ORDER BY ord";
    $restmp = mysqli_query($link, $sqltmp);

    $nentry=0;
    $panelclasses="";
    if (mysqli_num_rows($restmp) > 0)
    {
      while ($rtmp = mysqli_fetch_array($restmp))
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
        $res2 = mysqli_query($link, $sql2);
        if (mysqli_num_rows($res2) > 0)
        {
          if ($r2 = mysqli_fetch_array($res2))
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
    $link = ConnectToDB();
    $sql = "SELECT * FROM resultclass WHERE rcid=$oldrcid";
    $res = mysqli_query($link, $sql);
    if (mysqli_num_rows($res) > 0)
    {
      while ($r = mysqli_fetch_array($res))
      {
        $str = "'".$newrcid."', ";
        $str = $str."'".$r['cid']."', ";
        $str = $str."'".$r['id']."', ";
        $str = $str."'".$r['sid']."', ";
        $str = $str."'".$r['panel']."'";
        $sql2 = "INSERT INTO resultclass (rcid, cid, id, sid, panel) VALUES ($str)";
        $res2 = mysqli_query($link, $sql2);
      }
    }
  }
?>
