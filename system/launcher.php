<?php if (strpos(phpversion(), '5.') !== 0) die('Require PHP 5.');

/**
 *---------------------------------------------------------------
 * Set framework paths and main constants.
 *---------------------------------------------------------------
 */
// Define application environment
define('ENVIRONMENT', $environment);

// Define frameworks paths
define('PATH_CLASS', 'classes' . DIRECTORY_SEPARATOR);
define('PATH_APP',   realpath($application) . DIRECTORY_SEPARATOR);
define('PATH_MOD',   realpath($modules) . DIRECTORY_SEPARATOR);
define('PATH_SYS',   realpath($system) . DIRECTORY_SEPARATOR);

// Define frameworks namespaces
define('NS_APP', '\\App\\');
define('NS_SYS', '\\Juriya\\');

// Define PHP extension
define('EXT', '.php');

// Unset global variable
unset($environment, $application, $modules, $system);

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

// Turn off any internal errors.
ini_set('display_errors', 'Off');

/**
 *---------------------------------------------------------------
 * Load main handler.
 *---------------------------------------------------------------
 */
// Load main core handler classes
require_once PATH_SYS . PATH_CLASS . 'juriya' . EXT;
require_once PATH_SYS . PATH_CLASS . 'exception' . EXT;
require_once PATH_SYS . PATH_CLASS . 'logger' . EXT;
require_once PATH_SYS . PATH_CLASS . 'lib' . DIRECTORY_SEPARATOR . 'socket' . EXT;

// Import core class
use \Juriya\Juriya;
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
	\Juriya\Exception::accept($e)->handleException();
});

set_error_handler(function($errno, $errstr, $errfile, $errline) {
	$error = array($errno, $errstr, $errfile, $errline);
	\Juriya\Exception::accept($error)->handleError();
});

// Register shutdown handler
register_shutdown_function(function() {
	\Juriya\Logger::stop('Juriya\\Juriya');
	\Juriya\Logger::report();
});

/**
 *---------------------------------------------------------------
 * Load configuration and set appropriate behaviour
 *---------------------------------------------------------------
 */
// config pool
$configs = array();

// Iterate over all possible paths
foreach (array(PATH_APP, PATH_SYS) as $path) {
	// Set config path and scan the directory
	$config = $path . 'config' . DIRECTORY_SEPARATOR;
	Juriya::$temp = $config;
	is_dir($config) and $files = scandir($config);

	if (isset($files) and ! is_null($files)) {
		// Walk through files and extract config if exists
		array_walk($files, function(&$file, $i) use(&$files) { 
			// Only capture ini file
			if (substr($file, -3, 3) == 'ini') {
				$file = parse_ini_file(Juriya::$temp . $file, TRUE);
			} else {
				unset($files[$i]);
			}
		});

		// Assign config collection into config pool
		$configs = array_merge($configs, $files);
	}

	// Reset 
	unset($path, $config, $files);
}

// Configure Juriya 
Juriya::configure($configs);

/**
 *---------------------------------------------------------------
 * Launch program.
 *---------------------------------------------------------------
 */
// Instantiate new launcher
$launcher = new Juriya();

// Execute the application
$launcher->execute();


