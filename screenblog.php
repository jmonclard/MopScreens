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

  
  session_start();
  date_default_timezone_set('Europe/Paris');
  include_once('functions.php');
  redirectSwitchUsers();

  include_once('lang.php');
  $_SESSION['CurrentLanguage'] = isset($_SESSION['CurrentLanguage']) ? $_SESSION['CurrentLanguage'] : autoSelectLanguage(array('fr','en','sv'),'en');
  
  include_once('screenfunctions.php');
  include_once('config.php');
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style>
		  span.blogtime
		  {
			font-size: 9px;
			margin-right: 8px;
		  }
		  
		  ul.blogview
		  {
			list-style: none;
		  }
		  
		  ul.blogview li
		  {
			padding-bottom: 6px;
			font-size: 16px;
		  }
		  
		  li.blogrecent
		  {
			color: #333399;
		  }
        </style>
        <title>Screen Blog edit</title>
    </head>
    <body>
<?php

  $PHP_SELF = $_SERVER['PHP_SELF'];
  ConnectToDB();
  mysql_query("SET NAMES 'utf8';");
  mb_internal_encoding('UTF-8');
  $rcid = 0;
  $sql = "SELECT rcid FROM resultconfig WHERE active=1";
  $res = mysql_query($sql);
  if (mysql_num_rows($res) > 0)
  {
    $r = mysql_fetch_array($res);
    $rcid = $r['rcid'];
  }
  $action = ((isset($_GET['action'])) ? trim($_GET['action']) : "");
  
  if((isset($_POST['blogsubmit'])) && (isset($_POST['blogcontent'])))
  {
	  $content = trim($_POST['blogcontent']);
	  $sql = 'INSERT INTO resultblog (rcid, text) VALUES ('.$rcid.', \''.mysql_real_escape_string($content).'\')';
	  $res = mysql_query($sql);
	  if($res)
	  {
		  echo '<p><strong>Les données ont bien été envoyées</strong></p>';
	  }
  }
  else
  if($action == "clear")
  {
    $sql = 'DELETE FROM resultblog WHERE rcid='.$rcid;
	  $res = mysql_query($sql);
    if($res)
	  {
		  echo '<p><strong>Les données ont bien été effacées</strong></p>';
	  }
  }
?>
		<div>
			<p><?php echo MyGetText(108); ?></p>
			<form method="post" action="screenblog.php">
				<textarea name="blogcontent" cols="100" rows="10"></textarea>
				<br />
				<input type="submit" value="Envoyer" name="blogsubmit" />
			</form>
		</div>
		<div>
			<ul class="blogview" id="bloglist0">
			</ul>
		</div>
        <script type="text/javascript">
			var rcid = <?php echo $rcid; ?>;
            window.onload = function() 
            {
            }
			function blogView(mytab, panel)
			{
				var content = '';
				if(document.getElementById('bloglist' + panel))
				{
				  for(i=0;i<mytab.length;i++)
				  {
					{
					  content += '<li><span class="blogtime">' + mytab[i][2] + '</span>' + mytab[i][1] + '</li>';
					  //content += '<li><span class="blogtime">' + d.toLocaleTimeString() + '</span>' + mytab[i][1] + '</li>';
					}
				  }
				  document.getElementById('bloglist' + panel).innerHTML = content;
				}
			}
			function updateBlog()
			{
			  var panel = 0;
			  var xmlhttp = null;
			  if (window.XMLHttpRequest)
			  {// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp = new XMLHttpRequest();
			  }
			  else
			  {// code for IE6, IE5
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			  }
			  xmlhttp.onreadystatechange = function()
			  {
				if((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
				{
				  myblog = eval(xmlhttp.responseText);
				  blogView(myblog, panel);
				}
			  }
			  xmlhttp.open("GET", "aj_refreshblog.php?rcid=" + rcid + 
								  "&limit=20", false);
			  xmlhttp.send();
			}
			function create_refresh_blog()
			{
			  window.setInterval(updateBlog, 10*1000);
			}
			create_refresh_blog();
			updateBlog();
      
      function delBlog(prompt_text)
      {
          if(confirm(prompt_text+" ?"))
          {
              location.replace("screenblog.php?action=clear");
          }
      }
        </script>
        
        <br />
        <a href="screenconfig.php">Back</a>&nbsp;&nbsp;&nbsp;<a href="#" onclick="delBlog('Clear blog');return false;">Clear</a>
        <hr />
        
    </body>
</html>

