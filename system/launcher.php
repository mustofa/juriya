<?php if (strpos(phpversion(), '5.') !== 0) die('Require PHP 5.');

/**
 *---------------------------------------------------------------
 * Set framework paths and main constants.
 *---------------------------------------------------------------
 */
// Define application environment
define('ENVIRONMENT', $environment);

// Define frameworks paths
define('PATH_APP',   realpath($application) . DIRECTORY_SEPARATOR);
define('PATH_MOD',   realpath($modules)     . DIRECTORY_SEPARATOR);
define('PATH_PKG',   realpath($packages)    . DIRECTORY_SEPARATOR);
define('PATH_SYS',   realpath($system)      . DIRECTORY_SEPARATOR);

// Common sub-paths
define('PATH_CLASS', 'classes' . DIRECTORY_SEPARATOR);
define('PATH_LIB',   'lib'     . DIRECTORY_SEPARATOR);

// Define frameworks namespaces
define('NS_APP', 'App\\');
define('NS_MOD', 'Mod\\');
define('NS_SYS', 'Juriya\\');

// Define PHP extension
define('EXT', '.php');

// Unset global variable
unset($environment, $application, $modules, $packages, $system);

/**
 *---------------------------------------------------------------
 * Error reporting and display levels.
 *---------------------------------------------------------------
 */
// Adjust the error reporting and display levels for each environment.
if (ENVIRONMENT == 'development') {
	error_reporting(E_ALL | E_STRICT);
} else {
	error_reporting(0);
}

// Turn off any errors reports.
ini_set('display_errors', 'Off');

/**
 *---------------------------------------------------------------
 * Load main framework handler.
 *---------------------------------------------------------------
 */
// Load main core handler classes
require_once PATH_SYS . PATH_CLASS . 'juriya' . EXT;
require_once PATH_SYS . PATH_CLASS . 'exception' . EXT;
require_once PATH_SYS . PATH_CLASS . 'logger' . EXT;
require_once PATH_SYS . PATH_CLASS . PATH_LIB . 'socket' . EXT;
require_once PATH_SYS . PATH_CLASS . PATH_LIB . 'data' . EXT;

/**
 *---------------------------------------------------------------
 * Import core framework classes.
 *---------------------------------------------------------------
 */
use Juriya\Juriya;
use Juriya\Exception;
use Juriya\Logger;
use Juriya\Collection;

/**
 *---------------------------------------------------------------
 * Set framework environment and appropriate handler.
 *---------------------------------------------------------------
 */
// Limit maximum execution time
set_time_limit(300);

// Register core autoloader
spl_autoload_register('\\Juriya\\Juriya::autoload');

// Register exception and error handler
set_exception_handler(function($e) {
	Exception::make($e)->handleException();
});

set_error_handler(function($errno, $errstr, $errfile, $errline) {
	$e = array($errno, $errstr, $errfile, $errline);
	Exception::make($e)->handleError();
});

// Register shutdown handler
register_shutdown_function(function() {
	// Find some premature script error
	$lasterror = error_get_last();

	if ( ! empty($lasterror)) {
		Exception::make(array_values($lasterror))->handleError();
	}

	// Stop all log proccess and generate log reports
	Logger::stop('Juriya\\Juriya');
	Logger::report();
});

/**
 *---------------------------------------------------------------
 * Define framework low-level functions.
 *---------------------------------------------------------------
 */
 // Debugger method
function debug() {
	$vars   = func_get_args();
	echo call_user_func_array(array('\\Juriya\\Juriya', 'debug'), $vars);
}

// Logger methods
function log_start() {
	if (func_num_args() === 1) {
		$class = func_get_args();

		return call_user_func_array(array('\\Juriya\\Logger', 'start'), $class);
	} 
	
	throw new Exception('Cannot start log process for undefined class');
}

function log_stop() {
	if (func_num_args() === 1) {
		$class = func_get_args();

		return call_user_func_array(array('\\Juriya\\Logger', 'stop'), $class);
	} 
	
	throw new Exception('Cannot stop log process for undefined class');
}

function log_report() {
	if (func_num_args() === 1) {
		$class = func_get_args();

		return call_user_func_array(array('\\Juriya\\Logger', 'report'), $class);
	} 
	
	throw new Exception('Cannot report log process for undefined class');
}

function log_write() {
	if (func_num_args() >= 2) {
		$log = func_get_args();

		return call_user_func_array(array('\\Juriya\\Logger', 'write'), $log);
	} 
	
	throw new Exception('Cannot write log process for undefined class');
}

/**
 *---------------------------------------------------------------
 * Load bootstrap, configuration and instantiate new launcher.
 *---------------------------------------------------------------
 */
// Bootstrap pool
$bootstrap = include_once(PATH_APP . 'bootstrap' . EXT);

// Config pool
$configs = array();

// Set config path and scan the directory
$path    = PATH_APP;
$config  = $path . 'config' . DIRECTORY_SEPARATOR;
is_dir($config) and $files = scandir($config);

if (isset($files) && ! is_null($files)) {
	// Walk through files and extract config if exists
	array_walk($files, function(&$file, $i) use(&$files) { 
		// Only capture ini file
		global $config;

		if (substr($file, -3, 3) == 'ini') {
			$file = parse_ini_file($config . $file, TRUE);
		} else {
			unset($files[$i]);
		}
	});

	// Assign config collection into config pool
	$configs = $files;
}

// Reset all vars
unset($path, $config, $files);

// Prepare Juriya Configuration
$config  = new Collection();
$config->set('configuration', $configs);

// Instantiate new launcher
$launcher = new Juriya($bootstrap, $config);

// Reset bootstrap and configs value
unset($bootstrap, $config, $configs);

/**
 *---------------------------------------------------------------
 * Launch program.
 *---------------------------------------------------------------
 */

// Execute the application
$launcher->execute();