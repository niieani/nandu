<?php
/*
 * File:        c-css.php
 * CVS:         $Id$
 * Description: Conditional CSS parser
 * Author:      Allan Jardine
 * Created:     Sun May 20 14:05:46 GMT 2007
 * Modified:    $Date$ by $Author$
 * Language:    PHP
 * License:     CDDL v1.0
 * Project:     COnditional-CSS
 * 
 * Copyright 2007-2008 Allan Jardine, all rights reserved.
 *
 * This source file is free software, under the U4EA Common Development and
 * Distribution License (U4EA CDDL) v1.0 only, as supplied with this software.
 * This license is also available online:
 *   http://www.sprymedia.co.uk/license/u4ea_cddl
 * 
 * This source file is distributed in the hope that it will be useful, but 
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY 
 * or FITNESS FOR A PARTICULAR PURPOSE. See the CDDL for more details.
 * 
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * DESCRIPTION
 * 
 * c-css is a program which allows IE style conditional comments to be
 * inserted inline with CSS statements, and then be parsed out as required
 * for individual web browsers. This allows easy targeting of styles to 
 * different browsers, and different versions of browsers as required by the
 * developer, such that browser CSS bugs can be easily over come.
 * 
 * The bowsers which are currently supported are:
 *   Internet Explorer (v2 up) - IE
 *   Internet Explorer Mac - IEMac
 *   Gecko (Firefox etc) - Gecko
 *   Webkit (Safari etc) - Webkit
 *   Opera - Opera
 *   Konqueror - Konq
 *   Safari Mobile (iPhone, iPod) - SafMob
 *   IE Mobile - IEmob
 *   PSP Web browser - PSP
 *   NetFront - NetF
 * 
 * The syntax used for the conditional comments is:
 *   [if {!} {browser}]
 *   [if {!} {browser_group}]
 *   [if {!} {browser} {version}]
 *   [if {!} {condition} {browser} {version}]
 * 
 * Examples:
 *   [if ! Gecko]#column_right {
 *     [if cssA]float:left;
 *     width:250px;
 *     [if Webkit] opacity: 0.8;
 *     [if IE 6] ie6: 100%;
 *     [if lt IE 6] lt-ie6: 100%;
 *     [if lte IE 6] lte-ie6: 100%;
 *     [if eq IE 6] eq-ie6: 100%;
 *     [if gte IE 6] gte-ie6: 100%;
 *     [if gt IE 6] gt-ie6: 100%;
 *     [if ! lte IE 6] not-lte-ie6: 100%;
 *   }
 * 
 * As can be seen from above a conditional comment can be applied to either 
 * a whole CSS block, or to individual rules.
 *
 * Note that ___VAR___ type variables are used for the Conditional-CSS online compiler - it's safe 
 * to either ignore these or simply remove them.
 */




/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Class and interface declerations
 */


/*
 * Interface: ccss
 * Purpose:   'Prototype' functions for API
 * Notes:     See the implemention header comment for the purpose etc
 */
interface ifCcss
{
    /* Recommended public functions */
	public function fnComplete ();
	
	/* Other public functions */
	public function fnAddFiles ();
	public function fnReadCSSFiles ();
	public function fnCssIncludes ();
	public function fnStripComments ();
	public function fnProcess ();
	public function fnOutput ();
	public function fnOutputHeader ();
	public function fnSetUserBrowser ();
	public function fnSetUserBrowserGET ();
	public function fnSwitches ();
	public function fnOutputUsage(  );
	public function fnSetBrowserGroup ( $aGroups );
}


/*
 * Class:   ccss
 * Purpose: Preform c-css parsing
 */
class ccss implements ifCcss
{
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Public variables
	 */
	
	/*
	 * Variable: array string:aCssFiles
	 * Purpose:  Always include files
	 * Scope:    ccss
	 * Notes:    Define your CSS files to be included here. Remember that  
	 *   Conditional-CSS will automatically expand @import statements into the 
	 *   full css file. For  example:
	 *     $aCssFiles = array( 'site.css', 'fonts.css' );
	 */
	public $aCssFiles = array(
	  "conditional.css"
	);
	
	
	/*
	 * Variable: array array:aGroups
	 * Purpose:  Browser group definitions
	 * Scope:    ccss
	 * Notes:    Browsers can be groups together such that a  single conditional
	 *   statement can refer to multiple browsers. For example 'cssA' might be 
	 *   top level css support
	 *   The sub arrays must have the following information:
	 *      string:sGrade - CSS group name for easy groupng of statements
	 *      string:sEngine - Engine name (used in the conditional statements)
	 *      int:iGreaterOrEqual - whether the statements should apply for the browser
	 *        version greater (1) or less (0) that the given version
	 *      float:dVersion - The engine version for the condition of this group
	 */
	public $aGroups = array(
        array( 
        	'sGrade'=>'css3', 
        	'sEngine'=>'IE',     
        	'iGreaterOrEqual'=>1,
        	'dVersion'=>10 ), /* IE 10 :-D and up... maybe */
        array( 
            'sGrade'=>'css3', 
            'sEngine'=>'Opera',     
            'iGreaterOrEqual'=>1,
            'dVersion'=>10 ), /* Opera... v12 and up if it gets fixed :P */
        array( 
            'sGrade'=>'css3', 
            'sEngine'=>'Gecko',     
            'iGreaterOrEqual'=>1,
            'dVersion'=>1.9 ), /* Firefox 2 and up, for 3.6 and up would be dVersion 1.9.2, but that't not float o_O */
        array( 
            'sGrade'=>'css3', 
            'sEngine'=>'Webkit',     
            'iGreaterOrEqual'=>1,
            'dVersion'=>528 ), /* Safari 4, Chrome */
		array( 
			'sGrade'=>'cssA', 
			'sEngine'=>'IE',     
			'iGreaterOrEqual'=>1,
			'dVersion'=>6 ), /* IE 6 and up */
		array( 
			'sGrade'=>'cssA', 
			'sEngine'=>'Gecko',  
			'iGreaterOrEqual'=>1, 
			'dVersion'=>1.0 ), /* Mozilla 1.0 and up */
		array( 
			'sGrade'=>'cssA', 
			'sEngine'=>'Webkit', 
			'iGreaterOrEqual'=>1, 
			'dVersion'=>312 ), /* Safari 1.3 and up  */
		array( 
			'sGrade'=>'cssA', 
			'sEngine'=>'SafMob', 
			'iGreaterOrEqual'=>1, 
			'dVersion'=>312 ), /* All Mobile Safari  */
		array( 
			'sGrade'=>'cssA', 
			'sEngine'=>'Opera',  
			'iGreaterOrEqual'=>1, 
			'dVersion'=>7 ), /* Opera 7 and up */
		array( 
			'sGrade'=>'cssA', 
			'sEngine'=>'Konq',   
			'iGreaterOrEqual'=>1, 
			'dVersion'=>3.3 ), /* Konqueror 3.3 and up  */
		array( 
			'sGrade'=>'cssX', 
			'sEngine'=>'IE',     
			'iGreaterOrEqual'=>0, 
			'dVersion'=>4   ), /* IE 4 and down */
		array( 
			'sGrade'=>'cssX', 
			'sEngine'=>'IEMac',  
			'iGreaterOrEqual'=>0, 
			'dVersion'=>4.5 )  /* IE Mac 4 and down */
	);
	
	
	/*
	 * Variable: string:sVersion
	 * Purpose:  version information {major.minor.language.bugfix}
	 * Scope:    ccss - public
	 */
	public $sVersion = '1.2.php.4';
	
	
	/*
	 * Variable: string:sUserBrowser
	 * Purpose:  Store the target browser
	 * Scope:    ConditionalCSS.CCss - public
	 */
	public $sUserBrowser = "";
	
	
	/*
	 * Variable: string:dUserVersion
	 * Purpose:  Store the target browser version
	 * Scope:    ConditionalCSS.CCss - public
	 */
	public $sUserVersion = 0;
	
	
	/*
	 * Variable: string:sUserGroup
	 * Purpose:  Store the target group
	 * Scope:    ConditionalCSS.CCss - public
	 */
	public $sUserGroup = "";
	
	
	/*
	 * Variable: string:sUserAgent
	 * Purpose:  Store the target user agent string
	 * Scope:    ConditionalCSS.CCss - public
	 */
	public $sUserAgent = "";
	
	
	/*
	 * Variable: string:sAuthor
	 * Purpose:  Author information for the CSS file for inclusion in the header
	 * Scope:    ConditionalCSS.CCss - public
	 */
	public $sAuthor = "Bazyli Brzoska"; //  "";
	
	
	/*
	 * Variable: string:sCopyright
	 * Purpose:  Copyright information for the CSS file for inclusion in the header
	 * Scope:    ConditionalCSS.CCss - public
	 */
	public $sCopyright = "Creative Commons NC-SA"; // "";
	
	
	
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Private variables
	 */
	
	/*
	 * Variable: array string:asCSSFiles
	 * Purpose:  CSS files to be read
	 * Scope:    ccss - private
	 */
	private $_asCSSFiles;
	
	/*
	 * Function: string:_sCSS
	 * Purpose:  css buffer
	 * Scope:    ccss - private
	 */
	private $_sCSS = '';
	
	
	
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Public methods
	 */
	
	/*
	 * Function: __construct
	 * Purpose:  ccss constructor
	 * Returns:  -
	 * Inputs:   string:... - any number of string variables pointing to files
	 */
	function __construct ()
	{
		// If the object is created with arguments, store them as files
		$this->_asCSSFiles = func_get_args();
	}
	
	
	/*
	 * Function: fnComplete
	 * Purpose:  Perform a standard Conditional-CSS parsing run on input files
	 * Returns:  -
	 * Inputs:   -
	 * Notes:    You might want to customise this function or use the public functions to create
	 *   your own processing functionality
	 */
	public function fnComplete ()
	{
		/* Get the 'main' arguments */
		global $argv;
		global $argc;
		
		/*
		 * Set up the required variables based on input
		 */
		$this->fnSetUserBrowserGET();   /* Allow GET vars */
		$iOptind = $this->fnSwitches(); /* CLI switches */
		$this->fnSetUserBrowser();
		$this->fnSetBrowserGroup( $this->aGroups );
		
		$this->fnOutputHeader();
		
		/*
		 * Add files
		 */
		for ( $i=$iOptind ; $i<$argc ; $i++ )
		{
			$this->fnAddFiles( $argv[$i] );
		}
		
		for ( $i=0 ; $i<count($this->aCssFiles) ; $i++ )
		{
			$this->fnAddFiles( $this->aCssFiles[$i] );
		}
		
		/*
		 * Read all required files
		 */
		$this->fnReadCSSFiles();
		$this->fnCssIncludes();
		/*
		 * Do the c-css magic on the imported files
		 */
		$this->fnProcess();
		$this->fnOutput();
	}
	
	
	/*
	 * Function: fnAddFiles
	 * Purpose:  add new files to be processed
	 * Returns:  -
	 * Inputs:   string:... - any number of string variables pointing to files
	 */
	public function fnAddFiles ()
	{
		for ( $i=0 ; $i<func_num_args() ; $i++ )
		{
			$this->_asCSSFiles[] = func_get_arg( $i );
		}
	}
	
	
	/*
	 * Function: fnReadCSSFiles
	 * Purpose:  Read the CSS files
	 * Returns:  -
	 * Inputs:   string:... - any number of string variables pointing to files
	 */
	public function fnReadCSSFiles ()
	{
		$this->_sCSS = '';
		for ( $i=0 ; $i<count($this->_asCSSFiles) ; $i++ )
		{
			$this->_sCSS .= $this->_fnReadCSSFile( $this->_asCSSFiles[$i] );
		}
	}
	
	
	/*
	 * Function: fnCssIncludes
	 * Purpose:  Check the input for @import statements and include files found
	 * Returns:  -
	 * Inputs:   -
	 */
	public function fnCssIncludes ()
	{
		// First remove any comments as they could get in the way
		$this->fnStripComments();
		
		// Find all conditional @import statements
		while ( preg_match( '/\[if .*?\]\s*?@import .*?;/s', $this->_sCSS, $aMatch ) )
		{
			preg_match( "/\[if .*?\]/", $aMatch[0], $aCCBlock );
			$sImport = trim( preg_replace( "/\[if .*?\]/", "", $aMatch[0] ) );
			
			$this->fnCssImport( $sImport, $this->_fnCheckCC( $aCCBlock[0] ), $aMatch[0] );
			unset ( $aMatch );
		}
		
		// Find all non-conditional @import statements
		while ( preg_match( '/@import .*?;/s', $this->_sCSS, $aMatch ) )
		{
			$this->fnCssImport( $aMatch[0], 1, $aMatch[0] );
			unset ( $aMatch );
		}
	}
	
	
	/*
	 * Function: fnCssImport
	 * Purpose:  Deal with an import CSS file
	 * Returns:  -
	 * Inputs:   string:sImportStatement - @import...
	 *           int:iImport - include the file or not
	 *           string:sFullImport - The full string to remove
	 */
	public function fnCssImport ( $sImportStatement, $iImport, $sFullImport )
	{
		if ( $iImport == 1 )
		{
			// Parse @import to get the URL
			$sCSSFile = $this->_fnParseImport( $sImportStatement );
			
			// Read the CSS file
			$sTmpCSS = $this->_fnReadCSSFile( $sCSSFile );
			
			// Save it back into the main css string
			$this->_sCSS = str_replace( $sFullImport, $sTmpCSS, $this->_sCSS );
			
			// Remove comments to ease parsing
			$this->fnStripComments();
		}
		else
		{
			/* Remove the import statement */
			$this->_sCSS = str_replace( $sFullImport, "", $this->_sCSS );
		}
	}
	
	
	/*
	 * Function: fnStripComments
	 * Purpose:  Strip multi-line comments from the target css
	 * Returns:  -
	 * Inputs:   -
	 */
	public function fnStripComments ()
	{
		$this->_sCSS = preg_replace ( '/\/\*.*?\*\//s', "", $this->_sCSS );
	}
	
	
	/*
	 * Function: fnProcess
	 * Purpose:  Strip multi-line comments from the target css
	 * Returns:  -
	 * Inputs:   -
	 */
	public function fnProcess ()
	{
		// Break the CSS down into blocks
		// Match all blacks - with or without nested blocks
		preg_match_all( "/.*?\{((?>[^{}]*)|(?R))*\}/s", $this->_sCSS, $aCSSBlock );
		
		for ( $i=0 ; $i<count($aCSSBlock[0]) ; $i++ )
		{
			$iProcessBlock = 1;
			$sBlock = $aCSSBlock[0][$i];
			
			// Find if the block has a conditional comment
			if ( preg_match( "/\[if .*?\].*?\{/s", $sBlock ) )
			{
				preg_match( "/\[if .*?\]/", $sBlock, $aCCBlock );
				
				// Find out if the block should be included or not
				if ( $this->_fnCheckCC ( $aCCBlock[0] ) == 0 )
				{
					$iProcessBlock = 0;
					
					// Drop the block from the output string
					$this->_sCSS = str_replace ( $aCSSBlock[0][$i], "", $this->_sCSS );
				}
				// If it should be then remove the conditional comment from the start 
				// of the block
				else
				{
					$sBlock = preg_replace( "/\[if .*?\]/", "", $sBlock, 1 );	
				}
			}
			
			// If the block should be processed
			if ( $iProcessBlock == 1 )
			{
				// Loop over the block looking for conditional comment statements
				while ( preg_match( "/\[if .*?\]/", $sBlock, $aCSSRule ) )
				{
					// See if statement should be included or not
					if ( $this->_fnCheckCC( $aCSSRule[0] ) == 0 )
					{
						// Remove statement - note that this might remove the trailing
						// } of the block! This is valid css as the last statement is
						// implicitly closed by the }. So we moke sure there is one at the
						// end later on
						$sBlock = preg_replace( '/\[if .*?\].*?(;|\})/s', "", $sBlock, 1 );
					}
					// Include statement
					else
					{
						// Remove CC
						$sBlock = preg_replace( "/\[if .*?\]/", "", $sBlock, 1 );
					}
				}
				
				// Ensure the block has a closing }
				if ( preg_match ( '/\}$/', $sBlock ) == 0 )
				{
					$sBlock .= "}";
				}
				
				// Write the modifed block back into the CSS string
				$this->_sCSS = str_replace( $aCSSBlock[0][$i], $sBlock, $this->_sCSS );
			}
		}
	}
	
	
	/*
	 * Function: fnOutput
	 * Purpose:  Remove extra white space and output
	 * Returns:  -
	 * Inputs:   -
	 */
	public function fnOutput ()
	{
		// Remove the white space in the css - while preserving the needed spaces
		$this->_sCSS = preg_replace( '/\s/s', ' ', trim($this->_sCSS) );
		while ( preg_match ( '/  /', $this->_sCSS ) )
		{
			$this->_sCSS = preg_replace( '/  /', ' ', $this->_sCSS );
		}
		
		// Add new lines for basic legibility
		$this->_sCSS = preg_replace( '/} /', "}\n", $this->_sCSS );
		
		// Phew - we finally got there...
		echo $this->_sCSS;
		echo "\n";
	}
	
	
	/*
	 * Function: fnOutputHeader
	 * Purpose:  Header output with information
	 * Returns:  -
	 * Inputs:   -
	 */
	public function fnOutputHeader ()
	{
		// Give a CSS MIME type so the browser knows this is a css file
		header('Content-type: text/css');
		
		// Add comment to output
		echo "/*\n";
		echo " * c-css by U4EA Technologies - Allan Jardine\n";
		echo " * Version:       ".$this->sVersion."\n";
		
		if ( $this->sAuthor != "" )
		{
		echo " * CSS Author:    ".$this->sAuthor."\n";
		}
		
		if ( $this->sCopyright != "" )
		{
		echo " * Copyright:     ".$this->sCopyright."\n";
		}
		
		echo " * Browser:       ".$this->sUserBrowser." ".$this->sUserVersion."\n";
		echo " * Browser group: ".$this->sUserGroup."\n";
		echo " */\n";
		
  	/* X grade CSS means the browser doesn't see the CSS at all */
		if ( $this->sUserGroup == "cssX" )
		{
			exit(0);
		}
	}
	
	
	/*
	 * Function: fnSetUserBrowser
	 * Purpose:  Set the user's browser information
	 * Returns:  -
	 * Inputs:   -
	 */
	public function fnSetUserBrowser ()
	{
		/* Check we are not overriding a CLI or GET set of the browser */
		if ( $this->sUserBrowser != ""  )
		{
			return;
		}
		
		if ( $this->sUserAgent == "" )
		{
			$this->sUserAgent = $_SERVER['HTTP_USER_AGENT'];
		}
		
		
		// Safari Mobile
		if ( preg_match( '/mozilla.*applewebkit\/([0-9a-z\+\-\.]+).*mobile.*/si', $this->sUserAgent, $aUserAgent ) )
		{
			$this->sUserBrowser = "SafMob";
			$this->sUserVersion = $aUserAgent[1];
		}
		
		// Webkit (Safari, Shiira etc)
		else if ( preg_match( '/mozilla.*applewebkit\/([0-9a-z\+\-\.]+).*/si', $this->sUserAgent, $aUserAgent ) )
		{
			$this->sUserBrowser = "Webkit";
			$this->sUserVersion = $aUserAgent[1];
		}
		
		// Opera
		else if ( preg_match( '/mozilla.*opera ([0-9a-z\+\-\.]+).*/si', $this->sUserAgent, $aUserAgent ) 
		  || preg_match( '/^opera\/([0-9a-z\+\-\.]+).*/si', $this->sUserAgent, $aUserAgent ) )
		{
			$this->sUserBrowser = "Opera";
			$this->sUserVersion = $aUserAgent[1];
        }
		
		// Gecko (Firefox, Mozilla, Camino etc)
		else if ( preg_match( '/mozilla.*rv:([0-9a-z\+\-\.]+).*gecko.*/si', $this->sUserAgent, $aUserAgent ) )
		{
			$this->sUserBrowser = "Gecko";
			$this->sUserVersion = $aUserAgent[1];
		}
		
    // IE Mac
		else if( preg_match( '/mozilla.*MSIE ([0-9a-z\+\-\.]+).*Mac.*/si', $this->sUserAgent, $aUserAgent ) )
		{
			$this->sUserBrowser = "IEMac";
			$this->sUserVersion = $aUserAgent[1];
		}
		
    // MS mobile
		else if( preg_match( '/PPC.*IEMobile ([0-9a-z\+\-\.]+).*/si', $this->sUserAgent, $aUserAgent ) )
		{
			$this->sUserBrowser = "IEMob";
			$this->sUserVersion = "1.0";
		}
		
		// MSIE
		else if( preg_match( '/mozilla.*?MSIE ([0-9a-z\+\-\.]+).*/si', $this->sUserAgent, $aUserAgent ) )
		{
			$this->sUserBrowser = "IE";
			$this->sUserVersion = $aUserAgent[1];
		}
		
		// Konqueror
		else if( preg_match( '/mozilla.*konqueror\/([0-9a-z\+\-\.]+).*/si', $this->sUserAgent, $aUserAgent ) )
		{
			$this->sUserBrowser = "Konq";
			$this->sUserVersion = $aUserAgent[1];
		}
		
		// PSP
		else if( preg_match( '/mozilla.*PSP.*; ([0-9a-z\+\-\.]+).*/si', $this->sUserAgent, $aUserAgent ) )
		{
			$this->sUserBrowser = "PSP";
			$this->sUserVersion = $aUserAgent[1];
		}
		
		// NetFront
		else if( preg_match( '/mozilla.*NetFront\/([0-9a-z\+\-\.]+).*/si', $this->sUserAgent, $aUserAgent ) )
		{
			$this->sUserBrowser = "NetF";
			$this->sUserVersion = $aUserAgent[1];
		}
		
		
		// Round the version number to one decimal place
		$iDot = strpos( $this->sUserVersion, '.' );
		if ( $iDot > 0 )
		{
			$this->sUserVersion = substr( $this->sUserVersion, 0, $iDot+2 );
		}
	}
	
	
	/*
	 * Function: fnSetUserBrowserGET
	 * Purpose:  Set the user's browser information based on GET vars is there
	 *   are any
	 * Returns:  -
	 * Inputs:   -
	 */
	public function fnSetUserBrowserGET ()
	{
		if ( isset( $_GET['b'] ) )
		{
			$this->sUserBrowser = $_GET['b'];
		}
		
		if ( isset( $_GET['browser'] ) )
		{
			$this->sUserBrowser = $_GET['browser'];
		}
		
		if ( isset( $_GET['v'] ) )
		{
			$this->sUserVersion = $_GET['v'];
		}
		
		if ( isset( $_GET['version'] ) )
		{
			$this->sUserVersion = $_GET['version'];
		}
		
		if ( isset( $_GET['a'] ) )
		{
			$this->sAuthor = $_GET['a'];
		}
		
		if ( isset( $_GET['author'] ) )
		{
			$this->sAuthor = $_GET['author'];
		}
		
		if ( isset( $_GET['c'] ) )
		{
			$this->sCopyright = $_GET['c'];
		}
		
		if ( isset( $_GET['copyright'] ) )
		{
			$this->sCopyright = $_GET['copyright'];
		}
	}
	
	
	/*
	 * Function: fnSwitches
	 * Purpose:  Deal with command line switches
	 * Returns:  int:i - Where the sitches end in argc/v
	 * Inputs:   -
	 * Notes:    This is a short hand method to make this script look use
	 *   the same cli options as the 'c' version of this program. It won't do
	 *   well with dodgy input - but it's not expected to be used to much.
	 */
	public function fnSwitches ()
	{
		global $argc;
		global $argv;
		
		for ( $i=1 ; $i<$argc ; $i++ )
		{
			if ( $argv[$i][0] == "-" )
			{
				if ( $argv[$i][1] == "b" )
				{
					$i++;
					$this->sUserBrowser = $argv[$i];
				}
				else if ( $argv[$i][1] == "v" )
				{
					$i++;
					$this->sUserVersion = $argv[$i];
				}
				else if ( $argv[$i][1] == "u" )
				{
					$i++;
					$this->sUserAgent = $argv[$i];
				}
				else if ( $argv[$i][1] == "a" )
				{
					$i++;
					$this->sAuthor = $argv[$i];
				}
				else if ( $argv[$i][1] == "c" )
				{
					$i++;
					$this->sCopyright = $argv[$i];
				}
				else if ( $argv[$i][1] == "h" )
				{
					$this->fnOutputUsage();
					exit(0);
				}
			}
			else
			{
				return $i;
			}
		}
		
		return 1;
	}
	
	
	/*
	 * Function: fnOutputUsage()
	 * Purpose:  Output the usage to the user
	 * Returns:  void
	 * Inputs:   void
	 */
	public function fnOutputUsage(  )
	{
	  printf (
	     "Usage: php c-css.php [OPTIONS]... [FILE]...\n"
	    ."Parse a CSS file which contains IE style conditional comments into a\n"
	    ."stylesheet which is specifically suited for a particular web-browser.\n"
	    ."\n"
	    ." -a     Set the stylesheet's author name for the information header.\n"
	    ." -b     Use this particular browser. Requires that the \n"
	    ."        browser version must also be set, -v. Options are:\n"
	    ."          IE\n"
	    ."          IEMac\n"
	    ."          Gecko\n"
	    ."          Webkit\n"
	    ."          Opera\n"
	    ."          Konq\n"
	    ."          NetF\n"
	    ."          PSP\n"
	    ." -c     Set the copyright header for the information header.\n"
	    ." -h     This help information.\n"
	    ." -u     Browser user agent string.\n"
	    ." -v     Use this particular browser version. Requires that\n"
	    ."        the browser must also be set using -b.\n"
	    ."\n"
	    ."The resulting stylesheet will be printed to stdout. Note that expected\n"
	    ."usage for this PHP version of c-css is through the standard PHP\n"
	    ."interpreter, rather than the CLI.\n"
	    ."\n"
	    ."Example usage:\n"
	    ." php c-css.php -b IE -v 6 example.css\n"
	    ."        Parse a style sheet for Internet Explorer v6\n"
	    ."\n"
	    ." php c-css.php -b Webkit -v 897 demo1.css demo2.css\n"
	    ."        Parse two style sheets for Webkit (Safari) v897\n"
	    ."\n"
	    ." php c-css.php -u \"Mozilla/4.0 (compatible; MSIE 5.5;)\" example.css\n"
	    ."        Parse stylesheet for the specified user agent string\n"
	    ."\n"
	    ."Report bugs to <software@sprymedia.co.uk>\n"
	    ."\n"
	  );
	}
	
	
	/*
	 * Function: fnSetBrowserGroup
	 * Purpose:  Based on the browser grouping we set a short hand method for 
	 *   access
	 * Returns:  void
	 * Inputs:   array:aGroups - group information
	 */
	public function fnSetBrowserGroup ( $aGroups )
	{
		for ( $i=0 ; $i<count($aGroups) ; $i++ )
		{
			if ( $aGroups[$i]['sEngine'] == $this->sUserBrowser )
			{
				if ( $aGroups[$i]['iGreaterOrEqual'] == 1 &&
				     $aGroups[$i]['dVersion'] <= $this->sUserVersion )
				{
					$this->sUserGroup = $aGroups[$i]['sGrade'];
					break;
				}
				else if ( $aGroups[$i]['iGreaterOrEqual'] == 0 &&
				          $aGroups[$i]['dVersion'] >= $this->sUserVersion )
				{
					$this->sUserGroup = $aGroups[$i]['sGrade'];
					break;
				}
			}
		}
	}
	
	
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Private methods
	 */
	
	/*
	 * Function: _fnReadCSSFile
	 * Purpose:  Read a CSS file
	 * Returns:  array string: aCSS - the contents of the css file
	 * Inputs:   string:sPath - the file name and path to be read
	 */
	private function _fnReadCSSFile ( $sPath )
	{
		// We use output buffering here to read the required file using 'readfile'
		// as this allows us to over come some of the problems when safe mode is
		// turned on
		if ( is_file( $sPath ) )
		{
			ob_start();
			readfile( $sPath );
			$sCSS = ob_get_contents();
			ob_end_clean();
			
			// If there is a hash-bang line - strip it out for compatability with C
			$sCSS = preg_replace( '/^(#!.*?\n)/', '', $sCSS, 1 );
			
			return $sCSS;
		}
		else
		{
			echo "/*** Warning: The file $sPath could not be found ***/\n";
		}
	}
	
	
	/*
	 * Function: _fnParseImport
	 * Purpose:  Get the import URI from the import statement
	 * Returns:  string:  - Import URL
	 * Inputs:   string:sImport - @import CSS statement
	 */
	private function _fnParseImport ( $sImport )
	{
		$aImport = explode ( " ", $sImport );
		$sURL = trim($aImport[1]);
		
		if ( substr($sURL, 0, 3) == "url" )
		{
			$sURL = substr( $sURL, 3 );
		}
		$sURL = str_replace ( "(", "", $sURL );
		$sURL = str_replace ( ")", "", $sURL );
		$sURL = str_replace ( "'", "", $sURL );
		$sURL = str_replace ( '"', "", $sURL );
		$sURL = str_replace ( ';', "", $sURL );
		return $sURL;
	}
	
	
	/*
	 * Function: _fnCheckCC
	 * Purpose:  See if a conditional comment should be processed
	 * Returns:  int: 1-process, 0-don't process
	 * Inputs:   string:sCC - the conditional comment
	 *
	 * Notes:
	 * The browser conditions are:
	 *  [if {!} {browser}]
	 *  [if {!} {browser} {version}]
	 *  [if {!} {condition} {browser} {version}]
	 */
	private function _fnCheckCC ( $sCC )
	{
		// Strip brackets from the CC
		$sCC = str_replace( '[', '', $sCC );
		$sCC = str_replace( ']', '', $sCC );
		
		$aCC = explode ( " ", $sCC );
		
		$bNegate = false;
		if ( isset($aCC[1]) && $aCC[1] == "!" )
		{
			$bNegate = true;
			
			// Remove the negation operator so all the other operators are in place
			array_splice ( $aCC, 1, 1 );
		}
		
		//
		// Do the logic checking
		//
		$bInclude = false;
		
		// If the CC is an integer, then we drop the minor version number from the
		// users browser. This means that if the user is using v5.5, and the
		// statement is for v5, then it matches. To stop this a CC with v5.0 would
		// have to be used
		$sLocalUserVersion = $this->sUserVersion;
		if ( count($aCC) == 3 && !strpos( $aCC[2], "." ) ) /* if {browser} {version} */
		{
			$sLocalUserVersion = intval( $sLocalUserVersion );
		}
		else if ( count($aCC) == 4 && !strpos( $aCC[3], "." ) ) /* if {condition} {browser} {version} */
		{
			$sLocalUserVersion = intval( $sLocalUserVersion );
		}
		
		
		// Just the browser
		if ( count( $aCC ) == 2 )
		{
			if ( $this->sUserBrowser == $aCC[1] ||
				   $this->sUserGroup == $aCC[1] )
			{
				$bInclude = true;
			}
		}
		// Browser and version
		else if ( count( $aCC ) == 3 )
		{
			if ( $this->sUserBrowser == $aCC[1] && (float)$sLocalUserVersion == (float)$aCC[2] )
			{
				$bInclude = true;
			}
		}
		// Borwser and version with operator
		else if ( count( $aCC ) == 4 )
		{
			if ( $aCC[1] == "lt" )
			{
				if ( $this->sUserBrowser == $aCC[2] && (float)$sLocalUserVersion < (float)$aCC[3] )
				{
					$bInclude = true;
				}
			}
			else if ( $aCC[1] == "lte" )
			{
				if ( $this->sUserBrowser == $aCC[2] && (float)$sLocalUserVersion <= (float)$aCC[3] )
				{
					$bInclude = true;
				}
			}
			else if ( $aCC[1] == "eq" )
			{
				if ( $this->sUserBrowser == $aCC[2] && (float)$sLocalUserVersion == (float)$aCC[3] )
				{
					$bInclude = true;
				}
			}
			else if ( $aCC[1] == "gte" )
			{
				if ( $this->sUserBrowser == $aCC[2] && (float)$sLocalUserVersion >= (float)$aCC[3] )
				{
					$bInclude = true;
				}
			}
			else if ( $aCC[1] == "gt" )
			{
				if ( $this->sUserBrowser == $aCC[2] && (float)$sLocalUserVersion > (float)$aCC[3] )
				{
					$bInclude = true;
				}
			}
		}
		
		// Perform negation if required
		if ( $bNegate )
		{
			if ( $bInclude )
			{
				$bInclude = false;
			}
			else
			{
				$bInclude = true;
			}
		}
		
		// Return the required type
		if ( $bInclude )
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
};




/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Processing - actually run Conditional-CSS
 */
$oCss = new ccss();
$oCss->fnComplete();

?>