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


  include_once('functions.php');
  include_once('lang.php');
  session_start();

  $_SESSION['CurrentLanguage'] = isset($_SESSION['CurrentLanguage']) ? $_SESSION['CurrentLanguage'] : autoSelectLanguage(array('fr','en','sv'),'en');

  header('Content-type: text/html;charset=utf-8');

  $PHP_SELF = $_SERVER['PHP_SELF'];
  ConnectToDB();

  $SERVER_JSON_IP_ADDRESSES = array();
  // Catégories et ip pour les sélections
  $SERVER_JSON_IP_ADDRESSES["H"] = "192.168.0.78:4567";
  $SERVER_JSON_IP_ADDRESSES["F"] = "192.168.0.77:4567";
  $SERVER_JSON_IP_ADDRESSES["J"] = "192.168.0.79:4567";

  // Catégories et IP pour les finales
  $SERVER_JSON_IP_ADDRESSES["H-S"] = "192.168.0.78:4567";
  $SERVER_JSON_IP_ADDRESSES["F-S"] = "192.168.0.77:4567";
  $SERVER_JSON_IP_ADDRESSES["J-S"] = "192.168.0.79:4567";

  $cls = ((isset($_GET['cls'])) ? $_GET['cls'] : "0");
  $cmpId = ((isset($_GET['cmpId'])) ? $_GET['cmpId'] : "0");//competition
  
  
  $limit = ((isset($_GET['limit'])) ? $_GET['limit'] : "9999");
  $qualif = ((isset($_GET['qualif'])) ? $_GET['qualif'] : "0");

  $rcid = ((isset($_GET['rcid'])) ? $_GET['rcid'] : "0");//num configuration ecran
  $sid = ((isset($_GET['sid'])) ? $_GET['sid'] : "0");//screen ID
  
  
  $sql = 'UPDATE resultscreen SET panel1lastrefresh='.time().' WHERE rcid='.$rcid.' AND sid='.$sid;
  mysql_query($sql);

  $sql = 'SELECT name FROM mopclass WHERE cid ='.$cmpId.' AND id='.$cls;
  $res = mysql_query($sql);
  $course = "";
  if(mysql_num_rows($res) > 0)
  {
    $r = mysql_fetch_array($res);
    $course = $r['name'];
  }

  $maxCompetCount = 0;
  



function geco_getheaderinfo($contents)
{
  $status = false;
  //$_lasttime = "";
  $lasttime = "";
  $name = "";
  $results = array();

  $contents = utf8_encode($contents);
  $decoded = json_decode($contents, true);
  $status = //array_key_exists( "_lastTime", $decoded) &&
              array_key_exists( "lastTime", $decoded) &&
              array_key_exists( "name", $decoded) &&
              array_key_exists( "results", $decoded);
  if ($status)
  {
      //$_lasttime = $decoded["_lastTime"];
      $lasttime = $decoded["lastTime"];
      $name = $decoded["name"];
      $results = $decoded["results"];
  }

  return array($status, /*$_lasttime, */$lasttime, $name, $results);
}

function geco_getracenames($results)
{
  $status = true;
  $racenames = array();

  //print("<pre>".print_r($decoded,true)."</pre>");

  //print("Nb results=>".count($results)."<br>");
  for($i=0;$i<count($results);$i++)
  {
      $racenames[]=$results[$i]["name"];
  }

  return array($status, $racenames);
}

function geco_getrunners($results, $racename, $category=null)
{
  $status = false;
  $rankedrunners = array();
  $unrankedrunners = array();

  if ($category == null)
  {
      for($i=0;($status == false) && ($i<count($results));$i++)
      {
          //print("<br>Test avec ".$results[$i]["name"]."<br>");
          //print("<pre>".print_r($results[$i]["name"],true)."</pre>");
          $status = $results[$i]["name"] == $racename;
          if ($status)
          {
              $rankedrunners = $results[$i]["rankedRunners"];
              $unrankedrunners = $results[$i]["unrankedRunners"];
          }
      }
  }
  else
  {
      for($i=0;($status == false) && ($i<count($results));$i++)
      {
          foreach($results[$i]["rankedRunners"] as $key => $value)
          {
              if ($value["category"] == $category)
              {
                  $rankedrunners[] = $value;
              }
              //print("Key=".$key." Val=".$value["category"]."<br>");
          }
          foreach($results[$i]["unrankedRunners"] as $key => $value)
          {
              if ($value["category"] == $category)
              {
                  $unrankedrunners[] = $value;
              }
              //print("Key=".$key." Val=".$value["category"]."<br>");
          }
          //$rankedrunners = $results[$i]["rankedRunners"];
          //$unrankedrunners = $results[$i]["unrankedRunners"];
      }
      $status = true;
  }

  return array($status, $rankedrunners, $unrankedrunners);
}

function reorder_ShowO($totalResult, $selectioncount)
{
  global $maxCompetCount;
  
  $showo_data = array();

  //
  $first = 0;
  for($i=$maxCompetCount;$i>0;$i--)
  {
    foreach($totalResult as $key => $value)
    {
      if (($value->competCount == $i) && ($value->totalStatus == "OK"))
      {
        // bullons donc
        $index = $first;
        $found = false;
        while(($index < count($showo_data))&&(!$found))
        {
          // vrai si supérieur à moi
          $found = ($showo_data[$index]->totalTime > $value->totalTime);
          if (!$found)
          {
            $index++;
          }
        }

        array_splice( $showo_data, $index, 0, array($value)); //insérons cet élément
      }
    }
    $first = count($showo_data);
  }
  // traitement des oubliés
  for($i=$maxCompetCount;$i>0;$i--)
  {
    foreach($totalResult as $key => $value)
    {
      if (($value->competCount == $i) && ($value->totalStatus != "OK"))
      {
        $showo_data[] = $value;
      }
    }
  }

  return $showo_data;
}

class ShowOResult
{
  var $name;
  var $club;
  var $competCount;
  var $times;
  var $penalties;
  var $status;
  var $totalTime;
  var $totalPenalties;
  var $totalStatus;
  var $ranks;

  function ShowOResult($_name, $_club)
  {
    $this->name = $_name;
    $this->club = $_club;
    $this->competCount = 0;
    $this->totalTime = 0;
    $this->totalPenalties = 0;
    $this->times = array();
    $this->penalties = array();
    $this->status = array();
    $this->totalStatus = "OK";
    $this->ranks = array();
  }

  function Register($selection, $status, $time = 0, $penalties = 0, $rank = 0)
  {
    global $maxCompetCount;
    if(!isset($this->times[$selection]))
    {
      $this->totalTime += $time / 1000;
      $this->totalPenalties += $penalties / 1000;
      $this->times[$selection] = $time / 1000;
      $this->penalties[$selection] = $penalties / 1000;
      $this->status[$selection] = $status;
      $this->ranks[$selection] = $rank;
      if ($this->totalStatus == "OK")
      {
        $this->totalStatus = $status;
      }
      $this->competCount++;
      if ($this->competCount > $maxCompetCount)
      {
        $maxCompetCount = $this->competCount;
      }
    }
    else
    {/*
      $this->totalTime = $this->totalTime + $time / 1000 - $this->times[$selection];
      $this->totalPenalties = $this->totalPenalties + $penalties / 1000 - $this->penalties[$selection];
      $this->times[$selection] = $time / 1000;
      $this->penalties[$selection] = $penalties / 1000;
      $this->status[$selection] = $status;
      $this->ranks[$selection] = $rank;
      if ($this->totalStatus == "OK")
      {
        $this->totalStatus = $status;
      }*/
    }
  }
}

function toHms($time_s)
{
  $retour = $time_s;
  if($time_s > 3600)
  {
    $retour = sprintf("%d:%02d:%02d", $time_s/3600, ($time_s/60)%60, $time_s%60);
  }
  else
  {
    $retour = sprintf("%02d:%02d", ($time_s/60)%60, $time_s%60);
  }
  return $retour;
}

function formatShowOResultsQualif($showo_out, $selectioncount, $limit = 99999)
{
  $head = true;
  print '[';
  $i = 0;
  foreach($showo_out as $row)
  {
    $i++;
    if ($head)
    {
        print("[");
        $head = false;
    }
    else
    {
        print(",[");
    }

    if ($row->totalStatus == "OK")
    {
      print("\"".$i."\",");
    }
    else
    {
      print("\"&nbsp;\",");
    }

    print("\"".$row->name."\",\"".$row->club."\",");
    for($selection=0;$selection<$selectioncount;$selection++)
    {
      if ((null != $row->times[$selection]) /*&& (null != $row->penalties[$selection])*/)
      {
        print("\"".toHms($row->times[$selection] - $row->penalties[$selection])."\",");
        print("\"".round($row->penalties[$selection] / CST_PENALTY_S)."\",");
        if ($row->status[$selection] == "OK")
        {
          print("\"(".$row->ranks[$selection].") ".toHms($row->times[$selection])."\",");
          //print("\"(999) ".toHms($row->times[$selection])."\",");
        }
        else
        {
          print("\"".$row->status[$selection]."\",");
        }
      }
      else
      {
        print("\"&nbsp;\",\"&nbsp;\",\"&nbsp;\",");
      }
    }
    if ($row->totalStatus == "OK")
    {
      print("\"".toHms($row->totalTime)."\"");//print("\"".toHms($row->totalTime + $row->totalPenalties)."\"");
    }
    else
    {
      print("\"".$row->totalStatus."\"");
    }
    print ']';

    if($i >= $limit)
      break;
  }
  print "];";
}

function formatShowOResults($showo_out, $limit = 99999)
{
  $head = true;
  print '[';
  $i = 0;
  $previoustime = 0;
  foreach($showo_out as $row)
  {
    $i++;
    if ($head)
    {
        print("[");
        $head = false;
    }
    else
    {
        print(",[");
    }

    if ($row->totalStatus == "OK")
    {
      print("\"".$i."\",");
    }
    else
    {
      print("\"&nbsp;\",");
    }

    print("\"".$row->name."\",\"".$row->club."\",");
    
    
    
    if ((null != $row->totalTime) /*&& (null != $row->totalPenalties)*/)
    {
      print("\"".toHms($row->totalTime - $row->totalPenalties)."\",");
      print("\"".round($row->totalPenalties / CST_PENALTY_S)."\",");
      if ($row->totalStatus == "OK")
      {
        print("\"".toHms($row->totalTime)."\",");
      }
      else
      {
        print("\"".$row->totalStatus."\",");
      }
    }
    else
    {
      print("\"&nbsp;\",\"&nbsp;\",\"&nbsp;\",");
    }

    if (($previoustime != 0) && ($row->totalStatus == "OK"))
    {
      print("\"+".toHms(($row->totalTime) - $previoustime)."\"");
      //print("\"+".toHms(($row->totalTime + $row->totalPenalties) - $previoustime)."\"");
    }
    else
    {
      print("\"&nbsp;\"");
      if($row->totalStatus == "OK")
        $previoustime = $row->totalTime;//$row->totalTime + $row->totalPenalties;
    }
    

    print ']';

    if($i >= $limit)
      break;
  }
  print "];";
}

  $sql = 'SELECT panel1tm_count FROM resultscreen WHERE rcid='.$rcid.' AND sid='.$sid;
  $res = mysql_query($sql);
  $selectioncount = 0;
  if(mysql_num_rows($res) > 0)
  {
    $r = mysql_fetch_array($res);
    $selectioncount = $r['panel1tm_count'];
  }

  $jsonserverip = $SERVER_JSON_IP_ADDRESSES[$course];
  $json = "http://".$jsonserverip."/json/lastresults";
  $contents = utf8_decode(file_get_contents($json));
/*

  $contents='
{
   "_lastTime":1444264321327,
   "lastTime":1443969961453,
   "name":"Simulation 2016",
   "results":[
      {
         "name":"[Auto]",
         "finishCount":0,
         "presentCount":0,
         "rankedRunners":[

         ],
         "unrankedRunners":[

         ]
      },
      {
         "name":"H3",
         "finishCount":65,
         "presentCount":65,
         "rankedRunners":[
            {
               "id":18,
               "firstName":"LUCAS",
               "lastName":"JANSSENS",
               "category":"EH",
               "club":"RAIDS AVENTURE,FR",
               "finishTime":12385000,
               "totalPenalties":276000,
               "readTime":1443962177156,
               "status":"OK",
               "rank":"2",
               "nc":false
            },
            {
               "id":47,
               "firstName":"GOUY",
               "lastName":"GOUY",
               "category":"EH",
               "club":"GOUY\'S BROTHERS",
               "finishTime":12378000,
               "totalPenalties":203000,
               "readTime":1443961986140,
               "status":"OK",
               "rank":"1",
               "nc":false
            },
            {
               "id":45,
               "firstName":"BEGUIN",
               "lastName":"DIMITRIOU",
               "category":"EH",
               "club":"NEUCHAVENTURE",
               "finishTime":13223000,
               "totalPenalties":250000,
               "readTime":1443962364296,
               "status":"OK",
               "rank":"3",
               "nc":false
            },
            {
               "id":56,
               "firstName":"VALLA",
               "lastName":"REVOL",
               "category":"EM",
               "club":"TEAM FMR",
               "finishTime":12465000,
               "totalPenalties":272000,
               "readTime":1443962093578,
               "status":"PM",
               "rank":"4",
               "nc":false
            }
         ],
         "unrankedRunners":[ ]
      },
      {
         "name":"H2",
         "finishCount":25,
         "presentCount":25,
         "rankedRunners":[
            {
               "id":18,
               "firstName":"LUCAS",
               "lastName":"JANSSENS",
               "category":"EH",
               "club":"RAIDS AVENTURE,FR",
               "finishTime":11386000,
               "totalPenalties":396000,
               "readTime":1443962177156,
               "status":"OK",
               "rank":"1",
               "nc":false
            },
            {
               "id":47,
               "firstName":"GOUY",
               "lastName":"GOUY",
               "category":"EH",
               "club":"GOUY\'S BROTHERS",
               "finishTime":12488000,
               "totalPenalties":302000,
               "readTime":1443961986140,
               "status":"OK",
               "rank":"3",
               "nc":false
            },
            {
               "id":56,
               "firstName":"VALLA",
               "lastName":"REVOL",
               "category":"EM",
               "club":"TEAM FMR",
               "finishTime":12455000,
               "totalPenalties":172000,
               "readTime":1443962093578,
               "status":"PM",
               "rank":"4",
               "nc":false
            },
            {
               "id":42,
               "firstName":"GARDE",
               "lastName":"LAURENDON",
               "category":"EH",
               "club":"TEAM ELITE MTBO / CAP OXYGENE",
               "finishTime":12537000,
               "totalPenalties":274000,
               "readTime":1443962244343,
               "status":"OK",
               "rank":"5",
               "nc":false
            },
            {
               "id":7,
               "firstName":"HUBERT",
               "lastName":"ELDIN",
               "category":"EH",
               "club":"ERTIPS",
               "finishTime":12843000,
               "totalPenalties":327000,
               "readTime":1443962333203,
               "status":"OK",
               "rank":"6",
               "nc":false
            }
         ],
         "unrankedRunners":[ ]
      },
      {
         "name":"H1",
         "finishCount":52,
         "presentCount":52,
         "rankedRunners":[
            {
               "id":18,
               "firstName":"LUCAS",
               "lastName":"JANSSENS",
               "category":"EH",
               "club":"RAIDS AVENTURE,FR",
               "finishTime":12386000,
               "totalPenalties":296000,
               "readTime":1443962177156,
               "status":"OK",
               "rank":"1",
               "nc":false
            },
            {
               "id":47,
               "firstName":"GOUY",
               "lastName":"GOUY",
               "category":"EH",
               "club":"GOUY\'S BROTHERS",
               "finishTime":12388000,
               "totalPenalties":303000,
               "readTime":1443961986140,
               "status":"OK",
               "rank":"2",
               "nc":false
            },
            {
               "id":45,
               "firstName":"BEGUIN",
               "lastName":"DIMITRIOU",
               "category":"EH",
               "club":"NEUCHAVENTURE",
               "finishTime":12423000,
               "totalPenalties":270000,
               "readTime":1443962364296,
               "status":"OK",
               "rank":"3",
               "nc":false
            },
            {
               "id":56,
               "firstName":"VALLA",
               "lastName":"REVOL",
               "category":"EM",
               "club":"TEAM FMR",
               "finishTime":12465000,
               "totalPenalties":272000,
               "readTime":1443962093578,
               "status":"OK",
               "rank":"4",
               "nc":false
            },
            {
               "id":42,
               "firstName":"GARDE",
               "lastName":"LAURENDON",
               "category":"EH",
               "club":"TEAM ELITE MTBO / CAP OXYGENE",
               "finishTime":12538000,
               "totalPenalties":273000,
               "readTime":1443962244343,
               "status":"OK",
               "rank":"5",
               "nc":false
            },
            {
               "id":7,
               "firstName":"HUBERT",
               "lastName":"ELDIN",
               "category":"EH",
               "club":"ERTIPS",
               "finishTime":12844000,
               "totalPenalties":227000,
               "readTime":1443962333203,
               "status":"OK",
               "rank":"6",
               "nc":false
            }
         ],
         "unrankedRunners":[

         ]
      }
   ]
}
';
*/




  $totalResult = array();

  list($status, $lasttime, $name, $result) = geco_getheaderinfo($contents);
  if ($status)
  {
    list($status, $races) = geco_getracenames($result);
    if ($status)
    {
      for($selection=0;$selection<$selectioncount;$selection++)
      {
        if($selectioncount == 1)
        {
          list($status, $rankedrunners, $unrankedrunners) = geco_getrunners($result, $course, substr($course,0,1));
          /*print_r($rankedrunners);
          echo '<hr />';
          print_r($unrankedrunners);
          echo '<hr />';
          echo '/'.($status? '1':'0').'*';
          echo '<hr />';*/
        }
        else
        {
          list($status, $rankedrunners, $unrankedrunners) = geco_getrunners($result, $course.(1+$selection));
        }
        if ($status)
        {
            foreach ($rankedrunners as $key => $value)
            {
              $idrunner = $value["firstName"]." ".$value["lastName"];
              if (null == $totalResult[$idrunner])
              {
                $totalResult[$idrunner] = new ShowOResult($value["firstName"]." ".$value["lastName"], $value["club"]);
              }
              $totalResult[$idrunner]->Register($selection, $value["status"], $value["finishTime"], $value["totalPenalties"], $value['rank']);
            }
            foreach ($unrankedrunners as $key => $value)
            {
              $idrunner = $value["firstName"]." ".$value["lastName"];
              if (null == $totalResult[$idrunner])
              {
                $totalResult[$idrunner] = new ShowOResult($value["firstName"]." ".$value["lastName"], $value["club"]);
              }
              $totalResult[$idrunner]->Register($selection, $value["status"]);
            }
        }
      }
    }
  }
  //print_r($totalResult);
  // calcul savant
  $showo_out = reorder_ShowO($totalResult, $selectioncount);
/*  echo '<hr />';
  print_r($showo_out);
  echo '<hr />';
*/

  // conversion pour javascript
  if ($qualif != "0")
  {
    formatShowOResultsQualif($showo_out, $selectioncount, $limit);
  }
  else
  {
    formatShowOResults($showo_out, $limit);
  }
?>
