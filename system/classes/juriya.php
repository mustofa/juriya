<?php namespace Juriya;

/**
 * Juriya - RAD PHP Framework
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
	 * @var array Juriya bootstrap
	 */
	public static $bootstrap;

	/**
	 * @var array Juriya configuration
	 */
	public static $config;

	/**
	 * @var array Request parameter
	 */
	public static $input = array();

	/**
	 * @var string Request method / Call Tunnel
	 */
	public static $tunnel = 'HTTP';

	/**
	 * @var array Namespaces
	 */
	public static $ns;

	/**
	 * @var array Paths
	 */
	public static $path;

	/**
	 * @var boolean Juriya initialization status
	 */
	private static $init = FALSE;

	/**
	 * Constructor
	 *
	 * @throws Exception 
	 * @return void
	 */
	function __construct(Array $bootstrap, Collection $config)
	{
		// Initialize system once
		if ( ! empty($bootstrap) &&  ! empty($config) && self::initStatus() == FALSE) {
			// Start logger and turn on init status
			Logger::start(__CLASS__);

			// Get the environment runtime tunnel
			defined('STDIN') and self::$tunnel = 'CLI';

			// Register application bootstrap and configure the framework
			// then we're done
			self::register($bootstrap);
			self::configure($config->get('configuration'));
			self::init();
		} else {
			throw new \RuntimeException('Cannot start Juriya proccess without proper configuration');
		}
	}

	/**
	 * Destructor
	 *
	 * @return void
	 */
	function __destruct()
	{
		// Stop logger
		Logger::stop(__CLASS__);
	}

	/**
	 * Register application bootstrap
	 *
	 * @return  void
	 */
	public static function register(Array $bootstrap)
	{
		//  Instantiate new collection
		self::$bootstrap = new Collection();

		// Register DB instance
		self::$bootstrap->set('db',     $bootstrap['db']['prototype']());
		self::$bootstrap->set('parser', $bootstrap['parser']['prototype']());

		// Reset
		unset($bootstrap);
	}

	/**
	 * Load configuration and configure system behaviour
	 *
	 * @return  void
	 */
	public static function configure(Array $configs)
	{
		//  Instantiate new collection
		self::$config = new Collection();

		// Lifted and populate all configuration
		$configs = array_map(function ($item) use (&$configs) {
		    next($configs);
		    
		    $values = $item;
		    $index  = array_fill(0, count($values), key($item));

		    // Scan the level and inspect mathced cursor
		    $values = array_map(function ($subItems, $parentIndex) use (&$values, $index) {
		    	next($values);

		    	foreach ($subItems as $subIndex => $subItem) {
		    		$index = (array) $parentIndex;
		    		$keys  = explode('.', $subIndex);
		    		Juriya::$config->set(array_merge($index, $keys), $subItem);
		    	}
		    }, $values, $index);

		    return FALSE;
		}, $configs);

		// Reset
		unset($configs);
	}

	/**
	 * Execute the program
	 *
	 * @throws  Exception 
	 * @return  response  HTTP or CLI response
	 */
	public function execute()
	{
		// Retrieve bootstrap prototype
		$db     = self::$bootstrap->get('db');
		$parser = self::$bootstrap->get('parser');

		// Prepare request configuration
		$config = new Collection(); 
		$config->set('controller', PATH_SYS . PATH_CLS . 'controller' . EXT);
		$config->set('tunnel', self::$tunnel);
		$config->set('db', $db);
		$config->set('parser', $parser);


		// Create a new request and output the response
		$instance = self::factory('Request', $config);

		$output = $instance->route()->execute();

		// Determine the way to output the response
		if (ENVIRONMENT == 'test') {
			return $output;
		} elseif ($output instanceof Output) {
			$output->render();
		} else {
			throw new \RangeException('Invalid Output');
		}
	}

	/**
	 * Set initialize status
	 *
	 * @return  void
	 */
	public static function init()
	{
		// Turn on init status
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
	 * @param   string  item path
	 * @return  mixed
	 */
	public static function autoload($item)
	{
		// Ignore browser built-in request
		if (strpos($item, 'favicon.ico') !== FALSE) {
			return;
		}

		// Fetch then strip out the namespaces from path
		list($namespaces, $paths) = self::_namespacePath();
		$itemName = str_replace($namespaces, '', $item);
		$itemName = str_replace('\\', DIRECTORY_SEPARATOR, $itemName);

		// Set result pointer
		$loaded = FALSE;

		// Itterate over provided paths and include if the class exists
		foreach ($paths as $path) {
			$file = $path . PATH_CLS . strtolower($itemName) . EXT;

			if (($classFile = $file) and file_exists($classFile)) {
				include_once $classFile;
				$loaded = TRUE;

				continue;
			}

			// Look for interfaces
			if ( ! $loaded) {
				$file = $path . PATH_IFC . strtolower($itemName) . EXT;

				if (($interfaceFile = $file) and file_exists($interfaceFile)) {
					include_once $interfaceFile;
					$loaded = TRUE;

					continue;
				}
			}
		}
		
		unset($file, $classFile, $interfaceFile);
		
		// Check result and log appropriate warning if not exist
		if ( ! $loaded) {
			$e       = new \Exception();
			$validE  = TRUE;

			// Loop over tracer, to see whether the error is really valid
			foreach($e->getTrace() as $trace) {
				// We expect that this was just to check
				if ($trace['function'] == 'class_exists' || $trace['function'] == 'file_exists') {
					$validE = FALSE;

					continue;
				}
			}

			// Write error, and let further existed autoloader process it
			if ($validE) {
				Logger::write(__CLASS__, $item . ' not found in all known path.', 2);
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
	public static function factory($class, $params = NULL)
	{
		// Fetch namespaces and paths
		list($namespaces, $paths) = self::_namespacePath();

		// Itterate over the namespace and instantiate requested class
		foreach ($namespaces as $namespace) {
			if (($className = $namespace . $class) && class_exists($className)) {
				return (is_null($params)) ? new $className : new $className($params);
			}
		}

		// Return the class or throw exception
		if (class_exists($class)) {
			return (is_null($params)) ? new $class : new $class($params);
		}

		// throw Juriya expention
		throw new \LogicException('Class not exists');
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
			self::$ns       = new Collection($namespaces) 
			and self::$path = new Collection($paths);
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