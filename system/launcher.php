<?php if (strpos(phpversion(), '5.') === FALSE) die('Require PHP 5');

/**
 *---------------------------------------------------------------
 * Set framework paths and main constants.
 *---------------------------------------------------------------
 */
define('PATH_APP', realpath($application) . DIRECTORY_SEPARATOR);

define('PATH_MOD', realpath($modules) . DIRECTORY_SEPARATOR);

define('PATH_SYS', realpath($system) . DIRECTORY_SEPARATOR);

define('PATH_CLASS', 'classes' . DIRECTORY_SEPARATOR);

define('EXT', '.php');

unset($application, $modules, $system);

/**
 *---------------------------------------------------------------
 * Set framework environment and appropriate handler.
 *---------------------------------------------------------------
 */
require PATH_SYS . PATH_CLASS . 'juriya' . EXT;

set_time_limit(300);

spl_autoload_register('system\classes\Juriya::autoload');

set_exception_handler(function($e) {

	require_once PATH_SYS . PATH_CLASS . 'exception' . EXT;

	$handler = new system\classes\Exception($e);

	$handler->handle();

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

	system\classes\Juriya::configure($config_array);

	unset($config_array);
}

/**
 *---------------------------------------------------------------
 * Launch program.
 *---------------------------------------------------------------
 */
$launcher = new Juriya();

$launcher->execute();

