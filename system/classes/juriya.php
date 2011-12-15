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
	private static $_init = FALSE;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	function __construct()
	{
		// Initialize system once
		if (self::initStatus() == FALSE)
		{
			if (defined('STDIN')) self::$method = 'CLI';

			self::init();
		}
	}

	/**
	 * Execute the program
	 *
	 * @access  public
	 * @return  response  HTTP or CLI response
	 */
	public function execute()
	{
		// Create a new request and output the response
		$instance = self::factory('Request');

		return $instance->route()->execute();
	}

	/**
	 * Load configuration and configure system behaviour
	 *
	 * @access  public
	 * @return  void
	 */
	public static function configure($config)
	{
		self::$config = self::factory('Data');

		// Lifted and populate all configuration
		$config = array_map(function ($item) use (&$config) {

		    next($config);

		    $index = key($item);

		    $values = $item;

		    Juriya::$temp = $index;

		    $values = array_map(function ($sub_items) use (&$values) {

		    	next($values);

		    	$index = Juriya::$temp;

		    	foreach ($sub_items as $sub_index => $sub_item)
		    	{
		    		$index = (array) Juriya::$temp;

		    		$keys = explode('.', $sub_index);

		    		Juriya::$config->addCollection(array_merge($index, $keys), $sub_item);
		    	}

		    }, $values);

		    Juriya::$temp = NULL;

		    return FALSE;

		}, $config);

		// Only set if the aliases is enabled
		if (self::$config->get('ALIASES.classes_alias') == '1')
		{
			foreach (self::$config->get('ALIASES.classes') as $alias => $fullname)
			{
				class_alias($fullname, $alias);
			}
		}
	}

	/**
	 * Set initialize status
	 *
	 * @access  public
	 * @return  void
	 */
	public static function init()
	{
		Logger::start(__CLASS__);

		self::$_init = TRUE;
	}

	/**
	 * Get initialize status
	 *
	 * @access  public
	 * @return  boolean
	 */
	public static function initStatus()
	{
		return self::$_init;
	}
	
	/**
	 * Autoloader
	 *
	 * @access  public
	 * @param   string  class path
	 * @return  mixed
	 */
	public static function autoload($class)
	{
		// Fetch namespaces and paths
		list($namespaces, $paths) = self::_namespacePath();
		
		// Strip out the namespaces
		$class_name = str_replace($namespaces, '', $class);

		$class_name = str_replace('\\', DIRECTORY_SEPARATOR, $class_name);
		
		$loaded = FALSE;

		// Itterate over provided paths and include if the class exists
		foreach ($paths as $path)
		{
			$file = $path . PATH_CLASS . strtolower($class_name) . EXT;

			if (($class_file = $file) and file_exists($class_file))
			{
				include_once $file;

				$loaded = TRUE;

				continue;
			}
		}

		if ( ! $loaded) throw new \Exception('File not exists');
	}

	/**
	 * Manufacturing Juriya object
	 *
	 * @access  public
	 * @param   string  class name
	 * @param   mixed   constructor parameters
	 * @return  object  class instance
	 */
	public static function factory($class, $params = null)
	{
		// Fetch namespaces and paths
		list($namespaces, $paths) = self::_namespacePath();

		foreach ($namespaces as $namespace)
		{
			if (($class_name = $namespace . $class) and class_exists($class_name))
			{
				return (is_null($params)) ? new $class_name : new $class_name($params);
			}
		}

		if (class_exists($class)) return new $class;

		throw new \Exception('Class not exists');
	}

	/**
	 * Debug variable(s)
	 *
	 * @access	public
	 * @return	string  HTML chunk
	 */
	public static function debug()
	{
		if (func_num_args() === 0) return;

		// Get all passed variables and dump into readable output
		$vars = func_get_args();

		$output = array();
		
		foreach ($vars as $var)
		{
			$output[] = (self::$method == 'CLI') ? var_export($var, TRUE) : self::dump($var, 1024);
		}

		$output = implode("\n", $output);

		return (self::$method == 'CLI') ? $output : '<pre class="debug">' . $output . '</pre>';
	}

	/**
	 * Dump variable(s)
	 *
	 * @access	public
	 * @param	mixed	the variable to dump
	 * @param   int     length
	 * @param   int     depth level
	 * @return	string  HTML chunk
	 */
	public static function dump(&$var, $length = 128, $level = 0)
	{
		if ($var === NULL)
		{
			return '<small>NULL</small>';
		}
		elseif (is_bool($var))
		{
			return '<small>bool</small> ' . ($var ? 'TRUE' : 'FALSE');
		}
		elseif (is_float($var))
		{
			return '<small>float</small> ' . $var;
		}
		elseif (is_resource($var))
		{
			return '<small>resource</small><span>(' . $var . ')</span>';
		}
		elseif (is_string($var))
		{
			if (strlen($var) > $length)
			{
				// Encode the truncated string
				$str = htmlspecialchars(substr($var, 0, $length), ENT_NOQUOTES, 'utf-8') . '&nbsp;&hellip;';
			}
			else
			{
				// Encode the string
				$str = htmlspecialchars($var, ENT_NOQUOTES, 'utf-8');
			}

			return '<small>string</small><span>('.strlen($var).')</span> "'.$str.'"';
		}
		elseif (is_array($var))
		{
			$output = array();

			// Indentation for this variable
			$space = str_repeat($s = '    ', $level);

			static $marker;

			if ($marker === NULL)
			{
				// Make a unique marker
				$marker = uniqid("\x00");
			}

			if (empty($var))
			{
				// Do nothing
			}
			elseif (isset($var[$marker]))
			{
				$output[] = "(\n$space$s*RECURSION*\n$space)";
			}
			elseif ($level < 5)
			{
				$output[] = "<span>(";

				$var[$marker] = TRUE;

				foreach ($var as $key => &$val)
				{
					if ($key === $marker) continue;

					if ( ! is_int($key))
					{
						$key = '"' . htmlspecialchars($key, ENT_NOQUOTES, 'utf-8') . '"';
					}

					$output[] = "$space$s$key => " . self::dump($val, $length, $level + 1);
				}

				unset($var[$marker]);

				$output[] = "$space)</span>";
			}
			else
			{
				// Depth too great
				$output[] = "(\n$space$s...\n$space)";
			}

			return '<small>array</small><span>(' . count($var) . ')</span> ' . implode("\n", $output);
		}
		elseif (is_object($var))
		{
			// Copy the object as an array
			$array = (array) $var;

			$output = array();

			// Indentation for this variable
			$space = str_repeat($s = '    ', $level);

			$hash = spl_object_hash($var);

			// Objects that are being dumped
			static $objects = array();

			if (empty($var))
			{
				// Do nothing
			}
			elseif (isset($objects[$hash]))
			{
				$output[] = "{\n$space$s*RECURSION*\n$space}";
			}
			elseif ($level < 10)
			{
				$output[] = "<code>{";

				$objects[$hash] = TRUE;

				foreach ($array as $key => &$val)
				{
					if ($key[0] === "\x00")
					{
						// Determine if the access is protected or protected
						$access = '<small>'.($key[1] === '*' ? 'protected' : 'private').'</small>';

						// Remove the access level from the variable name
						$key = substr($key, strrpos($key, "\x00") + 1);
					}
					else
					{
						$access = '<small>public</small>';
					}

					$output[] = "$space$s$access $key => " . self::dump($val, $length, $level + 1);
				}

				unset($objects[$hash]);

				$output[] = "$space}</code>";
			}
			else
			{
				// Depth too great
				$output[] = "{\n$space$s...\n$space}";
			}

			return '<small>object</small>'

				. '<span>' . get_class($var) . '(' . count($array) . ')</span> ' 

				. implode("\n", $output);
		}
		else
		{
			return '<small>' . gettype($var) . '</small> '
					
				. htmlspecialchars(print_r($var, TRUE), ENT_NOQUOTES, 'utf-8');
		}
	}

	/**
	 * Register namespace and paths
	 *
	 * @access	protected
	 * @return	array 
	 */
	protected static function _namespacePath()
	{
		// Register namespaces
		if (self::initStatus() and self::$config->get('MODULES'))
		{
			$namespaces = array(NS_APP) and $paths = array(PATH_APP);

			$modules = self::$config->get('MODULES');
			
			foreach ($modules as $module => $params)
			{
				$namespaces[] = '\\' . $params['namespace'] . '\\';

				$paths[] = $params['path'];
			}

			$namespaces[] = NS_SYS and $paths[] = PATH_SYS;

			self::$ns = new Data($namespaces) and self::$path = new Data($paths);
			
		}
		elseif (self::$ns instanceof Data and self::$path instanceof Data)
		{
			$namespaces = self::$ns->get() and $paths = self::$path->get();
		}
		else
		{
			$namespaces = array(NS_APP, NS_SYS);

			$paths = array(PATH_APP, PATH_SYS);
		}

		return array($namespaces, $paths);
	}
}