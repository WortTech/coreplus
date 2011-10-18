<?php
/**
 * Core bootstrap helper file that handles all the core defines for the system
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


if(PHP_VERSION < '6.0.0' && ini_get('magic_quotes_gpc')){
	die('This application cannot run with magic_quotes_gpc enabled, please disable them now!' . "\n");
}

if(PHP_VERSION < '5.3.0'){
	die('This application requires at least PHP 5.3 to run!' . "\n");
}


/********************* Initial system defines *********************************/

// Right off the bat, I need to decide which mode I'm running in, either as a CLI script or regular.
// In addition, there are some other things that need to be retrieved early on, such as root path and what not.
if(isset($_SERVER['SHELL'])){
	$em = 'CLI';
	$rpdr = $_SERVER['PWD'] . '/';
	$rwdr = null;
	$rip = '127.0.0.1';
}
else{
	$em = 'WEB';
	$rip = '127.0.0.1';
	// Set the constants for the root directory (relative) and root directory (full path).
	$rpdr = pathinfo($_SERVER['SCRIPT_FILENAME' ], PATHINFO_DIRNAME );
	if($rpdr != '/') $rpdr .= '/'; // Append a slash if it's not the root dir itself.
	$rwdr = pathinfo($_SERVER['SCRIPT_NAME' ],     PATHINFO_DIRNAME );
	if($rwdr != '/') $rwdr .= '/'; // Append a slash if it's not the root dir itself.
	$rip = $_SERVER['REMOTE_ADDR'];
}

/**
 * The execution mode of the page.
 * This is used because scripts can run in the command line as well as a webpage.
 *
 * Either 'CLI' or 'WEB'.
 * @var string
 */
define('EXEC_MODE', $em);
/**
 * The physical directory of the CAE2 installation.
 * DOES have a trailing slash.
 *
 * Example: /home/someone/public_html/myinstall/
 * @var string
 */
if(!defined('ROOT_PDIR')) define('ROOT_PDIR', $rpdr);
/**
 * The location of the root installation based on the browser get string.
 * DOES have a trailing slash.
 *
 * Example: /~someone/myinstall/
 * @var string
 */
define('ROOT_WDIR', $rwdr);
/**
 * The remote IP of the connecting computer.
 * Based dynamically off the $_SERVER variable.
 *
 * @var string
 */
define('REMOTE_IP', $rip);

/**
 * FULL_DEBUG is useful for the core development of the platform.
 *
 * @var boolean
 */
define('FULL_DEBUG', false);
//define('FULL_DEBUG', false);

define('NL', "\n");
define('TAB', "\t");

/**
 * define shorthand directory separator constant
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * The GnuPG home directory to store keys in. 
 */
if(!defined('GPG_HOMEDIR')) define('GPG_HOMEDIR', ROOT_PDIR . 'gnupg');


// Cleanup!
unset($em, $rpdr, $rwdr, $rip);