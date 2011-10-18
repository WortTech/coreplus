<?php
/**
 * Core bootstrap file that kicks off the entire application
 * 
 * @package Core
 * @since 2011.06
 * @author Charlie Powell <powellc@powelltechs.com>
 * @copyright Copyright 2011, Charlie Powell
 * @license GNU Lesser General Public License v3 <http://www.gnu.org/licenses/lgpl-3.0.html>
 * This system is licensed under the GNU LGPL, feel free to incorporate it into
 * custom applications, but keep all references of the original authors intact,
 * read the full license terms at <http://www.gnu.org/licenses/lgpl-3.0.html>, 
 * and please contribute back to the community :)
 */

/**
 * Welcome to the CAE2 Bootstrapper!
 * 
 * This file is the core of the application; it's responsible for setting up
 *  all the necessary paths, settings and includes.
 */



/********************* Pre-instantiation system checks ************************/

// The bootstrap cannot be called directly.
if(basename($_SERVER['SCRIPT_NAME']) == 'bootstrap.php') die('You cannot call that file directly.');


// I expect some configuration options....
if(PHP_VERSION < '6.0.0' && ini_get('magic_quotes_gpc')){
	die('This application cannot run with magic_quotes_gpc enabled, please disable them now!');
}

if(PHP_VERSION < '5.3.0'){
	die('This application requires at least PHP 5.3 to run!');
}

// Start a timer for performance tuning purposes.
// This will be saved into the Core once that's available.
$start_time = microtime(true);



/********************* Initial system defines *********************************/
require_once('core/bootstrap_predefines.php');

$predefines_time = microtime(true);



/********************** Critical file inclusions ******************************/

require_once('core/bootstrap_preincludes.php');



// __TODO__ Make this errorHandler accept 'hooks' to be fired when a critical error is occured.
// This can include rendering an HTML file to the browser, or some other action.
//error_reporting ( E_ALL ) ;
//require_once("core/classes/ErrorHandler.class.php");


// Load the hook handler, which will allow cross-library/module communication abstractly.
Debug::Write('Loading hook handler');
require_once(ROOT_PDIR . "core/libs/core/HookHandler.class.php");


// Pre includes are ready.
$preincludes_time = microtime(true);

// And start the core!
Debug::Write('Loading core system');
require_once(ROOT_PDIR . 'core/libs/core/InstallTask.class.php');
require_once(ROOT_PDIR . 'core/libs/core/Core.class.php');
//Core::Singleton();


// Configuration handler, for loading any config variable/constant from XML data or the database.
Debug::Write('Loading configs');
require_once(ROOT_PDIR . "core/libs/core/ConfigHandler.class.php");
ConfigHandler::Singleton();


// Give me core settings!
// This will do the defines for the site, and provide any core variables to get started.
$core_settings = ConfigHandler::LoadConfigFile("configuration");

if(!$core_settings){
	die("Please ensure that you copy /config/configuration.xml.example to /config/configuration.xml and edit the appropriate values.");
}


/**
 * If the site is not in "development mode", force errors to be hidden.
 * This is useful to override any common server settings.
 * 
 * (php default is to display them after all...)
 */ 
if(!DEVELOPMENT_MODE){
	error_reporting(0);
	ini_set('display_errors', 0);
}



// Site not configured yet?
// This config-based constant will be set automatically after an installation to mark it's done.
//if(!SITE_CONFIGURED){
//	die('This site has not been configured yet.  If you are the administrator, please edit the XML files in "config" and be sure to set "SITE_CONFIGURED" to true when done.');
	//header('Location: install/');
	//die('If your browser does not refresh, please <a href="install.php">Click Here</a>');
//}

// The TMP_DIR needs to be writable!
if(!is_dir(TMP_DIR)){
	$ds = explode('/', TMP_DIR);
	$d = '';
	foreach($ds as $dir){
		if($dir == '') continue;
		$d .= '/' . $dir;
		if(!is_dir($d)) mkdir($d) or die("Please ensure that " . TMP_DIR . " is writable.");
	}
}



/*******   CALCULATE SEVERAL REQUIRED CONSTANTS, MAINLY ONES FOR PATH AND URL INFORMATION  ********/

/**
 * If the execution mode is as a script, most web-based constants are simply null.
 * This section sets up the following constants:
 * SERVERNAME
 * SERVERNAME_NOSSL
 * SERVERNAME_SSL
 * ROOT_URL
 * ROOT_URL_NOSSL
 * ROOT_URL_SSL
 * CUR_CALL
 */
if(EXEC_MODE == 'CLI'){
	$servername = null;
	$servernameSSL = null;
	$servernameNOSSL = null;
	$rooturl = null;
	$rooturlNOSSL = null;
	$rooturlSSL = null;
	$curcall = null;
	$relativerequestpath = null;
	$ssl = false;
}
else{
	/**
	 * Full URL of server.
	 * ie: http://www.example.com or https://127.0.0.1:8443
	 */
	if (isset ( $_SERVER [ 'HTTPS' ] )) $servername = "https://" ;
	else $servername = "http://" ;
	
	if($core_settings['site_url'] != '') $servername .= $core_settings['site_url'];
	else $servername .= $_SERVER [ 'HTTP_HOST' ];
	
	// First things are first... if site_url is set, it's expected that THAT should
	//  be the only valid URL to use.  If I wait until post-rendering, bad things
	//  can happen.
	if($core_settings['site_url'] != '' && $_SERVER['HTTP_HOST'] != $core_settings['site_url']){
	    $newURL = (isset($_SERVER['HTTPS'])? 'https://' : 'http://') . $core_settings['site_url'] . $_SERVER['REQUEST_URI'];
	    header("Location:" . $newURL);
	    die("If your browser does not refresh, please <a href=\"{$newURL}\">Click Here</a>");
	}
	
	/**
	 * Full URL of server in non-SSL mode.
	 * ie: http://www.example.com:80 or http://localhost:880
	 */
	// Create the server name with no SSL.  This can be used to go from an SSL page to a regular page.
	$servernameNOSSL = str_replace('https://', 'http://', $servername);
	// Check the last several digits of the serverName to see if there's a port number.
	if(preg_match('/\:\d+$/', substr($servernameNOSSL, -6))){
		$servernameNOSSL = preg_replace('/\:\d+$/', ':' . PORT_NUMBER, $servernameNOSSL);
	}
	else{
		$servernameNOSSL .= ':' . PORT_NUMBER;
	}
	// Default port number?
	if(PORT_NUMBER == 80){
		$servernameNOSSL = str_replace(':80', '', $servernameNOSSL);
	}
	
	
	/**
	 * Full URL of server in SSL mode.
	 * ie: https://www.example.com:443 or https://localhost:8443
	 * 
	 * (defaults back to SERVERNAME_NOSSL if ENABLE_SSL is disabled).
	 */
	if(ENABLE_SSL){
		// Create the server name for SSL connections.  This should override any previous port number.
		$servernameSSL = str_replace('http://', 'https://', SERVERNAME);
		// Check the last several digits of the serverName to see if there's a port number.
		if(preg_match('/\:\d+$/', substr($servernameSSL, -6))){
			$servernameSSL = preg_replace('/\:\d+$/', ':' . PORT_NUMBER_SSL, $servernameSSL);
		}
		else{
			$servernameSSL .= ':' . PORT_NUMBER_SSL;
		}
		// Default port number?
		if(PORT_NUMBER_SSL == 443){
			$servernameSSL = str_replace(':443', '', $servernameSSL);
		}
	}
	else{
		$servernameSSL = $servernameNOSSL;
	}
	
	$rooturl = $servername . ROOT_WDIR;
	$rooturlNOSSL = $servernameNOSSL . ROOT_WDIR;
	$rooturlSSL = $servername . ROOT_WDIR;
	$curcall = $servername . $_SERVER['REQUEST_URI'];
	$relativerequestpath = '/' . substr($_SERVER['REQUEST_URI'], strlen(ROOT_WDIR));
	if(strpos($relativerequestpath, '?') !== false) $relativerequestpath = substr($relativerequestpath, 0, strpos($relativerequestpath, '?'));
	$ssl = ( isset($_SERVER['HTTPS']) );
}

/**
 * Full URL of server.
 * ie: http://www.example.com or https://127.0.0.1:8443
 */
define('SERVERNAME', $servername );
/**
 * Full URL of the server forced non-ssl mode.
 * ie: http://www.example.com
 */
define('SERVERNAME_NOSSL', $servernameNOSSL );
/**
 * Full URL of the server forced SSL mode.
 * ie: https://www.example.com or https://127.0.0.1:8443
 */
define('SERVERNAME_SSL', $servernameSSL );
/**
 * URL of web root.
 * ie: http://www.example.com/foo/man/choo/
 */
define('ROOT_URL', $rooturl);
/**
 * URL of web root.
 * ie: http://www.example.com/foo/man/choo/
 */
define('ROOT_URL_NOSSL', $rooturlNOSSL);
/**
 * URL of web root.
 * ie: https://www.example.com/foo/man/choo/
 */
define('ROOT_URL_SSL', $rooturlSSL);
/**
 * Current call/request.
 * ie: /foo/man/choo/?somevariable=true&somethingelse=false
 */
define('CUR_CALL', $curcall);

/**
 * Relative requested path.
 * ie: /User/Login or '/' for the index.
 */
define('REL_REQUEST_PATH', $relativerequestpath);

/**
 * Simple true/false if current page call is via SSL.
 * @var boolean 
 */
define('SSL', $ssl);

/**
 * The GnuPG home directory to store keys in. 
 */
if(!defined('GPG_HOMEDIR')) define('GPG_HOMEDIR', ROOT_PDIR . 'gnupg');

// Cleanup!
unset($servername, $servernameNOSSL, $servernameSSL, $rooturl, $rooturlNOSSL, $rooturlSSL, $curcall, $ssl);
$maindefines_time = microtime(true);



// Now the core of the application, config handler, and all necessary core
//  settings should be available.



/**************************  START EXECUTION *****************************/

// Record the times thus far
Core::AddProfileTime('application_start', $start_time);
Core::AddProfileTime('predefines_complete', $predefines_time);
Core::AddProfileTime('preincludes_complete', $preincludes_time);
Core::AddProfileTime('maindefines_complete', $maindefines_time);

// Datamodel, GOGO!
require_once(ROOT_PDIR . 'core/libs/datamodel/DMI.class.php');
try{
	$dbconn = DMI::GetSystemDMI();
	HookHandler::DispatchHook('db_ready');
}
// This catch statement should be hit anytime the database is not available,
// core table doesn't exist, or the like.
catch(Exception $e){
	// Couldn't establish connection... do something fun!
	// If it's in development mode, redirect back to the installer, which should hopefully
	// get whatever problem this was fixed.
	if(DEVELOPMENT_MODE){
		header('Location: ' . ROOT_WDIR . 'install');
		die();
	}
	// For machines in production mode, well... I'm yet to decide what to do with that specifically,
	// but it should be something nice, like send an email or something.
	else{
		require(ROOT_PDIR . 'core/fatal_error.inc.html');
		die();
	}
}



unset($start_time, $predefines_time, $preincludes_time, $maindefines_time);
/*
 * This is all done from within the component handler now.
Core::_LoadFromDatabase();

// Does the core require an update?
if(Core::GetComponent()->needsUpdated()){
	// w00t for silent upgrades!
	Core::GetComponent()->upgrade();
}
Core::AddProfileTime('core_ready');
*/

// Give me some other useful core systems.
if(EXEC_MODE == 'WEB'){
	try{
		// Sessions are always useful for web apps
		require_once(ROOT_PDIR . 'core/libs/core/Session.class.php');
		Session::Singleton();
		//session_start();
	}
	catch(DMI_Exception $e){
		// There was a DMI exception... it may not have been installed.
		// Reload to the install page and let that take care.
		if(DEVELOPMENT_MODE){
			header('Location: ' . ROOT_WDIR . 'install');
			die();
		}
		else{
			require(ROOT_PDIR . 'core/fatal_error.inc.html');
			die();
		}
	}
}


/**
 * Load all the components
 */
require_once(ROOT_PDIR . 'core/libs/core/ComponentHandler.class.php');
ComponentHandler::Singleton();

// Load all the themes on the system.
require_once(ROOT_PDIR . 'core/libs/core/ThemeHandler.class.php');
ThemeHandler::Load();

HookHandler::DispatchHook('components_loaded');
//var_dump(ComponentHandler::Singleton());
HookHandler::DispatchHook('components_ready');

Core::AddProfileTime('components_load_complete');
