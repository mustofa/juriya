<?php namespace Juriya;

/**
 * Juriya - RAD PHP Framework
 *
 * Router class
 *
 * @package  Juriya
 * @category Core Class
 * @version  0.0.1
 * @author   Taufan Aditya
 */

class Router {

	/**
	 * @var array Server vars
	 */
	public $server = array();

	/**
	 * @var string Request tunnel (`HTTP` or `CLI`)
	 */
	public $tunnel;

	/**
	 * @var string Requested executor
	 */
	public $executor;

	/**
	 * @var string Requested module
	 */
	public $module;

	/**
	 * @var string Controller path
	 */
	public $path;

	/**
	 * @var string Requested controller
	 */
	public $controller;

	/**
	 * @var array Passed arguments
	 */
	public $arguments;

	/**
	 * @var array Matched route
	 */
	public static $route = array();

	/**
	 * Constructor
	 *
	 * @return void
	 */
	function __construct(Collection $config)
	{
		if ($config->valid()) {
			$this->server    = $config->get('server');
			$this->tunnel    = $config->get('tunnel');
			$this->executor  = $config->get('executor');
			$this->arguments = array();
		} else {
			throw new \Exception('Cannot start Router process without proper configuration');
		}
	}

	/**
	 * Detect program routes
	 *
	 * @return  object  Routes information
	 */
	public function detect()
	{
		$this->_detectController();
		$this->_detectParameter();

		return $this;
	}

	/**
	 * Detect requested controller
	 *
	 * @return  void
	 */
	protected function _detectController()
	{
		// Determine passed arguments
		switch($this->tunnel) {
			case 'HTTP':
				$arguments = explode('/', $this->server['REQUEST_URI']);
				array_shift($arguments);

				break;

			case 'CLI';
				$arguments = $this->server['argv'];

				break;
		}

		// Remove front socket
		$arguments = array_filter($arguments, function ($item) use (&$arguments) {
		    next($arguments);

		    return ($item == 'index.php') ? FALSE : $item;
		});

		// Remove query string url
		$arguments = array_map(function ($item) use (&$arguments) {
		    next($arguments);
		    $sanitized_item = parse_url($item);

		    return $sanitized_item['path'];
		}, $arguments);

		// Check the routes configuration against the arguments
		if (FALSE !== ($routes = Juriya::$config['ROUTES'])) {
			if (empty($arguments)) {
				// If no arguments being passed, get default route
				$this->controller =  $routes['default']['controller'];
			}

			// Iterate each routes and find matching pattern
			array_walk($routes, 'Juriya\\Router::_findRoute', $arguments);

			// Inspect the result
			if ( ! empty(self::$route)) {
				$this->controller = self::$route['controller'];
				$this->arguments  = self::$route['arguments'];
			}
		}
		
		// If requested controller still not found, check class existance
		if (is_null($this->controller)) {
			// Looking whether the request is asking for sub-controller or modules
			if (count($arguments) >= 2) {

				// Looking whether sub-controller is exists
				$sub_controller = NS_APP . 'Controllers' . '\\' . implode('\\', $arguments);
				
				if (class_exists($sub_controller)) {
					$this->controller = implode('\\', $arguments);

					return $this;
				}

				// Looking inside modules
				$fragments = array();
				$array     = new \ArrayObject($arguments);
				$iterator  = $array->getIterator();
				$namespace = '\\Mod\\' . ucfirst($iterator->current()) . '\\Controllers\\';
				$iterator->next();
				
				while($iterator->valid()) {
					$fragments[] = $iterator->current();
					$namespace  .= $iterator->current() . '\\';
					$iterator->next();
				}
				
				if (($class_name = substr($namespace, 0, -1)) && class_exists($class_name)) {

					$this->module     = array_shift($arguments);
					$this->path       = $this->module . '.' . implode('\\', $arguments);
					$this->controller = array_pop($fragments);
				}
			}

			// Look-up all available namespace for matching controller
			foreach (Juriya::$ns as $ns) {
				if (($class_name = $ns . 'Controllers\\' . implode('\\', $arguments))
				    && class_exists($class_name)) {
			    	// The request arguments already contain valid controller
			    	$fragments        = new Collection(explode('\\', $class_name));
			    	$this->controller = ucfirst($fragments->last());

			    	continue;
			    }
			}
			
			if (is_null($this->controller)) {
				throw new \Exception('Request not found');
			}
		}
		
		return $this;
	}

	/**
	 * Detect request parameters
	 *
	 * @return  void
	 */
	protected function _detectParameter()
	{
		if ($this->tunnel == 'HTTP') {
			// Determine the HTTP request method
			$http_method = $this->server['REQUEST_METHOD'];

			// Save any http request into Juriya input properties
			switch ($http_method) {
				case 'GET':
					Juriya::$input = $_GET;

					break;

				case 'POST':
					Juriya::$input = array_merge($_GET, $_POST);

					break;

				default:
					parse_str(file_get_contents('php://input'), $input);
					Juriya::$input = $input;

					break;
			}
		}
	}

	protected function _findRoute(&$route, $name, $arguments)
	{
		// Only start checking if argument count matched
		if (count($arguments)  == count($route['arguments'])) {
			if (($intersection = array_intersect($arguments, $route['arguments'])) 
			   && $arguments == $intersection ) {
				// Routes and arguments are literary identical
				self::$route['controller'] = $route['controller'];
				self::$route['arguments']  = $arguments;
			} else {
				// Perform regular expression to catch matched route
				$results =  array_map(function($pattern, $elem) {
					if ( ! empty($pattern) && ! empty($elem)) {
						substr($pattern, 0, 1)  == '/' or $pattern  = '/' . $pattern;
						substr($pattern, -1, 1) == '/' or $pattern .= '/';
						preg_match($pattern, $elem, $matches);

						return ( ! empty($matches) && FALSE !== array_shift($matches));
					}

				}, $route['arguments'], $arguments);

				// Determine the result
				$success = TRUE;

				foreach ($results as $result) {
					if ($result == FALSE) {
						$success = FALSE;

						continue;
					}
				}

				if ($success) {
					// Set match controller and matched arguments
					self::$route['controller'] = $route['controller'];
					self::$route['arguments']  = $arguments;
				}
			}
		}
	}
}