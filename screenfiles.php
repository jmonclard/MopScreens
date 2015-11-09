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
    <title>Screen files management</title>
    <link rel="stylesheet" type="text/css" href="styles/screen.css" />

    <script type="text/javascript">

      function DelImageFile(prompt_text,name)
      {
        if(confirm(prompt_text+name+" ?"))
        {
          location.replace("screenfiles.php?action=delimage&name=\""+name+"\"");
        }
      }

      function DelHtmlFile(prompt_text,name)
      {
        if(confirm(prompt_text+name+" ?"))
        {
          location.replace("screenfiles.php?action=delhtml&name=\""+name+"\"");
        }
      }

    </script>
  </head>
  <body>
<?php
  include_once('functions.php');
  include_once('screenfunctions.php');
  include_once('lang.php');

	if (is_uploaded_file ($_FILES['imagefilename']['tmp_name']))
	{
	  $file_type = explode('/',$_FILES['imagefilename']['type']);
	  if ($file_type[0]=='image')
	  {
      $dest_filepathname="./pictures/".$_FILES['imagefilename']['name'];
      copy($_FILES['imagefilename']['tmp_name'],$dest_filepathname);
	  }
	  else
	  {
		  echo MyGetText(15).$_FILES['imagefilename']['type']." <br/>\n"; // Bad file type
		  echo MyGetText(16)."<br/><br/>\n";
	  }
	}
	if (is_uploaded_file ($_FILES['htmlfilename']['tmp_name']))
	{
	  $file_type = $_FILES['htmlfilename']['type'];
	  if ($file_type=='text/html')
	  {
      $dest_filepathname="./htmlfiles/".$_FILES['htmlfilename']['name'];
      copy($_FILES['htmlfilename']['tmp_name'],$dest_filepathname);
	  }
	  else
	  {
		  echo MyGetText(15).$_FILES['htmlfilename']['type']." <br/>\n";  // Bad file type
		  echo MyGetText(16)."<br/><br/>\n";
	  }
	}	


    $PHP_SELF = $_SERVER['PHP_SELF'];
    
    $action = isset($_GET['action']) ? strval($_GET['action']) : "";

    if ($action==="delimage")
    {
        $name = isset($_GET['name']) ? strval($_GET['name']) : "";
        if ($name!="")
        {
          $pathname = "./pictures/".substr($name,1,-1);
          unlink($pathname);
        }
    }

    if ($action==="delhtml")
    {
        $name = isset($_GET['name']) ? strval($_GET['name']) : "";
        if ($name!="")
        {
          $pathname = "./htmlfiles/".substr($name,1,-1);
          unlink($pathname);
        }
    }

    //---------- files lists creation ----
    $picturefilelist= array();
    $tmp_picturefilelist=array_diff(scandir("./pictures"), array('..', '.','index.php','index.html'));
    foreach ($tmp_picturefilelist as $name)
    {
      $picturefilelist[$name]=$name;
    }

    $htmlfilelist= array();
    $tmp_htmlfilelist=array_diff(scandir("./htmlfiles"), array('..', '.','index.php','index.html'));
    foreach ($tmp_htmlfilelist as $name)
    {
      $pathname = "./htmlfiles/".substr($name,1,-1);
      $htmlfilelist[$name]=$name;
    }

    //------- pictures files -----------
    
    print "<table border>\n";
    print "<tr>\n";
    print "<th colspan=3>".MyGetText(17)."</th>\n"; // Image files
    print "<th colspan=2>&nbsp;</th>\n";
    print "</tr>\n";
	
    foreach ($picturefilelist as $id => $name)
    {
      $pathname = "./pictures/".$name;
      if (file_exists($pathname))
      {
        $filesize = round(filesize($pathname)/1024,1);
        $filedate = date("Y-m-d H:i:s", filemtime($pathname));
      }
      
      print "<tr>\n";
      print "<td>".$name."</td>\n";
      print "<td align='right'>".$filesize." k</td>\n";
      print "<td align='right'>".$filedate."</td>\n";
      print "<td><a href='".$pathname."' target='_blank'><img src='img/pict.png' title='".MyGetText(18)."'></img></a></td>\n";  // show
      print "<td><img src='img/suppr.png' title='".MyGetText(6)."' onclick='DelImageFile(\"".MyGetText(14)."\",\"".$name."\");'></img></td>\n";
      print "</tr>\n";
    }
    print "</table>\n";
	  print "<br/>\n";
	  
    print "<form enctype='multipart/form-data' action='screenfiles.php' method='POST' id='imagefileadd'>\n";
	  print MyGetText(21)."<br/>\n";  // Image upload
		print "<input type=hidden name='MAX_FILE_SIZE' value='2000000'>\n";
		print "<input type='file' accept='image/*' size=80 name='imagefilename' onchange='document.getElementById(\"imagefileadd\").submit();'>\n";
    print "</form>\n";
    print "<br/>\n";
	  print "<hr/><br/>\n";

    //------------- html files -------------
      
    print "<table border>\n";
    print "<tr>\n";
    print "<th colspan=3>".MyGetText(20)."</th>\n";
    print "<th colspan=2>&nbsp;</th>\n";
    print "</tr>\n";
	
    foreach ($htmlfilelist as $id => $name)
    {
      $pathname = "./htmlfiles/".$name;
      if (file_exists($pathname))
      {
        $filesize = round(filesize($pathname)/1024,1);
        $filedate = date("Y-m-d H:i:s", filemtime($pathname));
      }
      print "<tr>\n";
      print "<td>".$name."</td>\n";
      print "<td align='right'>".$filesize." k</td>\n";
      print "<td align='right'>".$filedate."</td>\n";
      print "<td><a href='".$pathname."' target='_blank'><img src='img/htm.png' title='".MyGetText(18)."'></img></a></td>\n";
      print "<td><img src='img/suppr.png' title='".MyGetText(6)."' onclick='DelHtmlFile(\"".MyGetText(14)."\",\"".$name."\");'></img></td>\n";
      print "</tr>\n";
    }
    print "</table>\n";
	  print "<br/>\n";
    
    print "<form enctype='multipart/form-data' action='screenfiles.php' method='POST' id='htmlfileadd'>\n";
	  print MyGetText(22)."<br/>\n";  // HTML upload
		print "<input type=hidden name='MAX_FILE_SIZE' value='2000000'>\n";
		print "<input type='file' accept='.html,.htm' size=80 name='htmlfilename' onchange='document.getElementById(\"htmlfileadd\").submit();'>\n";
    print "</form>\n";
    print "<br/>\n";
	  print "<hr/><br/>\n";
    print "<a href='screenconfig.php'>".MyGetText(19)."</a>&nbsp;&nbsp;&nbsp;";
    print "<br/>\n";
    
?>        
    </form>
    </body>
</html>
