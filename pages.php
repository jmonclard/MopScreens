<?php
    $ip=$_SERVER['REMOTE_ADDR'];
    $ipnb=explode('.',$ip);
    if (($ipnb[0]!='192')||($ipnb[1]!='168')||($ipnb[2]!='0')||($ipnb[3]=='20'))
    {
        header("Location: http://192.168.0.10");
        die();
    }

    include_once('functions.php');
    include_once('screenfunctions.php');
    session_start();

    $PHP_SELF = $_SERVER['PHP_SELF'];
    ConnectToDB();

    $screenIndex = isset($_GET['p']) ? intval($_GET['p']) : 1;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>CFCO 2014 (page <?php print $screenIndex; ?>)</title>
        <link rel="stylesheet" type="text/css" href="styles/co2014.css" />
       
        <script type="text/javascript">
            <!--
        var screenIndex;
        var rcid;
        var phpTitle;
        var phpcls;
        var phpcmpId;
        var phpleg;
        var phpord;
        var phpradio;
        var phpnumbercls;
        var phpupdateduration;
        var categorieIndex = [0, 0];
        var after_decrement_counter = [5, 5];
        var before_decrement_counter = [5, 5];
        
        var CST_REPEAT = 50; // /10 de secondes
            
        window.onload = function() 
        { 
<?php
        $arr_cls = array();
        $sql = "SELECT rcid FROM resultconfig WHERE active=1";
        $res = mysql_query($sql);
        if (mysql_num_rows($res) > 0)
        {
            $r = mysql_fetch_array($res);
            $rcid=$r['rcid'];
            
            $sql = "SELECT * FROM resultscreen WHERE rcid=$rcid AND sid=$screenIndex";
            $res = mysql_query($sql);
            if (mysql_num_rows($res) > 0)
            {
                // Récupération de la configuration utilisateur
                $r = mysql_fetch_array($res);
                $cid=$r['cid'];
                $title=stripslashes($r['title']);
                $titlesize=$r['titlesize'];
                $titlecolor=$r['titlecolor'];
                $subtitle=stripslashes($r['subtitle']);
                $subtitlesize=$r['subtitlesize'];
                $subtitlecolor=$r['subtitlecolor'];
                $titleleftpict=$r['titleleftpict'];
                $titlerightpict=$r['titlerightpict'];
                $screenmode=$r['screenmode'];

                $fullcontent=$r['fullcontent'];
                $fullpict=$r['fullpict'];
                $fulltxt=stripslashes($r['fulltxt']);
                $fulltxtsize=$r['fulltxtsize'];
                $fulltxtcolor=$r['fulltxtcolor'];
                $fullhtml=$r['fullhtml'];
                $fullfixedlines=$r['fullfixedlines'];
                $fullscrolledlines=$r['fullscrolledlines'];
                $fullscrolltime=$r['fullscrolltime'];
                $fullscrollbeforetime = $r['fullscrollbeforetime'];
                $fullscrollaftertime = $r['fullscrollaftertime'];
                $fullupdateduration=$r['fullupdateduration'];

                $leftcontent=$r['leftcontent'];
                $leftpict=$r['leftpict'];
                $lefttxt=stripslashes($r['lefttxt']);
                $lefttxtsize=$r['lefttxtsize'];
                $lefttxtcolor=$r['lefttxtcolor'];
                $lefthtml=$r['lefthtml'];
                $leftfixedlines=$r['leftfixedlines'];
                $leftscrolledlines=$r['leftscrolledlines'];
                $leftscrolltime=$r['leftscrolltime'];
                $leftscrollbeforetime = $r['leftscrollbeforetime'];
                $leftscrollaftertime = $r['leftscrollaftertime'];
                $leftupdateduration=$r['leftupdateduration'];

                $rightcontent=$r['rightcontent'];
                $rightpict=$r['rightpict'];
                $righttxt=stripslashes($r['righttxt']);
                $righttxtsize=$r['righttxtsize'];
                $righttxtcolor=$r['righttxtcolor'];
                $righthtml=$r['righthtml'];
                $rightfixedlines=$r['rightfixedlines'];
                $rightscrolledlines=$r['rightscrolledlines'];
                $rightscrolltime=$r['rightscrolltime'];
                $rightscrollbeforetime = $r['rightscrollbeforetime'];
                $rightscrollaftertime = $r['rightscrollaftertime'];
                $rightupdateduration=$r['rightupdateduration'];
                
                $classLeft = Array();
                $classNameLeft = Array();
                $classRight = Array();
                $classNameRight = Array();
                if(($screenmode == CST_SCREENMODE_DIVISE) || (($screenmode == CST_SCREENMODE_FULL) && ($fullcontent == CST_CONTENT_RELAIS)))
                {
                    //-----------------------------------------------------------------
                    // Récupération des catégories gauche et droite
                    $sql = "SELECT id FROM resultclass WHERE cid=$cid AND rcid=$rcid AND sid=$screenIndex AND panel=1";
                    $res = mysql_query($sql);
                    if (mysql_num_rows($res) > 0)
                    {
                        while ($r = mysql_fetch_array($res))
                        {
                            $myid = $r['id'];
                            $classLeft[]=$myid;
                            
                            $sql = "SELECT name FROM mopclass WHERE cid=$cid AND id=$myid";
                            $resname = mysql_query($sql);
                            if (mysql_num_rows($resname) > 0)
                            {
                                if ($rname = mysql_fetch_array($resname))
                                {
                                    $classNameLeft[] = $rname['name'];
                                }
                                else
                                {
                                    $classNameLeft[] = $myid;
                                }
                            }
                            else
                            {
                                $classNameLeft[] = $myid;
                            }
                        }
                    }
                    
                    if($screenmode == CST_SCREENMODE_DIVISE)
                    {
                        $sql = "SELECT id FROM resultclass WHERE cid=$cid AND rcid=$rcid AND sid=$screenIndex AND panel=2";
                        $res = mysql_query($sql);
                        if (mysql_num_rows($res) > 0)
                        {
                            while ($r = mysql_fetch_array($res))
                            {
                                $myid = $r['id'];
                                $classRight[]=$myid;
                                
                                $sql = "SELECT name FROM mopclass WHERE cid=$cid AND id=$myid";
                                $resname = mysql_query($sql);
                                if (mysql_num_rows($resname) > 0)
                                {
                                    if ($rname = mysql_fetch_array($resname))
                                    {
                                        $classNameRight[] = $rname['name'];
                                    }
                                    else
                                    {
                                        $classNameRight[] = $myid;
                                    }
                                }
                                else
                                {
                                    $classNameRight[] = $myid;
                                }
                            }
                        }
                    }
                }
                
                $sql_classes = array(-1);
                if(is_array($classRight))
                    $sql_classes = array_merge($classRight, $sql_classes);
                if(is_array($classLeft))
                    $sql_classes = array_merge($classLeft, $sql_classes);
                $sql = 'SELECT cls, COUNT(*) AS nb FROM mopcompetitor WHERE cid='.$cid.' AND cls IN('.implode(', ', $sql_classes).') GROUP BY cid, cls';
                $res = mysql_query($sql);
                while($data = mysql_fetch_array($res))
                {
                    $arr_cls[$data['cls']] = $data['nb'];
                }
            }
        }
        
        /*
        
        /////////////////////////
        ////////////////////////
        // récuperation de la configuration utilisateur
        //$configuration = array($configGauche, $configDroite);
        
        $panelIndex = isset($_GET['panelIndex']) ? intval($_GET['panelIndex']) : 0;
        //$configSouhaitee = $configuration[$panelIndex];

        global $courses;
        
        
        $cats = $courses[$configGauche[0]]["categories"];
        
        foreach($cats as $cat)
        {
            if ($cat["className"] == $configGauche[1])
            {
                $cls[0] = intval($cat["classId"]);
            }
        }
      
        $cmpId[0] = $courses[$configGauche[0]]["competitionId"];
        $numlegs[0] = null;
        $leg[0] = 0;
        $ord[0] = 0;
        $radio[0] = "finish";
        $cats = $courses[$configDroite[0]]["categories"];

        foreach($cats as $cat)
        {
            if ($cat["className"] == $configDroite[1])
            {
                $cls[1] = intval($cat["classId"]);
            }
        }
      
        $cmpId[1] = $courses[$configDroite[0]]["competitionId"];
        $numlegs[1] = null;
        $leg[1] = 0;
        $ord[1] = 0;
        $radio[1] = "finish";



        defineVariableArr("phpTitle", $configGauche[1], $configDroite[1]);
        defineVariableArr("phpcls", $cls[0], $cls[1]);
        defineVariableArr("phpcmpId", $cmpId[0], $cmpId[1]);
        defineVariableArr("phpleg", $leg[0], $leg[1]);
        defineVariableArr("phpord", $ord[0], $ord[1]);
        defineVariableArr("phpradio", $radio[0], $radio[1]);
        */
        
        if($screenmode == CST_SCREENMODE_DIVISE)
        {
            defineVariableArr2x("phpTitle", $classNameLeft, $classNameRight);
            defineVariableArr2x("phpcls", $classLeft, $classRight);
            
            defineVariableArr("phpscrolltime", $leftscrolltime, $rightscrolltime);
            defineVariableArr("phpfixedlines", $leftfixedlines, $rightfixedlines);
            defineVariableArr("phpscrolledlines", $leftscrolledlines, $rightscrolledlines);
            defineVariableArr("phpscrollaftertime", $leftscrollaftertime, $rightscrollaftertime);
            defineVariableArr("phpscrollbeforetime", $leftscrollbeforetime, $rightscrollbeforetime);
            defineVariableArr("phpupdateduration", $leftupdateduration, $rightupdateduration);
        }
        else
        /*if(($screenmode == CST_SCREENMODE_FULL) && ($fullcontent == CST_CONTENT_RELAIS))*/
        {
            defineVariableArr2x("phpTitle", $classNameLeft, $classNameLeft);
            defineVariableArr2x("phpcls", $classLeft, $classLeft);
            
            defineVariableArr("phpscrolltime", $fullscrolltime, $fullscrolltime);
            defineVariableArr("phpfixedlines", $fullfixedlines, $fullfixedlines);
            defineVariableArr("phpscrolledlines", $fullscrolledlines, $fullscrolledlines);
            defineVariableArr("phpscrollaftertime", $fullscrollaftertime, $fullscrollaftertime);
            defineVariableArr("phpscrollbeforetime", $fullscrollbeforetime, $fullscrollbeforetime);
            defineVariableArr("phpupdateduration", $fullupdateduration, $fullupdateduration);
        }
        
        defineVariableArr("phpcmpId", $cid, $cid);
        defineVariableArr("phpleg", 1, 1);
        defineVariableArr("phpord", 0, 0);
        defineVariableArr("phpradio", 'finish', 'finish');
        
        defineVariableArrFromArr("phpnumbercls", $arr_cls);
        
        defineVariable("screenIndex", $screenIndex);
        defineVariable("rcid", $rcid);
        
?>
        phpscrolledlines[0] = parseInt(phpscrolledlines[0], 10);
        phpscrolledlines[1] = parseInt(phpscrolledlines[1], 10);
        phpfixedlines[0] = parseInt(phpfixedlines[0], 10);
        phpfixedlines[1] = parseInt(phpfixedlines[1], 10);
        phpscrolltime[0] = parseInt(phpscrolltime[0], 10);
        phpscrolltime[1] = parseInt(phpscrolltime[1], 10);
        
        phpscrollbeforetime[0] = parseInt(phpscrollbeforetime[0], 10);
        phpscrollbeforetime[1] = parseInt(phpscrollbeforetime[1], 10);
        
        phpscrollaftertime[0] = parseInt(phpscrollaftertime[0], 10);
        phpscrollaftertime[1] = parseInt(phpscrollaftertime[1], 10);
        
        if(phpscrolltime[0] <= 0)
            phpscrolltime[0] = 10;
        if(phpscrolltime[1] <= 0)
            phpscrolltime[1] = 10;
        
        if(phpscrollaftertime[0] <= 0)
            phpscrollaftertime[0] = 50;
        if(phpscrollaftertime[1] <= 0)
            phpscrollaftertime[1] = 50;
        
        if(phpscrollbeforetime[0] <= 0)
            phpscrollbeforetime[0] = 50;
        if(phpscrollbeforetime[1] <= 0)
            phpscrollbeforetime[1] = 50;
        
        after_decrement_counter[0] = phpscrollaftertime[0] / phpscrolltime[0];
        after_decrement_counter[1] = phpscrollaftertime[1] / phpscrolltime[1];
        
        before_decrement_counter[0] = phpscrollbeforetime[0] / phpscrolltime[0];
        before_decrement_counter[1] = phpscrollbeforetime[1] / phpscrolltime[1];
<?php            
if($screenmode == CST_SCREENMODE_DIVISE)
{
    if(($leftcontent == CST_CONTENT_RESULT) || ($rightcontent == CST_CONTENT_RESULT) || 
			($leftcontent == CST_CONTENT_START) || ($rightcontent == CST_CONTENT_START))
    {
?>
            updatePage();
            updateTables();
            updateStarts();
<?php
            if($leftcontent == CST_CONTENT_RESULT)
            {
?>
                updateDisplay1();
<?php
            }
            else
            if($leftcontent == CST_CONTENT_START)
            {
?>
                updateDisplayStart1();
<?php
            }
            if($rightcontent == CST_CONTENT_RESULT)
            {
?>
                updateDisplay2();
<?php
            }
            else
            if($rightcontent == CST_CONTENT_START)
            {
?>
                updateDisplayStart2();
<?php
            }
?>
            create_refresh_table();
            create_refresh_start();
            create_refresh_display();
<?php
    }
}
else
if(($screenmode == CST_SCREENMODE_FULL) && ($fullcontent == CST_CONTENT_RELAIS))
{
?>
            updatePage();
            updateRelais();
            create_refresh_relais();
            
            create_refresh_display();
<?php
}
?>
            create_refresh_page();
        }
        
        var ATTENTE_BASE_s = 5;
        var ATTENTE_PAGE_s = 10;

        var dataArray = new Array(2);
        var tableUpdated = new Array(2);
        var displayScrollIndex = [1, 1];
        
        var bfirst = true;
        var mytime = 0;
        var nowtime = 0;

        function isUndefined(o)
        {
            return typeof o == "undefined";
        }
        
        function updatePage()
        {
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
                    var temp = eval(xmlhttp.responseText)
                    if((!bfirst) && (parseInt(temp, 10) != mytime))
                    {
                        window.location = self.location;
                    }
                    else
                    {
                        mytime = parseInt(temp[0], 10);
                        nowtime = parseInt(temp[1], 10);
                        bfirst = false;
                        //alert('**' + mytime + '--' + nowtime);
                    }
                }
            }
            xmlhttp.open("GET", "aj_refreshpage.php?rcid=" + rcid + "&sid=" + screenIndex, false);
            xmlhttp.send();
        }

        function updateTable(panelIndex)
        {
            if  ((panelIndex === 0) || (panelIndex === 1))
            {
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
                        //alert(xmlhttp.responseText);
                        dataArray[panelIndex] = eval(xmlhttp.responseText);
                        tableUpdated[panelIndex] = true;
                    }
                }
                    
                xmlhttp.open("GET", "aj_refreshtable.php?cls=" + phpcls[panelIndex][categorieIndex[panelIndex]] +
                                    "&cmpId=" + phpcmpId[panelIndex] +
                                    "&leg=" + phpleg[panelIndex] +
                                    "&ord=" + phpord[panelIndex] +
                                    "&radio=" + phpradio[panelIndex] +
                                    "&rcid=" + rcid +
                                    "&sid=" + screenIndex, false);
                xmlhttp.send();
            }
        }
        
        function updateStart(panelIndex)
        {
            if  ((panelIndex === 0) || (panelIndex === 1))
            {
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
                        //alert(xmlhttp.responseText);
                        dataArray[panelIndex] = eval(xmlhttp.responseText);
                        tableUpdated[panelIndex] = true;
                    }
                }
                xmlhttp.open("GET", "aj_refreshstart.php?cls=" + phpcls[panelIndex][categorieIndex[panelIndex]] +
                                    "&cmpId=" + phpcmpId[panelIndex] +
                                    "&leg=" + phpleg[panelIndex] +
                                    "&ord=" + phpord[panelIndex] +
                                    "&radio=" + phpradio[panelIndex] +
                                    "&rcid=" + rcid +
                                    "&sid=" + screenIndex, false);
                xmlhttp.send();
            }
        }
        
        function updateRelais()
        {
            var panelIndex = 0;
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
                    //alert(xmlhttp.responseText);
                    dataArray[panelIndex] = eval(xmlhttp.responseText);
                    tableUpdated[panelIndex] = true;
                }
            }
                
            xmlhttp.open("GET", "aj_refreshrelais.php?cls=" + phpcls[panelIndex][categorieIndex[panelIndex]] +
                                "&cmpId=" + phpcmpId[panelIndex] +
                                "&leg=" + phpleg[panelIndex] +
                                "&ord=" + phpord[panelIndex] +
                                "&radio=" + phpradio[panelIndex] +
                                    "&rcid=" + rcid +
                                    "&sid=" + screenIndex, false);
            xmlhttp.send();
        }
        
        function updateTables()
        {
<?php
            if($leftcontent == CST_CONTENT_RESULT)
            {
?>
                updateTable(0);
<?php
            }
            if($rightcontent == CST_CONTENT_RESULT)
            {
?>
            	updateTable(1);
<?php
            }
?>
        }
        
        function updateStarts()
        {
<?php
            if($leftcontent == CST_CONTENT_START)
            {
?>
                updateStart(0);
<?php
            }
            if($rightcontent == CST_CONTENT_START)
            {
?>
                updateStart(1);
<?php
            }
?>
        }
        
        function ConvertToNiceHtmlTableRow(panelIndex, identifiant)
        {
            var r = "";
            var bUpdateNeeded = false;
            
            var prefix_class = 'td';
            var position = 0;
            var count = 5;

            if(identifiant === 'start')
            {
                prefix_class = 'tdi';
                phpscrolledlines[panelIndex] = phpfixedlines[panelIndex] + phpscrolledlines[panelIndex];
                phpfixedlines[panelIndex] = 0;
                count = 6;
            }
            else
            if(identifiant === 'result')
            {
                prefix_class = 'td';
                count = 11;//9;
            }
            else
            if(identifiant === 'relais')
            {
                prefix_class = 'tdrelais';
                count = 26;//20;
            }
            
            r += '<table class="fixedTable">\r\n';
            // affichage du header
            r += '<thead id="fixedHeader' + panelIndex + '" class="fixedHeader">\r\n';
            r += '<tr class="normalRow">\r\n';
            
            var c;

            var txt_nb = '';
			var length=0;
			if (tableUpdated[panelIndex])
			{
				length = dataArray[panelIndex].length;
            }

            for(c=0;c<phpTitle[panelIndex].length;c++)
            {
                if(categorieIndex[panelIndex] == c)
                {
                    //if((identifiant === 'result') && (length > 0))
					if(identifiant === 'result')
                    {
                        txt_nb = length + ' / ' + phpnumbercls[phpcls[panelIndex][c]];
                    }
                    r += '<th class="activeOnglet">' + phpTitle[panelIndex][c] + ' <span class="number_class">' + txt_nb + '</span></th>\r\n';
                }
                else
                {
                    if((identifiant === 'result') || (identifiant === 'relais'))
                    {
                        r += '<th class="inactiveOnglet">' + phpTitle[panelIndex][c] + /*' <span class="number_class">(' + phpnumbercls[phpcls[panelIndex][c]] + ')</span>' +*/ '</th>\r\n';
                    }
                    else
                    if(identifiant === 'start')
                    {
                        r += '<th class="inactiveOnglet">' + phpTitle[panelIndex][c] + ' <span class="number_class">(' + phpnumbercls[phpcls[panelIndex][c]] + ')</span>' + '</th>\r\n';
                    }
                }
            }
            
            r += '</tr>\r\n';
            r += '</thead>\r\n';
            
            var nf = '';

			

            if (length > 0)             

            {
                r +='<tbody class="scrollContent">';

                // lignes fixes
                var endPosition = phpfixedlines[panelIndex];
                if (length < (phpfixedlines[panelIndex] + phpscrolledlines[panelIndex]))
                {
                    endPosition = length;
                }
                var line = eval(dataArray[panelIndex][0]);
                count = line.length;
                
                if(identifiant === 'relais')
                {
                    r += '<tr>\r\n';
                    r += '<th class="entete_relais">&nbsp;</th>\r\n';
                    r += '<th class="entete_relais">&nbsp;</th>\r\n';
                    r += '<th class="entete_relais" colspan="4">Relayeur 1</th>\r\n';
                    r += '<th class="entete_relais" colspan="2">Après R1</th>\r\n';
                    if(line[5] > 1)
                    {
                        r += '<th class="entete_relais" colspan="4">Relayeur 2</th>\r\n';
                        r += '<th class="entete_relais" colspan="2">Après R2</th>\r\n';
                    }
                    if(line[5] > 2)
                    {
                        r += '<th class="entete_relais" colspan="4">Relayeur 3</th>\r\n';
                        r += '<th class="entete_relais" colspan="2">Après R3</th>\r\n';
                    }
                    r += '</tr>\r\n';
                }
                
                while((position < endPosition) && (position<length))
                {
                    line = eval(dataArray[panelIndex][position++]);
                    if(line != null)
                    {
                        if(identifiant === 'result')
                        {
                            if(line[0] < 1)
                            {
                                nf = ' nonfini';
                            }
                            else
                            {
                                nf = '';
                            }
                        }
                        if((identifiant === 'result') || (identifiant === 'relais'))
                        {
                            if(line[1] > nowtime - (60 * phpupdateduration[panelIndex]))
                            {
                                nf += ' updated';
                            }
                        }
                        
                        var cl = ((position % 2) ? 'alternateRow' : 'normalRow');
                        r += '<tr class="' + cl + nf + '">\r\n';
                        for(var e in line)
                        {
                            if(identifiant === 'result')
                            {
                                if(e > 1)
                                {
                                    if(e == (count - 1))
                                    {
                                        r += '<td class="tdtimediff">' + line[e] + '</td>\r\n';
                                    }
                                    else
                                    if(e == (count - 2))
                                    {
                                        r += '<td class="tdtimeresult">' + line[e] + '</td>\r\n';
                                    }
                                    else
                                    {
                                        r += '<td class="'+ prefix_class + (e-2) +'">' + line[e] + '</td>\r\n';
                                    }
                                }
                            }
                            else
                            if(identifiant === 'relais')
                            {
                                if(e > 5)
                                {
                                    /*if(e == (count - 1))
                                    {
                                        r += '<td class="tdtimediff">' + line[e] + '</td>\r\n';
                                    }
                                    else
                                    if(e == (count - 2))
                                    {
                                        r += '<td class="tdtimeresult">' + line[e] + '</td>\r\n';
                                    }
                                    else*/
                                    if(((line[5] > 1) && (e < 20)) || ((line[5] > 2) && (e < 26)) || e < 13)
                                    {
                                        r += '<td class="'+ prefix_class + (e-6) +'">' + line[e] + '</td>\r\n';
                                    }
                                }
                            }
                            else
                            {
                                r += '<td class="'+ prefix_class + e +'">' + line[e] + '</td>\r\n';
                            }
                        }
                        r += '</tr>\r\n';
                    }
                }
                
                
                if (length >= (phpfixedlines[panelIndex] + phpscrolledlines[panelIndex]))
                {
                    if((identifiant === 'result') || (identifiant === 'relais'))
                    {
                        r += "</tbody>\r\n";
                        r += '</table>\r\n';
                    
                        r += '<hr />';
                        r += '<table class="scrollTable">\r\n';
                        r +='<tbody class="scrollContent">';
                    }
                    
                    var startPosition = displayScrollIndex[panelIndex];
                    var endPosition = startPosition + phpscrolledlines[panelIndex];
                    nf = '';
                    for(position = startPosition; position < endPosition; position++)
                    {
                        if (position < length)
                        {
                            var line = eval(dataArray[panelIndex][position]);
                            if(line != null)
                            {
                                var cl = ((position % 2) ? 'alternateRow' : 'normalRow');
                                if(identifiant === 'result')
                                {
                                    if(line[0] < 1)
                                    {
                                        nf = ' nonfini';
                                    }
                                    else
                                    {
                                        nf = '';
                                    }
                                }
                                if((identifiant === 'result') || (identifiant === 'relais'))
                                {
                                    if(line[1] > nowtime - (60 * phpupdateduration[panelIndex]))
                                    {
                                        nf += ' updated';
                                    }
                                }
                                r += '<tr class="' + cl + nf + '">\r\n';
                                for(var e in line)
                                {
                                    if(identifiant === 'result')
                                    {
                                        if(e > 1)
                                        {
                                            if(e == (count - 1))
                                            {
                                                r += '<td class="tdtimediff">' + line[e] + '</td>\r\n';
                                            }
                                            else
                                            if(e == (count - 2))
                                            {
                                                r += '<td class="tdtimeresult">' + line[e] + '</td>\r\n';
                                            }
                                            else
                                            {
                                                r += '<td class="'+ prefix_class + (e-2) +'">' + line[e] + '</td>\r\n';
                                            }
                                        }
                                    }
                                    else
                                    if(identifiant === 'relais')
                                    {
                                        if(e > 5)
                                        {
                                            /*if(e == (count - 1))
                                            {
                                                r += '<td class="tdtimediff">' + line[e] + '</td>\r\n';
                                            }
                                            else
                                            if(e == (count - 2))
                                            {
                                                r += '<td class="tdtimeresult">' + line[e] + '</td>\r\n';
                                            }
                                            else*/
                                            if(((line[5] > 1) && (e < 20)) || ((line[5] > 2) && (e < 26)) || e < 13)
                                            {
                                                r += '<td class="'+ prefix_class + (e-6) +'">' + line[e] + '</td>\r\n';
                                            }
                                        }
                                    }
                                    else
                                    {
                                        r += '<td class="'+ prefix_class + e +'">' + line[e] + '</td>\r\n';
                                    }
                                }
                                r += '</tr>\r\n';
                            }
                        }
                        else
                        {
                            var cl = ((position % 2) ? 'alternateRow' : 'normalRow');
                            nf = '';
                            r += '<tr class="' + cl + nf + '">\r\n';
                            if(identifiant === 'result')
                            {
                                count1 = count - 2;
                            }
                            else
                            if(identifiant === 'relais')
                            {
                                count1 = count - 6;
                            }
                            else
                            {
                                count1 = count;
                            }
                            for(var e=0;e<count1;e++)
                            {
                                if((identifiant === 'result') && (e == (count1 - 1)))
                                {
                                    r += '<td class="tdtimediff">&nbsp;</td>\r\n';
                                }
                                else
                                if((identifiant === 'result') && (e == (count1 - 2)))
                                {
                                    r += '<td class="tdtimeresult">&nbsp;</td>\r\n';
                                }
                                else
                                {
                                    r += '<td class="'+ prefix_class + e +'">&nbsp;</td>\r\n';
                                }
                            }
                            r += '</tr>\r\n';
                        }
                    }
                    
                    if(before_decrement_counter[panelIndex] <= 0)
                    {
                        displayScrollIndex[panelIndex]++;
                    }
                    else
                    {
                        before_decrement_counter[panelIndex]--;
                    }
                    if (displayScrollIndex[panelIndex] > length-5)
                    {
                        before_decrement_counter[panelIndex] = phpscrollbeforetime[panelIndex] / phpscrolltime[panelIndex];
                        bUpdateNeeded = true;
                        /*displayScrollIndex[panelIndex] = 0;
                        after_decrement_counter[panelIndex] = 0;*/
                    }
                    r += "</tbody>\r\n";
                    r += '</table>\r\n';
                }
                else
                {
                    bUpdateNeeded = true;
                    nf = '';
                    if(identifiant === 'result')
                    {
                        count = count - 2;
                    }
                    else
                    if(identifiant === 'relais')
                    {
                        count = count - 6;
                    }
                    while(position < (phpfixedlines[panelIndex] + phpscrolledlines[panelIndex]))
                    {
                        var cl = ((position % 2) ? 'alternateRow' : 'normalRow');
                        r += '<tr class="' + cl + nf + '">\r\n';
                        for(var e=0;e<count;e++)
                        {
                            if((identifiant === 'result') && (e == (count - 1)))
                            {
                                r += '<td class="tdtimediff">&nbsp;</td>\r\n';
                            }
                            else
                            if((identifiant === 'result') && (e == (count - 2)))
                            {
                                r += '<td class="tdtimeresult">&nbsp;</td>\r\n';
                            }
                            else
                            {
                                r += '<td class="'+ prefix_class + e +'">&nbsp;</td>\r\n';
                            }
                        }
                        r += '</tr>\r\n';
                        position++;
                    }
                    r += "</tbody>\r\n";
                    r += '</table>\r\n';
                }
            }
            else
            {
                r +='<tbody class="scrollContent">';
                position = 0;
                nf = '';
                if(identifiant === 'result')
                {
                    count = count - 2;
                }
                else
                if(identifiant === 'relais')
                {
                    count = count - 6;
                }
                while(position < (phpfixedlines[panelIndex] + phpscrolledlines[panelIndex]))
                {
                    var cl = ((position % 2) ? 'alternateRow' : 'normalRow');
                    r += '<tr class="' + cl + nf + '">\r\n';
                    for(var e=0;e<count;e++)
                    {
                        if((identifiant === 'result') && (e == (count - 1)))
                        {
                            r += '<td class="tdtimediff">&nbsp;</td>\r\n';
                        }
                        else
                        if((identifiant === 'result') && (e == (count - 2)))
                        {
                            r += '<td class="tdtimeresult">&nbsp;</td>\r\n';
                        }
                        else
                        {
                            r += '<td class="'+ prefix_class + e +'">&nbsp;</td>\r\n';
                        }
                    }
                    r += '</tr>\r\n';
                    position++;
                }
                r += "</tbody>\r\n";
                r += '</table>\r\n';
                bUpdateNeeded = true;
                after_decrement_counter[panelIndex] = 0;
            }
            
            if(bUpdateNeeded)
            {
                after_decrement_counter[panelIndex]--;
                if(after_decrement_counter[panelIndex] <= 0)
                {
                    displayScrollIndex[panelIndex] = 0;
                    categorieIndex[panelIndex] = (categorieIndex[panelIndex] + 1) % phpcls[panelIndex].length;
                    tableUpdated[panelIndex] = false;
                    if(identifiant === 'start')
                    {
                        updateStart(panelIndex);
                    }
                    else
                    if(identifiant === 'result')
                    {
                        updateTable(panelIndex);
                    }
                    else
                    if(identifiant === 'relais')
                    {
                        updateRelais();
                    }
                    after_decrement_counter[panelIndex] = phpscrollaftertime[panelIndex] / phpscrolltime[panelIndex];
                }
            }
            else
            {
                //after_decrement_counter[panelIndex] = phpscrollaftertime[panelIndex] / phpscrolltime[panelIndex];
            }
            
            return r;
        }
        
        function updateDisplayStart1()
        {
            if(document.getElementById("tableContainer0"))
            {
                document.getElementById("tableContainer0").innerHTML = ConvertToNiceHtmlTableRow(0, 'start');
            }
        }
        function updateDisplayStart2()
        {
            if(document.getElementById("tableContainer1"))
            {
                document.getElementById("tableContainer1").innerHTML = ConvertToNiceHtmlTableRow(1, 'start');
            }            
        }

        function updateDisplay1()
        {
            if(document.getElementById("tableContainer0"))
            {
                document.getElementById("tableContainer0").innerHTML = ConvertToNiceHtmlTableRow(0, 'result');
            }
        }
        function updateDisplay2()
        {
            if(document.getElementById("tableContainer1"))
            {
                document.getElementById("tableContainer1").innerHTML = ConvertToNiceHtmlTableRow(1, 'result');
            }            
        }
        function updateDisplayRelais()
        {
            if(document.getElementById("tableContainer3"))
            {
                document.getElementById("tableContainer3").innerHTML = ConvertToNiceHtmlTableRow(0, 'relais');
            }
        }
        function create_refresh_display()
        {
<?php
            if($screenmode == CST_SCREENMODE_DIVISE)
            {
                if($leftcontent == CST_CONTENT_RESULT)
                {
?>
                    window.setInterval(updateDisplay1, phpscrolltime[0]*100);
<?php
                }
                else
                if($leftcontent == CST_CONTENT_START)
                {
?>
                    window.setInterval(updateDisplayStart1, phpscrolltime[0]*100);
<?php
                }
                if($rightcontent == CST_CONTENT_RESULT)
                {
?>
                    window.setInterval(updateDisplay2, phpscrolltime[1]*100);
<?php
                }
                else
                if($rightcontent == CST_CONTENT_START)
                {
?>
                    window.setInterval(updateDisplayStart2, phpscrolltime[1]*100);
<?php
                }
            }
            else
            if(($screenmode == CST_SCREENMODE_FULL) && ($fullcontent == CST_CONTENT_RELAIS))
            {
?>
                window.setInterval(updateDisplayRelais, phpscrolltime[0]*100);
<?php
            }            
?>
        }
        function create_refresh_table()
        {
            window.setInterval(updateTables, ATTENTE_BASE_s*1000);
        }
        function create_refresh_start()
        {
            window.setInterval(updateStarts, ATTENTE_BASE_s*1000);
        }
        function create_refresh_page()
        {
            window.setInterval(updatePage, ATTENTE_PAGE_s*1000);
        }
        function create_refresh_relais()
        {
            window.setInterval(updateRelais, ATTENTE_BASE_s*1000);
        }
        
            -->
        </script>
        
    </head>
    <body>
<?php
    $hauteur = 40;
?>
        <div style="float:right;height:<?php print $hauteur; ?>px;display:block;width:15%;text-align:right;">
<?php
            print displayTopPicture($titlerightpict, $hauteur); //'<img src="pictures/'.$titlerightpict.'" alt="" max-height="100%">';
?>
        </div>
        <div style="float:left;height:<?php print $hauteur; ?>px;display:block;width:15%;text-align:left;">
<?php
            print displayTopPicture($titleleftpict, $hauteur); //'<img src="pictures/'.$titleleftpict.'" alt="" max-height="100%">';
?>
        </div>
        <div style="float:left;height:<?php print $hauteur; ?>px;display:block;width:70%;text-align:center;">
<?php
            print '<div style="padding:0;margin:0;vertical-align:top;font-size:'.$titlesize.'px;color:#'.$titlecolor.';">'.$title.'</div>';
            print '<div style="padding:0;margin:0;vertical-align:top;font-size:'.$subtitlesize.'px;color:#'.$subtitlecolor.';">'.$subtitle.'</div>';    
?>
        </div>

        <div style="clear:both;"></div>
<?php
    switch($screenmode)
    {
        case CST_SCREENMODE_FULL:
            switch($fullcontent)
            {
                case CST_CONTENT_PICTURE:
                    print displayContentPicture($fullpict);
                break;
                case CST_CONTENT_TEXT:
                    print displayContentText($fulltxt, $fulltxtsize, $fulltxtcolor);
                break;
                case CST_CONTENT_HTML:
                    print displayContentHtml($fullhtml);
                break;
                case CST_CONTENT_RELAIS:
                    ?>
        <div style="padding:0;margin:0;display:block;width:100%">
            <div style="float:left;display:inline;min-width:100%;width:100%;" id="tableContainer3" class="tableContainer">
            </div>
        </div>
                    <?php
                break;
                default:
                break;
            }
        break;
        case CST_SCREENMODE_DIVISE:
?>
        <div style="padding:0;margin:0;display:block;width:100%">
            <div style="float:left;display:inline;min-width:50%;width:50%;">
<?php
            switch($leftcontent)
            {
                case CST_CONTENT_PICTURE:
                    print displayContentPicture($leftpict);
                break;
                case CST_CONTENT_TEXT:
                    print displayContentText($lefttxt, $lefttxtsize, $lefttxtcolor);
                break;
                case CST_CONTENT_HTML:
                    print displayContentHtml($lefthtml);
                break;
                case CST_CONTENT_START:
?>
                <div style="float:left;display:inline;min-width:100%;width:100%;" id="tableContainer0" class="tableContainer">
                </div>
<?php
                break;
                case CST_CONTENT_RESULT:
?>
                <div style="float:left;display:inline;min-width:100%;width:100%;" id="tableContainer0" class="tableContainer">
                </div>
<?php
                break;
                default:
                break;
            }
?>
            </div>
            <div style="float:left;display:inline;min-width:50%;width:50%;">
<?php
            switch($rightcontent)
            {
                case CST_CONTENT_PICTURE:
                    print displayContentPicture($rightpict);
                break;
                case CST_CONTENT_TEXT:
                    print displayContentText($righttxt, $righttxtsize, $righttxtcolor);
                break;
                case CST_CONTENT_HTML:
                    print displayContentHtml($righthtml);
                break;
                case CST_CONTENT_START:
?>
                <div style="float:left;display:inline;min-width:100%;width:100%;" id="tableContainer1" class="tableContainer">
                </div>
<?php
                break;
                case CST_CONTENT_RESULT:
?>
                <div style="float:left;display:inline;min-width:100%;width:100%;" id="tableContainer1" class="tableContainer">
                </div>
<?php
                break;
                default:
                break;
            }
?>
            </div>
        </div>
<?php
        break;
        default:
        break;
    }
?>
    </body>
</html>
