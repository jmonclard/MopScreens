<?php

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr" dir="ltr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Ma page de test</title>
  </head>
    <body>
<?php
function json_decode_nice($json, $assoc = FALSE)
{
    $json = str_replace(array("\n","\r"),"",$json);
    $json = preg_replace('/([{,]+)(\s*)([^"]+?)\s*:/','$1"$3":',$json);
    return json_decode($json,$assoc);
}

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
            //print("<br>Test avec ".$results[$i]["name"]."<br>");
            //print("<pre>".print_r($results[$i]["name"],true)."</pre>");
            $status = $results[$i]["name"] == $racename;
            if ($status)
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
        }
    }

    return array($status, $rankedrunners, $unrankedrunners);
}
/*
function geco_namegetraces($contents)
{
    $status = false;
    $contents = utf8_encode($contents);
    $decoded = json_decode($contents, true);

//var_dump($decoded);
    $nb = count($decoded);
    if ($nb == 4)
    {
        // normalement _lasttime, lasttime, name, results

    }

print("<pre>".print_r($decoded,true)."</pre>");



#print(json_encode($decoded));
}
*/

print("<b>hello world</b>...");
//$contents = file_get_contents($url);
//$contents = utf8_encode($contents);
//$results = json_decode($contents);

//$contents='{"a":1,"b":2,"c":3,"d":4,"e":5}';

$contents='
{
   "_lastTime":1444264321327,
   "lastTime":1443969961453,
   "name":"ROA 2015",
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
         "name":"CHALLENGER",
         "finishCount":65,
         "presentCount":65,
         "rankedRunners":[ ],
         "unrankedRunners":[ ]
      },
      {
         "name":"DECOUVERTE",
         "finishCount":25,
         "presentCount":25,
         "rankedRunners":[ ],
         "unrankedRunners":[ ]
      },
      {
         "name":"ELITE",
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
            },
            {
               "id":24,
               "firstName":"RELAVE",
               "lastName":"FORESTIER",
               "category":"EH",
               "club":"LIGERAID",
               "finishTime":13038000,
               "totalPenalties":247000,
               "readTime":1443962643484,
               "status":"OK",
               "rank":"7",
               "nc":false
            },
            {
               "id":5,
               "firstName":"FRECHINET",
               "lastName":"PEYVEL",
               "category":"EM",
               "club":"TEAM DE RAID",
               "finishTime":13223000,
               "totalPenalties":305000,
               "readTime":1443962900406,
               "status":"OK",
               "rank":"8",
               "nc":false
            },
            {
               "id":30,
               "firstName":"DOMMANGET",
               "lastName":"MASSON",
               "category":"EH",
               "club":"BIM BIM TEAM",
               "finishTime":13581000,
               "totalPenalties":226000,
               "readTime":1443963317625,
               "status":"OK",
               "rank":"9",
               "nc":false
            },
            {
               "id":52,
               "firstName":"MAIRE",
               "lastName":"PARRA",
               "category":"EH",
               "club":"SPEED\'RAIDEURS",
               "finishTime":13611000,
               "totalPenalties":306000,
               "readTime":1443967149171,
               "status":"OK",
               "rank":"10",
               "nc":false
            },
            {
               "id":38,
               "firstName":"VIALLON",
               "lastName":"MOUGEL",
               "category":"EH",
               "club":"LES RAIDEURS DU MATIN",
               "finishTime":13658000,
               "totalPenalties":43404000,
               "readTime":1443963997515,
               "status":"OK",
               "rank":"11",
               "nc":false
            },
            {
               "id":29,
               "firstName":"GERAL",
               "lastName":"GELSOMINO",
               "category":"EH",
               "club":"AGDE RAID AVENTURE",
               "finishTime":13730000,
               "totalPenalties":272000,
               "readTime":1443963349125,
               "status":"OK",
               "rank":"12",
               "nc":false
            },
            {
               "id":26,
               "firstName":"VIALE",
               "lastName":"VOLPE",
               "category":"EH",
               "club":"PACARAID LE PHOCEEN",
               "finishTime":13842000,
               "totalPenalties":259000,
               "readTime":1443963835046,
               "status":"OK",
               "rank":"13",
               "nc":false
            },
            {
               "id":35,
               "firstName":"LECONTE",
               "lastName":"BISSONNET",
               "category":"EH",
               "club":"XTTR63 PUCEAUX SAUVAGES",
               "finishTime":13971000,
               "totalPenalties":357000,
               "readTime":1443963748671,
               "status":"OK",
               "rank":"14",
               "nc":false
            },
            {
               "id":49,
               "firstName":"MOLLARET",
               "lastName":"LE GLAND",
               "category":"EH",
               "club":"ASO",
               "finishTime":14110000,
               "totalPenalties":265000,
               "readTime":1443964429218,
               "status":"OK",
               "rank":"15",
               "nc":false
            },
            {
               "id":53,
               "firstName":"CHAMPIGNY",
               "lastName":"CHAMPIGNY",
               "category":"EH",
               "club":"LES FRER\'O\"\"",
               "finishTime":14120000,
               "totalPenalties":243000,
               "readTime":1443963778921,
               "status":"OK",
               "rank":"16",
               "nc":false
            },
            {
               "id":3,
               "firstName":"ALAMONE",
               "lastName":"RIBET",
               "category":"EM",
               "club":"READY TO GO",
               "finishTime":14145000,
               "totalPenalties":256000,
               "readTime":1443963877140,
               "status":"OK",
               "rank":"17",
               "nc":false
            },
            {
               "id":55,
               "firstName":"BAVEREL",
               "lastName":"PERROT",
               "category":"EH",
               "club":"400TEAM RAIDLIGHT NATUREX",
               "finishTime":14221000,
               "totalPenalties":264000,
               "readTime":1443964185265,
               "status":"OK",
               "rank":"18",
               "nc":false
            },
            {
               "id":2,
               "firstName":"POMEON",
               "lastName":"MERCIER",
               "category":"EH",
               "club":"LIGERAID 007",
               "finishTime":14333000,
               "totalPenalties":239000,
               "readTime":1443965398187,
               "status":"OK",
               "rank":"19",
               "nc":false
            },
            {
               "id":14,
               "firstName":"COLLIOUD",
               "lastName":"DESMARIS",
               "category":"EH",
               "club":"MC KINLEY ADVENTURE RACING",
               "finishTime":14446000,
               "totalPenalties":236000,
               "readTime":1443964327000,
               "status":"OK",
               "rank":"20",
               "nc":false
            },
            {
               "id":44,
               "firstName":"HAUMMESSER",
               "lastName":"MARIE",
               "category":"EH",
               "club":"A RAID LES PAGU",
               "finishTime":14629000,
               "totalPenalties":205000,
               "readTime":1443965279765,
               "status":"OK",
               "rank":"21",
               "nc":false
            },
            {
               "id":16,
               "firstName":"PREVOST",
               "lastName":"POUDES",
               "category":"EH",
               "club":"A FOND GASTON",
               "finishTime":14906000,
               "totalPenalties":325000,
               "readTime":1443964455500,
               "status":"OK",
               "rank":"22",
               "nc":false
            },
            {
               "id":50,
               "firstName":"GALLI",
               "lastName":"RIVOIRE",
               "category":"EH",
               "club":"LES JEUX DE MOTS LAIDS 1",
               "finishTime":15097000,
               "totalPenalties":266000,
               "readTime":1443965090015,
               "status":"OK",
               "rank":"23",
               "nc":false
            },
            {
               "id":31,
               "firstName":"PAVEE",
               "lastName":"SOYEZ",
               "category":"EM",
               "club":"RAIDSAVENTURE.FR CHAMROUSSE",
               "finishTime":15183000,
               "totalPenalties":345000,
               "readTime":1443964842375,
               "status":"OK",
               "rank":"24",
               "nc":false
            },
            {
               "id":4,
               "firstName":"CHATELON",
               "lastName":"CHATELON",
               "category":"EM",
               "club":"OUTDOOR",
               "finishTime":15193000,
               "totalPenalties":243000,
               "readTime":1443964894125,
               "status":"OK",
               "rank":"25",
               "nc":false
            },
            {
               "id":17,
               "firstName":"TSOMBANOPOULOS",
               "lastName":"SARTI",
               "category":"EH",
               "club":"TEAM BARAKAFRITES SPORT NATURE",
               "finishTime":15339000,
               "totalPenalties":326000,
               "readTime":1443965033843,
               "status":"OK",
               "rank":"26",
               "nc":false
            },
            {
               "id":23,
               "firstName":"ADAMSKI",
               "lastName":"CHATAING",
               "category":"EH",
               "club":"LOS MUCHACHOS",
               "finishTime":15365000,
               "totalPenalties":426000,
               "readTime":1443965201953,
               "status":"OK",
               "rank":"27",
               "nc":false
            },
            {
               "id":34,
               "firstName":"CORNELOUP",
               "lastName":"LALLIER",
               "category":"EM",
               "club":"RAID O\'BROTHERS 1",
               "finishTime":15402000,
               "totalPenalties":249000,
               "readTime":1443965269343,
               "status":"OK",
               "rank":"28",
               "nc":false
            },
            {
               "id":33,
               "firstName":"PORRET",
               "lastName":"HUOT",
               "category":"EM",
               "club":"AUTOKA",
               "finishTime":15431000,
               "totalPenalties":361000,
               "readTime":1443965416734,
               "status":"OK",
               "rank":"29",
               "nc":false
            },
            {
               "id":27,
               "firstName":"RENON",
               "lastName":"REBOUD",
               "category":"EH",
               "club":"LES GNOCCHIS",
               "finishTime":15888000,
               "totalPenalties":243000,
               "readTime":1443965708750,
               "status":"OK",
               "rank":"30",
               "nc":false
            },
            {
               "id":43,
               "firstName":"BOYE",
               "lastName":"JOUBERT",
               "category":"EM",
               "club":"GONES RAIDEURS GRENOBLOIS",
               "finishTime":15988000,
               "totalPenalties":244000,
               "readTime":1443965839500,
               "status":"OK",
               "rank":"31",
               "nc":false
            },
            {
               "id":11,
               "firstName":"BLANES",
               "lastName":"PARES",
               "category":"EM",
               "club":"TEAM AZERTIT BARAGNAS",
               "finishTime":16007000,
               "totalPenalties":229000,
               "readTime":1443966991343,
               "status":"OK",
               "rank":"32",
               "nc":false
            },
            {
               "id":8,
               "firstName":"FROMENTON",
               "lastName":"FAURE",
               "category":"EH",
               "club":"MADE IN RAID 4",
               "finishTime":16013000,
               "totalPenalties":212000,
               "readTime":1443965853515,
               "status":"OK",
               "rank":"33",
               "nc":false
            },
            {
               "id":12,
               "firstName":"PREVOST",
               "lastName":"BAINIER",
               "category":"EM",
               "club":"A RAID DE COURIR",
               "finishTime":16673000,
               "totalPenalties":451000,
               "readTime":1443966450640,
               "status":"OK",
               "rank":"34",
               "nc":false
            },
            {
               "id":20,
               "firstName":"TELLIER",
               "lastName":"ESCUDE",
               "category":"EH",
               "club":"BARAGNAS",
               "finishTime":17100000,
               "totalPenalties":272000,
               "readTime":1443966972640,
               "status":"OK",
               "rank":"35",
               "nc":false
            },
            {
               "id":19,
               "firstName":"PECH",
               "lastName":"CLEMONT",
               "category":"EM",
               "club":"BARAGNAS RAID TEAM",
               "finishTime":17123000,
               "totalPenalties":330000,
               "readTime":1443967007828,
               "status":"OK",
               "rank":"36",
               "nc":false
            },
            {
               "id":36,
               "firstName":"GROSSE",
               "lastName":"OUVRARD",
               "category":"EF",
               "club":"ARVERNE OUTDOOR ERTIPS",
               "finishTime":17770000,
               "totalPenalties":233000,
               "readTime":1443967687078,
               "status":"OK",
               "rank":"37",
               "nc":false
            },
            {
               "id":40,
               "firstName":"SZARZENSKI",
               "lastName":"PIGNEDE",
               "category":"EH",
               "club":"LES DAHUTS",
               "finishTime":17844000,
               "totalPenalties":241000,
               "readTime":1443967994218,
               "status":"OK",
               "rank":"38",
               "nc":false
            },
            {
               "id":15,
               "firstName":"BILLET",
               "lastName":"MICHAUD",
               "category":"EF",
               "club":"LIGERAID-EUSES",
               "finishTime":17909000,
               "totalPenalties":239000,
               "readTime":1443968033046,
               "status":"OK",
               "rank":"39",
               "nc":false
            },
            {
               "id":48,
               "firstName":"MACHEBOEUF",
               "lastName":"HEURTAULT",
               "category":"EM",
               "club":"XTTR63 LE LIEVRE ET LA TORTUE",
               "finishTime":18084000,
               "totalPenalties":274000,
               "readTime":1443967948984,
               "status":"OK",
               "rank":"40",
               "nc":false
            },
            {
               "id":28,
               "firstName":"OCTOBRE",
               "lastName":"DEJOURS",
               "category":"EF",
               "club":"RAIDLINK\'S 100% FILLE",
               "finishTime":18272000,
               "totalPenalties":282000,
               "readTime":1443968161750,
               "status":"OK",
               "rank":"41",
               "nc":false
            },
            {
               "id":39,
               "firstName":"MICON",
               "lastName":"MAZAN",
               "category":"EH",
               "club":"HAUT LANGEDOC AVENTURE",
               "finishTime":18309000,
               "totalPenalties":3898000,
               "readTime":1443964231718,
               "status":"OK",
               "rank":"42",
               "nc":false
            },
            {
               "id":6,
               "firstName":"POTIER",
               "lastName":"BOUTEILLE",
               "category":"EH",
               "club":"LA CABANE SUR LE CHIEN",
               "finishTime":18450000,
               "totalPenalties":290000,
               "readTime":1443968049140,
               "status":"OK",
               "rank":"43",
               "nc":false
            },
            {
               "id":1,
               "firstName":"DAL BELLO",
               "lastName":"BERNARD",
               "category":"EH",
               "club":"BOL D\'AIR - LES BERDAL",
               "finishTime":18862000,
               "totalPenalties":3844000,
               "readTime":1443965059531,
               "status":"OK",
               "rank":"44",
               "nc":false
            },
            {
               "id":54,
               "firstName":"PRUNET",
               "lastName":"FUSERO",
               "category":"EH",
               "club":"RAID O\'BROTHERS 2",
               "finishTime":18957000,
               "totalPenalties":312000,
               "readTime":1443968761921,
               "status":"OK",
               "rank":"45",
               "nc":false
            },
            {
               "id":41,
               "firstName":"MARION",
               "lastName":"ACKERRER",
               "category":"EM",
               "club":"RAID NATURE 46",
               "finishTime":19546000,
               "totalPenalties":363000,
               "readTime":1443969084328,
               "status":"OK",
               "rank":"46",
               "nc":false
            },
            {
               "id":32,
               "firstName":"CAPELLE",
               "lastName":"BOYER",
               "category":"EM",
               "club":"MARE MONTI",
               "finishTime":19939000,
               "totalPenalties":247000,
               "readTime":1443969869546,
               "status":"OK",
               "rank":"47",
               "nc":false
            },
            {
               "id":22,
               "firstName":"COLLE",
               "lastName":"BARDINE",
               "category":"EF",
               "club":"RAIDLINK\'S PPGC 100% FILLES",
               "finishTime":20422000,
               "totalPenalties":347000,
               "readTime":1443969961453,
               "status":"OK",
               "rank":"48",
               "nc":false
            },
            {
               "id":37,
               "firstName":"GARNIER",
               "lastName":"GARNIER",
               "category":"EH",
               "club":"AZIMUT PROVENCE",
               "finishTime":20905000,
               "totalPenalties":3870000,
               "readTime":1443967338046,
               "status":"OK",
               "rank":"49",
               "nc":false
            },
            {
               "id":25,
               "firstName":"CHANET",
               "lastName":"CHANET",
               "category":"EH",
               "club":"ECRINS",
               "finishTime":22300000,
               "totalPenalties":270000,
               "readTime":1443972166890,
               "status":"OK",
               "rank":"50",
               "nc":false
            },
            {
               "id":13,
               "firstName":"BACCOU",
               "lastName":"DELORME",
               "category":"EH",
               "club":"ENVOI DU GROS",
               "finishTime":27467000,
               "totalPenalties":280000,
               "readTime":1443976973343,
               "status":"OK",
               "rank":"51",
               "nc":false
            },
            {
               "id":51,
               "firstName":"MAILLOT",
               "lastName":"JACQUEMET",
               "category":"EM",
               "club":"LES JEUX DE MOTS LAIDS 2",
               "finishTime":28671000,
               "totalPenalties":11041000,
               "readTime":1443969740000,
               "status":"OK",
               "rank":"52",
               "nc":false
            }
         ],
         "unrankedRunners":[

         ]
      }
   ]
}
';

// CODE pour faire un vrai acces au serveur json
//$json = "http://192.168.0.57:4567/json/lastresults";
//$contents = file_get_contents($json);
//var_dump($contents);// le var_dump juste pour vérifier mais pas nécessaire


list($status, $lasttime, $name, $result) = geco_getheaderinfo($contents);
if ($status)
{
    //print("<pre>".print_r($res,true)."</pre>");
    print("<br>Name:".$name."<br>");
    list($status, $races) = geco_getracenames($result);
    if ($status)
    {
        print("Race count:".count($races)."<br>");
        print("<ul>");
        for($i=0;$i<count($races);$i++)
        {
            print("<li>".$races[$i]."</li>");
        }
        print("</ul>");


        list($status, $rankedrunners, $unrankedrunners) = geco_getrunners($result, "ELITE");
        if ($status)
        {
            print("RANKED RUNNERS<br>");
            print("<ul>");
            foreach ($rankedrunners as $key => $value)
            {
                //print("<pre>".print_r($value,true)."</pre>");
                print("<li>".$value["firstName"]." ".$value["lastName"]."<br>");
            }
            print("</ul>");
        }

        list($status, $rankedrunners, $unrankedrunners) = geco_getrunners($result, "ELITE", "EH");
        if ($status)
        {
            print("RANKED RUNNERS category EH<br>");
            print("<ul>");
            foreach ($rankedrunners as $key => $value)
            {
                print("<pre>".print_r($value,true)."</pre>");
                //print("<li>".$value["firstName"]." ".$value["lastName"]."<br>");
            }
            print("</ul>");
        }
    }
}
else
{
    print("<br>mauvais decodage<br>");
}

?>
</body>
</html>
