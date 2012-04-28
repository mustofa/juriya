<?php
/**
 *---------------------------------------------------------------
 * Set framework paths and main constants.
 *---------------------------------------------------------------
 */
// Define application environment
define('ENVIRONMENT', 'test');

// Define frameworks paths
define('PATH_IDX',   realpath(__DIR__) . DIRECTORY_SEPARATOR);
define('PATH_APP',   realpath(__DIR__."/../") . DIRECTORY_SEPARATOR);
define('PATH_MOD',   realpath(__DIR__."/../") . DIRECTORY_SEPARATOR);
define('PATH_SYS',   realpath(__DIR__."/../src/Juriya") . DIRECTORY_SEPARATOR);

// Define frameworks namespaces
define('NS_APP', 'App\\');
define('NS_MOD', 'Mod\\');
define('NS_SYS', 'Juriya\\');

// Define PHP extension
define('EXT', '.php');

/**
 *---------------------------------------------------------------
 * Load main framework handler.
 *---------------------------------------------------------------
 */
// Load main core component
require_once PATH_SYS . 'Juriya' . EXT;

/**
 *---------------------------------------------------------------
 * Set framework environment and appropriate handler.
 *---------------------------------------------------------------
 */
// Limit maximum execution time
set_time_limit(300);

// Register core autoloader
spl_autoload_register('\\Juriya\\Juriya::autoload');


// Register tests autoloader
spl_autoload_register('juriya_tests_autoloader');

// Juriya tests autoloader
function juriya_tests_autoloader($class)
{
	// Only triggered when Mock_ fragment found
	if (strpos($class, 'Mock_') !== FALSE)
	{
		$mocks_dir = __DIR__.DIRECTORY_SEPARATOR.'mocks'.DIRECTORY_SEPARATOR;
		$file = str_replace('Mock_', '', $class);
		
		include_once($mocks_dir.strtolower($file).'.php');
	}
}

/**
 *---------------------------------------------------------------
 * Define framework low-level functions.
 *---------------------------------------------------------------
 */
 // Debugger method
function debug() {
	$vars   = func_get_args();
	echo call_user_func_array(array('\\Juriya\\Debugger', 'dump'), $vars);
}

// Logger methods
function log_start() {
	if (func_num_args() === 1) {
		$class = func_get_args();

		return call_user_func_array(array('\\Juriya\\Logger', 'start'), $class);
	} 
	
	throw new InvalidArgumentException('Cannot start log process for undefined class');
}

function log_stop() {
	if (func_num_args() === 1) {
		$class = func_get_args();

		return call_user_func_array(array('\\Juriya\\Logger', 'stop'), $class);
	} 
	
	throw new InvalidArgumentException('Cannot stop log process for undefined class');
}

function log_write() {
	if (func_num_args() >= 2) {
		$log = func_get_args();

		return call_user_func_array(array('\\Juriya\\Logger', 'write'), $log);
	} 
	
	throw new InvalidArgumentException('Cannot write log process for undefined class');
}