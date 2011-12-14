<?php namespace system\classes;

/**
 * Juriya - RAD PHP 5 Micro Framework
 *
 * Base View
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
	 * @var string Requested controller
	 */
	public $controller;

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

		// Set controller namespace
		$controller_ns = 'application\\controllers\\';

		// Check the routes configuration against the arguments
		if (FALSE !== ($routes = Juriya::$config['ROUTES']))
		{
			if (empty($arguments))
			{
				// Get the default route
				$this->controller = $controller_ns 

								  . $routes['default']['controller'];
			}
			
			else
			{
				// Iterate each routes and find matching pattern
				foreach ($routes as $route_name => $route)
				{
					if (($intersection = array_intersect($arguments, $route['arguments'])) 

						and $arguments == $intersection )
					{
						// Set match controller
						$this->controller = $controller_ns

										  . $routes[$route_name]['controller'];

						continue;
					}
				}

				if (is_null($this->controller)

					and ($controller_name = $controller_ns . implode('\\', $arguments))

					and class_exists($controller_name))
				{
					// The request arguments already contain valid controller
					$this->controller = $controller_name;
				}
			}
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