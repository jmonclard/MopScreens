# MopScreens
Large event results display for MeOS

* 2015-11-04 Source code cleaning and small fixes and improvements

	What's New ?
		Icons for configuration rename and edit have been change to remove any possible confusion
		It is now possible to define the fisrt line of a screen or panel (for instance to keep the content of a panel fixed while on another panel the remaining of the class is scrolling)
		When scrolling, stops now after 3 empty lines
		It is now possible to select classes in full screen mode right from the summary table of screen.php clicking on the podium icon
	
	Known bugs
		When screens' panels are empty, they are not correctly refreshed. Workaround : edit the screen in screenconfig.php and press ok.
		In relay mode it is mandatory to have at least one radio control
		If pressing classes edit button while editing a screen configuration, all changes altready made in the form fields are lost. Workaround : use podium icon in screen.php
		
	Missing features
		Relay must be extended to 10 team members
		Screens should be dividable in three panels
		Rows height should be decreased in order to stack more lines on the screen
		It should be possible from screenconfig.php to upload pictures and html files to their respective directories
		Add language support
		Add documentation access from the interface

* 2015-09-13 Version used by CSP Orienteering club
	Includes a temporary fix of the lost of class allocation on MeOS service restart.

    Master branch holds the original MeOS version
    Ligue PACA branch holds the new created one to be used.

MopScreens is a set of php files to be used on a web server (mainly a local one) to display large event results in real time in an O'Ringen like style. It uses MeOS Online Protocol for data updating, and reuse part of the Mop example provided with MeOS.

Currently it can handle 12 screens, but can be extended to more screens very easily. Main limitation concerns relay : currently the team is limited to 3 members and to 3 radio controls per leg. The limitation in team size will be increased to 10 members within the next 6 months.

To get a better idea of what it looks like, have a look to the english documentation MeOSScreensEn.pdf

It is provided "as is" as open source code and can be freely used, modified, enhenced and distributed. It is licensed in the same condition as MeOS.
