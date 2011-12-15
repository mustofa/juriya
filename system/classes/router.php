<?php namespace Juriya;

/**
 * Juriya - RAD PHP 5 Micro Framework
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
	 * Constructor
	 *
	 * @return void
	 */
	function __construct()
	{
		$this->server = $_SERVER;
		$this->tunnel = Juriya::$method;
		$this->executor = 'execute' . Juriya::$method;
	}

	/**
	 * Detect program routes
	 *
	 * @access  public
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
	 * @access  protected
	 * @return  void
	 */
	protected function _detectController()
	{
		// Determine passed arguments
		switch($this->tunnel)
		{
			case 'HTTP':
				$arguments = explode('/', $this->server['REQUEST_URI']);
				array_shift($arguments);

				break;

			case 'CLI';
				$arguments = $this->server['argv'];

				break;
		}

		// Remove front socket
		$arguments = array_filter($arguments, function ($item) use (&$arguments) 
		{
		    next($arguments);

		    return ($item == 'index.php') ? FALSE : $item;
		});

		// Remove query string url
		$arguments = array_map(function ($item) use (&$arguments) 
		{
		    next($arguments);

		    $sanitized_item = parse_url($item);

		    return $sanitized_item['path'];
		}, $arguments);

		// Check the routes configuration against the arguments
		if (FALSE !== ($routes = Juriya::$config['ROUTES']))
		{
			if (empty($arguments))
			{
				// If no arguments being passed, get default route
				$this->controller =  $routes['default']['controller'];
			}

			// Iterate each routes and find matching pattern
			foreach ($routes as $route_name => $route)
			{
				// Only start checking if argument count matched
				if (count($arguments) == count($route['arguments']))
				{
					if (($intersection = array_intersect($arguments, $route['arguments'])) 
						and $arguments == $intersection )
					{
						// Routes and arguments are literary identical
						$this->controller = $routes[$route_name]['controller'];
					}
					else
					{
						// Perform regular expression to catch matched route
						Juriya::$temp = TRUE;

						array_map(function($pattern, $elem) 
						{
							if ( ! empty($pattern) and ! empty($elem))
							{
								substr($pattern, 0, 1) == '/' or $pattern = '/' . $pattern;
								substr($pattern, -1, 1) == '/' or $pattern .= '/';
								preg_match($pattern, $elem, $matches);

								if ( ! empty($matches) 
									and FALSE !== ($argument = array_shift($matches)))
								{
									Juriya::$temp = TRUE;
								}
								else
								{
									Juriya::$temp = FALSE;
								}
							}

						}, $route['arguments'], $arguments);

						if (Juriya::$temp)
						{
							// Set match controller and matched arguments
							$this->controller = $routes[$route_name]['controller'];
							$this->arguments[] = $arguments;
						}
					}
				}
			}
		}
		
		// If requested controller still not found, check class existance
		if (is_null($this->controller))
		{
			// Looking whether the request is asking for module
			if (count($arguments) >= 2)
			{
				$array = new \ArrayObject($arguments);

				$iterator = $array->getIterator();
				$namespace = '\\Mod\\' . ucfirst($iterator->current()) . '\\Controllers\\';
				$module = $iterator->current();
				$iterator->next();
				$controller_fragments = array();

				while($iterator->valid())
				{
					$controller_fragments[] = $iterator->current();
					$namespace .= $iterator->current() . '\\';
					$iterator->next();
				}

				if (($class_name = substr($namespace, 0, -1))
					and class_exists($class_name))
				{
					$this->module = $module_index;
					$this->controller = array_pop($controller_fragments);
					$this->path = $controller_fragments;
				}
			}

			// Look-up all available namespace for matching controller
			foreach (Juriya::$ns as $ns)
			{
				if (($class_name = $ns . 'Controllers\\' . implode('\\', $arguments))
				     and class_exists($class_name))
			    {
			    	// The request arguments already contain valid controller
			    	$fragments = new Data(explode('\\', $class_name));
			    	$this->controller = ucfirst($fragments->last());

			    	continue;
			    }
			}
			
			if (is_null($this->controller)) throw new \Exception('Request not found');
		}
		
		return $this;
	}

	/**
	 * Detect request parameters
	 *
	 * @access  protected
	 * @return  void
	 */
	protected function _detectParameter()
	{
		if ($this->tunnel == 'HTTP')
		{
			// Determine the HTTP request method
			$http_method = $this->server['REQUEST_METHOD'];

			// Save any http request into Juriya input properties
			switch ($http_method)
			{
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
}