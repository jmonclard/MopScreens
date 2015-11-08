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
    
    function GetLanguages()
    {
      $lng[]=array('fr','Français');
      $lng[]=array('en','English');
      $lng[]=array('sv','Svenska');
      
      return $lng;
    }
    
    function MyGetText($sentence)
    {
      $text[0]['fr']="Configurations";
      $text[0]['en']="Configurations";
      $text[0]['sv']="";

      $text[1]['fr']="Editer";
      $text[1]['en']="Edit";
      $text[1]['sv']="";

      $text[2]['fr']="Activer";
      $text[2]['en']="Activate";
      $text[2]['sv']="";
      
      $text[3]['fr']="Configuration active";
      $text[3]['en']="Active configuration";
      $text[3]['sv']="";
      
      $text[4]['fr']="Renommer";
      $text[4]['en']="Rename";
      $text[4]['sv']="";
      
      $text[5]['fr']="Cloner";
      $text[5]['en']="Clone";
      $text[5]['sv']="";
      
      $text[6]['fr']="Supprimer";
      $text[6]['en']="Delete";
      $text[6]['sv']="";
      
      $text[7]['fr']="Créer";
      $text[7]['en']="New";
      $text[7]['sv']="";
      
      $text[8]['fr']="N°";
      $text[8]['en']="Id";
      $text[8]['sv']="";
      
      $text[9]['fr']="Compétitions";
      $text[9]['en']="Competitions";
      $text[9]['sv']="";
      
      $text[10]['fr']="Date";
      $text[10]['en']="Date";
      $text[10]['sv']="";
      
      $text[11]['fr']="Organisateurs";
      $text[11]['en']="Organizers";
      $text[11]['sv']="";
      
      $text[12]['fr']="Gestion des fichiers images et html";
      $text[12]['en']="Images and html files management";
      $text[12]['sv']="";
      
      $text[13]['fr']="Nom de la nouvelle configuration : ";
      $text[13]['en']="New configuration name : ";
      $text[13]['sv']="";
      
      $text[14]['fr']="Souhaitez-vous vraiment supprimer ";
      $text[14]['en']="Do you really want to delete ";
      $text[14]['sv']="";
      
      $text[15]['fr']="Type du fichier : ";
      $text[15]['en']="File type : ";
      $text[15]['sv']="";
      
      $text[16]['fr']="Mauvais type de fichier. Chargement annulé.";
      $text[16]['en']="Bad file type. Upload cancelled";
      $text[16]['sv']="";
      
      $text[17]['fr']="Fichiers images";
      $text[17]['en']="Images files";
      $text[17]['sv']="";
      
      $text[18]['fr']="Voir";
      $text[18]['en']="View";
      $text[18]['sv']="";
      
      $text[19]['fr']="Retour à la page principale";
      $text[19]['en']="Back to main page";
      $text[19]['sv']="";

      $text[20]['fr']="Fichiers HTML";
      $text[20]['en']="HTML files";
      $text[20]['sv']="";
      
      $text[21]['fr']="Chargement d'une nouvelle image";
      $text[21]['en']="Image file upload";
      $text[21]['sv']="";
      
      $text[22]['fr']="Chargement d'un nouveau fichier HTML";
      $text[22]['en']="HTML file upload";
      $text[22]['sv']="";
      
      $text[23]['fr']="Rafraîchir";
      $text[23]['en']="Refresh";
      $text[23]['sv']="";
      
      $text[24]['fr']="Ecran";
      $text[24]['en']="Screen";
      $text[24]['sv']="";
      
      $text[25]['fr']="Titre";
      $text[25]['en']="Title";
      $text[25]['sv']="";
      
      $text[26]['fr']="Sous-titre";
      $text[26]['en']="Subtitle";
      $text[26]['sv']="";
      
      $text[27]['fr']="Logos";
      $text[27]['en']="Pictures";
      $text[27]['sv']="";
      
      $text[28]['fr']="Gauche";
      $text[28]['en']="Left";
      $text[28]['sv']="";
      
      $text[29]['fr']="Droit";
      $text[29]['en']="Right";
      $text[29]['sv']="";
      
      $text[30]['fr']="Mode";
      $text[30]['en']="Mode";
      $text[30]['sv']="";
      
      $text[31]['fr']="Plein écran";
      $text[31]['en']="Full screen";
      $text[31]['sv']="";
      
      $text[32]['fr']="Type";
      $text[32]['en']="Type";
      $text[32]['sv']="";
      
      $text[33]['fr']="Contenu";
      $text[33]['en']="Content";
      $text[33]['sv']="";
      
      $text[34]['fr']="Partie gauche";
      $text[34]['en']="Left pane";
      $text[34]['sv']="";
      
      $text[35]['fr']="Partie droite";
      $text[35]['en']="Right pane";
      $text[35]['sv']="";
      
      $text[36]['fr']="Langue";
      $text[36]['en']="Language";
      $text[36]['sv']="";
      
      $text[37]['fr']="Deux parties";
      $text[37]['en']="Two panels";
      $text[37]['sv']="";
      
      $text[38]['fr']="Image";
      $text[38]['en']="Picture";
      $text[38]['sv']="";
      
      $text[39]['fr']="Texte";
      $text[39]['en']="Text";
      $text[39]['sv']="";
      
      $text[40]['fr']="HTML";
      $text[40]['en']="HTML";
      $text[40]['sv']="";
      
      $text[41]['fr']="Résultats relais";
      $text[41]['en']="Relay results";
      $text[41]['sv']="";
      
      $text[42]['fr']="Horaires";
      $text[42]['en']="Start list";
      $text[42]['sv']="";
      
      $text[43]['fr']="Résultats";
      $text[43]['en']="Results";
      $text[43]['sv']="";
      
      $text[44]['fr']="Catégories";
      $text[44]['en']="Classes";
      $text[44]['sv']="";
      
      $text[45]['fr']="Départs";
      $text[45]['en']="Starts";
      $text[45]['sv']="";
      
      $text[46]['fr']="Inscrits";
      $text[46]['en']="Entries";
      $text[46]['sv']="";
      
      $text[47]['fr']="Terminé";
      $text[47]['en']="Done";
      $text[47]['sv']="";
      
      $text[48]['fr']="G"; // Gauche
      $text[48]['en']="L"; // Left
      $text[48]['sv']="";
      
      $text[49]['fr']="D"; // Droit
      $text[49]['en']="R"; // Right
      $text[49]['sv']="";
      
      $text[50]['fr']="Affichés";
      $text[50]['en']="Displayed";
      $text[50]['sv']="";
      
      $text[51]['fr']="Disponibles";
      $text[51]['en']="Available";
      $text[51]['sv']="";
      
      $text[52]['fr']="OK";
      $text[52]['en']="OK";
      $text[52]['sv']="";
      
      $text[53]['fr']="Annuler";
      $text[53]['en']="Cancel";
      $text[53]['sv']="";
      
      $text[54]['fr']="Nouveau nom";
      $text[54]['en']="New name";
      $text[54]['sv']="";
      
      $text[55]['fr']="Compétition";
      $text[55]['en']="Competition";
      $text[55]['sv']="";
      
      $text[56]['fr']="Logo de gauche";
      $text[56]['en']="Left picture";
      $text[56]['sv']="";
      
      $text[57]['fr']="Logo de droite";
      $text[57]['en']="Right picture";
      $text[57]['sv']="";
      
      $text[58]['fr']="Première ligne";
      $text[58]['en']="First line";
      $text[58]['sv']="";
      
      $text[59]['fr']="Lignes fixes";
      $text[59]['en']="Fixed lines";
      $text[59]['sv']="";
      
      $text[60]['fr']="Lignes défilantes";
      $text[60]['en']="Scrolling lines";
      $text[60]['sv']="";
      
      $text[61]['fr']="Vitesse de défilement";
      $text[61]['en']="Scroll speed";
      $text[61]['sv']="";
      
      $text[62]['fr']="Attente avant défilement";
      $text[62]['en']="Before scroll wait time";
      $text[62]['sv']="";
      
      $text[63]['fr']="Attente après défilement";
      $text[63]['en']="After scroll wait time";
      $text[63]['sv']="";
      
      $text[64]['fr']="Surbrillance récents";
      $text[64]['en']="Recent highlight";
      $text[64]['sv']="";
      
      $text[65]['fr']="Si les cases à cocher sont cochées, le réglage correspondant est copié pour tous les écrans de la configuration courante";
      $text[65]['en']="Checkbox checked ==> copy corresponding settings to all screens of this configuration";
      $text[65]['sv']="";
      
      $text[66]['fr']="Couleur";
      $text[66]['en']="Color";
      $text[66]['sv']="";
      
      $text[67]['fr']="";
      $text[67]['en']="";
      $text[67]['sv']="";
      
      $text[68]['fr']="";
      $text[68]['en']="";
      $text[68]['sv']="";
      
      $text[69]['fr']="";
      $text[69]['en']="";
      $text[69]['sv']="";
      
      $text[70]['fr']="";
      $text[70]['en']="";
      $text[70]['sv']="";
      
      
      
      
      return $text[$sentence][$_SESSION['CurrentLanguage']];


    }
    
?>


