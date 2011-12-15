<?php if (strpos(phpversion(), '5.') === FALSE) die('Require PHP 5');

/**
 *---------------------------------------------------------------
 * Set framework paths and main constants.
 *---------------------------------------------------------------
 */
define('ENVIRONMENT', $environment);
define('PATH_CLASS',  'classes' . DIRECTORY_SEPARATOR);
define('PATH_APP',    realpath($application) . DIRECTORY_SEPARATOR);
define('PATH_MOD',    realpath($modules) . DIRECTORY_SEPARATOR);
define('PATH_SYS',    realpath($system) . DIRECTORY_SEPARATOR);
define('EXT',         '.php');
define('NS_APP',      '\\App\\');
define('NS_SYS',      '\\Juriya\\');

unset($environment, $application, $modules, $system);

/**
 *---------------------------------------------------------------
 * Load main handler.
 *---------------------------------------------------------------
 */
require_once PATH_SYS . PATH_CLASS . 'juriya' . EXT;
require_once PATH_SYS . PATH_CLASS . 'exception' . EXT;
require_once PATH_SYS . PATH_CLASS . 'logger' . EXT;
require_once PATH_SYS . PATH_CLASS . 'lib' . DIRECTORY_SEPARATOR . 'socket' . EXT;

/**
 *---------------------------------------------------------------
 * Set framework environment and appropriate handler.
 *---------------------------------------------------------------
 */
set_time_limit(300);

set_exception_handler(function($e) 
{
	$handler = new \Juriya\Exception($e);
	
	$handler->handle();

	\Juriya\Logger::write('Juriya\\Juriya', $handler->log(), 3);
});

spl_autoload_register('\\Juriya\\Juriya::autoload');

register_shutdown_function(function() 
{
	\Juriya\Logger::stop('Juriya\\Juriya');
	\Juriya\Logger::report();
});

/**
 *---------------------------------------------------------------
 * Load configuration and set appropriate behaviour
 *---------------------------------------------------------------
 */
$config = PATH_APP . 'config' . DIRECTORY_SEPARATOR;

if (is_dir($config) and ($config_files = scandir($config)))
{
	$config_array = array();

	foreach($config_files as $conf)
	{
		if (substr($conf, -3, 3) == 'ini')
		{
			$file = $config  . $conf;
			file_exists($file) and $config_array[] = parse_ini_file($file, TRUE);
		}
	}

	\Juriya\Juriya::configure($config_array);

	unset($config_array);
}

/**
 *---------------------------------------------------------------
 * Launch program.
 *---------------------------------------------------------------
 */
use \Juriya\Juriya;

$launcher = new Juriya();

$launcher->execute();

