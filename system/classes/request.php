<?php namespace Juriya;

/**
 * Juriya - RAD PHP 5 Micro Framework
 *
 * Request class
 *
 * @package  Juriya
 * @category Core Class
 * @version  0.0.1
 * @author   Taufan Aditya
 */

class Request {

	/**
	 * @var object Routes information for corresponding request
	 */
	public $routes;

	function __construct()
	{
		include_once PATH_SYS . PATH_CLASS . 'controller' . EXT;
	}

	/**
	 * Manufacturing Controller
	 *
	 * @access  public
	 * @param   string  controller name
	 * @return  object  controller interface
	 */
	public static function factory($controller)
	{
		foreach (Juriya::$ns as $ns)
		{
			if (($class_name = $ns . 'Controllers\\'.$controller)

			     and class_exists($class_name))
		    {
		    	return new $class_name;
		    }
		}

		throw new \Exception('Controller not exists');
	}

	/**
	 * Get request routes information
	 *
	 * @access  public
	 * @return  void
	 */
	public function route()
	{
		$this->routes = Juriya::factory('Router')->detect();

		return $this;
	}

	/**
	 * Execute the request
	 *
	 * @access  public
	 * @return  mixed   Response to output
	 */
	public function execute()
	{
		$path = is_array($this->routes->path) ? implode('\\', $this->routes->path) : '';

		$controller = self::factory($path . $this->routes->controller);
		
		$executor = $this->routes->executor;

		$response = $controller->$executor();

		return $response;
	}
}