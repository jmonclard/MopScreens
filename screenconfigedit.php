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


  session_start();
  //date_default_timezone_set('Europe/Paris');
  date_default_timezone_set('UTC');
  include_once('functions.php');
  redirectSwitchUsers();


  include_once('lang.php');
  $_SESSION['CurrentLanguage'] = isset($_SESSION['CurrentLanguage']) ? $_SESSION['CurrentLanguage'] : autoSelectLanguage(array('fr','en','sv'),'en');

?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Config Rename</title>
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
     $link = ConnectToDB();

    $rcid = isset($_GET['rcid']) ? intval($_GET['rcid']) : 0;
    if ($rcid>0)
    {
      $sql = "SELECT rc.name rcname, c.name cname FROM mopcompetition c, resultconfig rc WHERE rc.rcid=$rcid";
      $res = mysqli_query($link , $sql);

      if (mysqli_num_rows($res) > 0)
      {
        $r = mysqli_fetch_array($res);
        $rcname=$r['rcname'];

        print "<form method=GET action='screenconfig.php'>";
        print "<input type='hidden' name='action' value='update'>";
        print "<input type='hidden' name='rcid' value='$rcid'>";
        print MyGetText(54)." : <input type='text' name='configname' value='$rcname' size=64 maxlength=64><br/>"; // New name
        print "<br/><input type='submit' value='".MyGetText(52)."'>&nbsp;"; // OK
        print "<input type='button' value='".MyGetText(53)."' onclick='GoBack();'>"; // cancel
        print "</form>";
      }
    }

?>
    </body>
</html>

