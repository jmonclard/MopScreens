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
//  include_once('functions.php');
  include_once('lang.php');

  session_start();
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

    if (file_exists('pictures/serverip.txt'))
    {
      $severipfile = fopen('pictures/serverip.txt', 'r');
      $ligne = fgets($severipfile);
      fclose($severipfile);
      $adr = explode(".",$ligne);
      $ip1=$adr[0];
      $ip2=$adr[1];
      $ip3=$adr[2];
      $ip4=$adr[3];
    }
    else
    {
      $ip1=192;
      $ip2=168;
      $ip3=0;
      $ip4=56;
    }
    

    print "<form method=GET action='screenconfig.php'>";
    print "<input type='hidden' name='action' value='serverip'>";
    print MyGetText(70);
    print " : <input type='text' name='ip1' value='$ip1' size=3 maxlength=3>";
    print " . <input type='text' name='ip2' value='$ip2' size=3 maxlength=3>";
    print " . <input type='text' name='ip3' value='$ip3' size=3 maxlength=3>";
    print " . <input type='text' name='ip4' value='$ip4' size=3 maxlength=3><br>";
    print "<br/><input type='submit' value='".MyGetText(52)."'>&nbsp;"; // OK
    print "<input type='button' value='".MyGetText(53)."' onclick='GoBack();'>"; // cancel
    print "</form>";
    
?>
    </body>
</html>

