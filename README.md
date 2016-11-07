# MopScreens
Large event results display for MeOS

###2016-11-07 Version planned to be used for PACA night orienteering on November the 19th
####What's New ?
[x] Better support of 1, 2, 3 and 4 panels modes

[x] Up to 15 radio control supported (in 1 panel mode individual race)

[x] Better suport for homemade radio system (based on LoRa technology) with real time radio quality and battery level display superimposed on map. Ask for details.

####Known bugs
[ ] Multistages competition are not correctly handled (no cumulative time)

[ ] Documentation is no more up to date, but still help.

####Missing features
[ ] Add multilines scrolling. Well not sure it is really useful...

[ ] Allow course based instead of class based display (may require some changes in MeOS)

###2016-05-04 Version used for French Clubs Championship
####What's New ?
[x] Rows height have been decreased in order to stack more lines on the screen

[x] Support for slideshow

[x] Support for micro blogging (not connected to standard blogging software)

[x] Support for a summary of the competition allowing winners of all classes to be displayed on the same screen (updated in real time)

[x] Suport for homemade radio system (based on LoRa technology) with real time radio quality and battery level display superimposed on map. Ask for details.

[x] Allows CSS selection on a screen by screen basis to match caracteristics of screens (ex adapt rows height)

[x] Partial support of GECO software.

####Known bugs
[ ] Multistages competition are not correctly handled (no cumulative time)

[ ] Documentation is no more up to date, but still help.

####Missing features
See below.

###2016-04-04 Relay extended to 10
####What's New ?
NOTE : Database tables structure have been altered. If you are updating an existing version, please ask for support.

[X] Relay extended up to 10 team members

[X] Screens can now be divided in two, three or four panels for non relay competitions

[X] MP, DNF etc... translation

[x] Support for experimental radio transmitted punches improved. Ask for details if interested.

####Known bugs
[ ] Documentation is no more up to date, but still help.

####Missing features
[ ] Add multilines scrolling

[ ] Allow course based instead of class based display (may require some changes in MeOS)

###2016-03-25 Fixes
####What's New ?
[x] Empty panels refresh issue fixed

[x] Radio control is no more mandatory for relays

[x] Rows height has be decreased in order to stack more lines on the screen

[x] Team members names added above radio times on relays

[x] No wrap on team name

[x] Added support for experimental radio transmitted punches. Ask for details if interested.

[x] Documentation can be accessed right from the interface

####Known bugs
[ ] If pressing classes edit button while editing a screen configuration, all changes already made in the form fields are lost. Workaround : use podium icon in screen.php

[ ] Relay display order may be incorrect if a runner lost time after radio control until he completes the course (he can appears ahead of already arrived competitors in the list).

####Missing features
[ ] Relay must be extended up to 10 team members (work in progress)

[ ] Screens should be dividable in three and four panels (work in progress)

[ ] Add multilines scrolling

[ ] Allow course based instead of class based display (may require some changes in MeOS)

[ ] Add MP, DNF etc... translation (work in progress)


###2016-01-09 Warning fix and simultaneous support of both original MeOS display and new "MopScreens" display
####What's New ?
[x] Fixes a warning issue in formatResult() function in functions.php

[x] It's now possible to simultaneousely use show.php (original MeOS result online display) and pages.php ("Screen" O'Ringen like one).

####Known bugs
No bug fixed. Please see below for known bugs.

####Missing features
No missing features added. See below for a list of expected features.

###2015-11-08 Picture files management and internationalisation

####What's New ?
[x] It's now possible from screenconfig.php to upload pictures and html files to their respective directories and to manage them.

[x] Fixed an issue relative to picture and html file allowing configuration even if these files do not exist on the PC used for configuration.

[x] Added French and Swedish support. Language can be chosen from screenconfig.php. Default language is determined based on browser configuration. Thanks to Jens Kastensson for the Swedish translation.

####Known bugs
		
[ ] When screens' panels are empty, they are not correctly refreshed. Workaround : edit the screen in screenconfig.php and press ok.

[ ] In relay mode it is mandatory to have at least one radio control

[ ] If pressing classes edit button while editing a screen configuration, all changes altready made in the form fields are lost. Workaround : use podium icon in screen.php
		
####Missing features

[ ] Relay must be extended to 10 team members

[ ] Screens should be dividable in three panels

[ ] Rows height should be decreased in order to stack more lines on the screen

[ ] Add documentation access from the interface

###2015-11-04 Source code cleaning and small fixes and improvements

####What's New ?

[x] Icons for configuration rename and edit have been changed to remove any possible confusion

[x] It is now possible to define the fisrt line of a screen or panel (for instance to keep the content of a panel fixed while on another panel the remaining of the class is scrolling)

[x] When scrolling, stops now after 3 empty lines

[x] It is now possible to select classes in full screen mode right from the summary table of screen.php clicking on the podium icon


###2015-09-13 Version used by CSP Orienteering club

####What's New ?

[x] Includes a temporary fix of the lost of class allocation on MeOS service restart.

##Description
MopScreens is a set of php files to be used on a web server (mainly a local one) to display large event results in real time in an O'Ringen like style.
It uses MeOS Online Protocol for data updating, and reuse part of the Mop example provided with MeOS.

Currently it can handle 12 screens, but can be extended to more screens very easily. Main limitation concerns relay : currently the team is limited to 3 members and to 3 radio controls per leg.
The limitation in team size will be increased to 10 members before June 2016.

To get a better idea of what it looks like, have a look to the english documentation MeOSScreensEn.pdf

It is provided "as is" as open source code and can be freely used, modified, enhenced and distributed. It is licensed in the same conditions as MeOS.

