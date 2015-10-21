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
  include_once('functions.php');

  session_start();
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>CFCO 2014 Config Edit</title>
        <script type="text/javascript">
            function GoBack()
            {
              location.replace("screenconfig.php");
            }        
        </script>
    </head>
    <body>
<?php

    $PHP_SELF = $_SERVER['PHP_SELF'];
    ConnectToDB();

    $rcid = isset($_GET['rcid']) ? intval($_GET['rcid']) : 0;
    if ($rcid>0)
    {
      $sql = "SELECT rc.name rcname, c.name cname FROM mopcompetition c, resultconfig rc WHERE rc.rcid=$rcid";
      $res = mysql_query($sql);
      
      if (mysql_num_rows($res) > 0)
      {
        $r = mysql_fetch_array($res);
        $rcname=$r['rcname'];
        
        print "<form method=GET action='screenconfig.php'>";
        print "<input type='hidden' name='action' value='update'>";
        print "<input type='hidden' name='rcid' value='$rcid'>";
        print "Name : <input type='text' name='configname' value='$rcname' size=64 maxlength=64><br/>";
        print "<br/><input type='submit' value='OK'>&nbsp;";
        print "<input type='button' value='Cancel' onclick='GoBack();'>";
        print "</form>";
      }
    }
    
?>
    </body>
</html>

