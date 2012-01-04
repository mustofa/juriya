<?php namespace Juriya;

/**
 * Juriya - RAD PHP 5 Micro Framework
 *
 * Juriya core class
 *
 * @package  Juriya
 * @category Core Class
 * @version  0.0.1
 * @author   Taufan Aditya
 */

class Juriya {

	/**
	 * @var string Juriya version
	 */
	const VERSION = '0.0.1';

	/**
	 * @var array Juriya configuration
	 */
	public static $config = array();

	/**
	 * @var array Request parameter
	 */
	public static $input = array();

	/**
	 * @var string Request method
	 */
	public static $method = 'HTTP';

	/**
	 * @var array Namespaces
	 */
	public static $ns;

	/**
	 * @var array Paths
	 */
	public static $path;

	/**
	 * @var string Temporary data
	 */
	public static $temp = NULL;

	/**
	 * @var boolean Juriya initialization status
	 */
	private static $init = FALSE;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	function __construct($config = array())
	{
		// Initialize system once
		if ( ! empty($config) && self::initStatus() == FALSE) {
			// Get the environment runtime
			defined('STDIN') and self::$method = 'CLI';

			// Configure
			self::configure($config);
			self::init();
		} else {
			throw new \Exception('Cannot start Juriya proccess without proper configuration');
		}
	}

	/**
	 * Execute the program
	 *
	 * @return  response  HTTP or CLI response
	 */
	public function execute()
	{
		// Create a new request and output the response
		$instance = new Request();

		return $instance->route()->execute();
	}

	/**
	 * Load configuration and configure system behaviour
	 *
	 * @return  void
	 */
	public static function configure($configs)
	{
		//  Instantiate new collection
		self::$config = new Data();

		// Lifted and populate all configuration
		$configs = array_map(function ($item) use (&$configs) {
		    next($configs);
		    
		    $values = $item;
		    $index  = array_fill(0, count($values), key($item));

		    // Scan the level and inspect mathced cursor
		    $values = array_map(function ($sub_items, $parent_index) use (&$values, $index) {
		    	next($values);

		    	foreach ($sub_items as $sub_index => $sub_item) {
		    		$index = (array) $parent_index;
		    		$keys  = explode('.', $sub_index);
		    		Juriya::$config->set(array_merge($index, $keys), $sub_item);
		    	}
		    }, $values, $index);

		    return FALSE;
		}, $configs);

		// Reset
		unset($configs);

		// Only set if the aliases is enabled
		if (self::$config->get('ALIASES.classes_alias') == '1') {
			foreach (self::$config->get('ALIASES.classes') as $alias => $fullname) {
				class_alias($fullname, $alias);
			}
		}
	}

	/**
	 * Set initialize status
	 *
	 * @return  void
	 */
	public static function init()
	{
		// Start logger and turn on init status
		Logger::start(__CLASS__);
		self::$init = TRUE;
	}

	/**
	 * Get initialize status
	 *
	 * @return  boolean
	 */
	public static function initStatus()
	{
		return self::$init;
	}
	
	/**
	 * Autoloader
	 *
	 * @param   string  class path
	 * @throws  Juriya exception
	 * @return  mixed
	 */
	public static function autoload($class)
	{
		// Fetch then strip out the namespaces from path
		list($namespaces, $paths) = self::_namespacePath();
		$class_name = str_replace($namespaces, '', $class);
		$class_name = str_replace('\\', DIRECTORY_SEPARATOR, $class_name);

		// Set result pointer
		$loaded = FALSE;

		// Itterate over provided paths and include if the class exists
		foreach ($paths as $path) {
			$file = $path . PATH_CLASS . strtolower($class_name) . EXT;
			//debug($file);
			if (($class_file = $file) and file_exists($class_file)) {
				//debug($class_file);
				include_once $class_file;
				$loaded = TRUE;

				continue;
			}
		}
		
		unset($file, $class_file);
		
		// Check result and log appropriate warning if not exist
		if ( ! $loaded) {
			$e       = new \Exception();
			$valid_e = TRUE;

			// Loop over tracer, to see whether the error is really valid
			foreach($e->getTrace() as $trace) {
				// We expect that this was just to check
				if ($trace['function'] == 'class_exists') {
					$valid_e = FALSE;
					Logger::write(__CLASS__, $class . ' not found.', 2);

					continue;
				}
			}

			// Throw exception
			if ($valid_e) {
				throw new \Exception($class . ' not found.');
			}
		}
	}

	/**
	 * Manufacturing Juriya object
	 *
	 * @param   string  class name
	 * @param   mixed   constructor parameters
	 * @throws  object  Juriya Exception
	 * @return  object  class instance
	 */
	public static function factory($class, $params = null)
	{
		// Fetch namespaces and paths
		list($namespaces, $paths) = self::_namespacePath();

		// Itterate over the namespace and instantiate requested class
		foreach ($namespaces as $namespace) {
			if (($class_name = $namespace . $class) && class_exists($class_name)) {
				return (is_null($params)) ? new $class_name : new $class_name($params);
			}
		}

		// Return the class or throw exception
		if (class_exists($class)) {
			return (is_null($params)) ? new $class : new $class($params);
		}

		// throw Juriya expention
		throw new \Exception('Class not exists');
	}

	/**
	 * Debug variable(s)
	 *
	 * @return	string  HTML chunk
	 */
	public static function debug()
	{
		// Only start if there are passed variables
		if (func_num_args() === 0) {
			return;
		};

		// Get all passed variables 
		$vars   = func_get_args();
		$output = array();
		
		// Itterate variables and dump into readable output
		foreach ($vars as $var) {
			$output[] = (self::$method == 'CLI') ? var_export($var, TRUE) : self::dumpHtml($var, 1024);
		}

		// Prepare the output, then return appropriate result
		$output = implode("\n", $output);

		return (self::$method == 'CLI') ? $output : '<pre class="debug">' . $output . '</pre>';
	}

	/**
	 * Dump variable(s) as HTML fragments
	 *
	 * @param   mixed   the variable to dump
	 * @param   int     length
	 * @param   int     depth level
	 * @return	string  HTML chunk
	 */
	public static function dumpHtml(&$var, $length = 128, $level = 0)
	{
		$small = function($var) {
			return '<small>' . $var . '</small>';
		};

		$span  = function($var) {
			return '<span>(' . $var . ')</span>';
		};

		if ($var === NULL) {
			return $small('NULL');
		}

		if (is_bool($var)) {
			return $small('bool ') . ($var ? 'TRUE' : 'FALSE');
		}
		
		if (is_float($var)) {
			return $small('float ') . $var;
		}

		if (is_resource($var)) {
			return $small('resource ') . $span($var);
		}

		if (is_string($var)) {
			if (strlen($var) > $length) {
				// Encode the truncated string
				$str = htmlspecialchars(substr($var, 0, $length), ENT_NOQUOTES, 'utf-8') 
				       . '&nbsp;&hellip;';
			} else {
				// Encode the string
				$str = htmlspecialchars($var, ENT_NOQUOTES, 'utf-8');
			}

			return $small('string ') . $span(strlen($var)) . ' "'.$str.'"';
		}

		if (is_array($var)) {
			// Indentation for this variable
			$output = array();
			$space  = str_repeat($s = '    ', $level);
			static $marker;

			if ($marker === NULL) {
				// Make a unique marker
				$marker = uniqid("\x00");
			}

			if (empty($var)) {
				// Do nothing
			} elseif (isset($var[$marker]) && ! empty($var)) {
				$output[] = "(\n$space$s*RECURSION*\n$space)";
			} elseif ($level < 5) {
				$output[]     = "<span>(";
				$var[$marker] = TRUE;

				foreach ($var as $key => &$val) {
					if ($key === $marker) {
						continue;
					}

					if ( ! is_int($key)) {
						$key = '"' . htmlspecialchars($key, ENT_NOQUOTES, 'utf-8') . '"';
					}

					$output[] = "$space$s$key => " . self::dumpHtml($val, $length, $level + 1);
				}

				unset($var[$marker]);
				$output[] = "$space)</span>";
			} else {
				// Depth too great
				$output[] = "(\n$space$s...\n$space)";
			}

			return $small('array') . $span(count($var)) . implode("\n", $output);
		}

		if (is_object($var)) {
			// Copy the object as an array
			$array = (array) $var and $output = array();

			// Indentation for this variable
			$space = str_repeat($s = '    ', $level);
			$hash  = spl_object_hash($var);

			// Objects that are being dumped
			static $objects = array();

			if (isset($objects[$hash])) {
				$output[] = "{\n$space$s*RECURSION*\n$space}";
			} elseif ($level < 10) {
				$output[] = "<code>{";
				$objects[$hash] = TRUE;

				foreach ($array as $key => &$val) {
					if ($key[0] === "\x00") {
						// Determine the access and remove the access level from the variable name
						$visibility = $key[1] === '*' ? 'protected' : 'private';
						$access     = $small($visibility);
						$key        = substr($key, strrpos($key, "\x00") + 1);
					} else {
						$access = $small('public');
					}

					$output[] = "$space$s$access $key => " 
					            . self::dumpHtml($val, $length, $level + 1);
				}

				unset($objects[$hash]);
				$output[] = "$space}</code>";
			} else {
				// Depth too great
				$output[] = "{\n$space$s...\n$space}";
			}

			return $small('object')
			       . ' <span>' . get_class($var) . '(' . count($array) . ')</span> ' 
			       . implode("\n", $output);
		}
		
		return $small(gettype($var) . ' ')
		       . htmlspecialchars(print_r($var, TRUE), ENT_NOQUOTES, 'utf-8');
	}

	/**
	 * Register namespace and paths
	 *
	 * @return	array 
	 */
	protected static function _namespacePath()
	{
		// Register namespaces
		if (self::initStatus() && self::$config->get('MODULES') && is_null(self::$ns)) {
			$namespaces = array(NS_APP) and $paths = array(PATH_APP);
			$modules    = self::$config->get('MODULES');
			
			foreach ($modules as $module => $params) {
				$namespaces[] = $params['namespace'] . '\\';
				$paths[]      = $params['path'];
			}

			$namespaces[]   = NS_SYS and $paths[] = PATH_SYS;
			self::$ns       = new Data($namespaces) 
			and self::$path = new Data($paths);
		} elseif (self::$ns instanceof Data && self::$path instanceof Data) {
			$namespaces = self::$ns->get() and $paths = self::$path->get();
		} else {
			$namespaces = array(NS_APP, NS_SYS);
			$paths      = array(PATH_APP, PATH_SYS);
		}

		// Return the main 
		return array($namespaces, $paths);
	}
}