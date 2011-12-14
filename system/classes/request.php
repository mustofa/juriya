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

class Request {

	/**
	 * @var object Routes information for corresponding request
	 */
	public $routes;

	/**
	 * Get request routes information
	 *
	 * @access  public
	 * @return  void
	 */
	public function route()
	{
		$this->routes = Juriya::factory('router')->detect();

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
		$controller = Juriya::factory($this->routes->controller, 'C');
		
		$executor = $this->routes->executor;

		$response = $controller->$executor();

		return $this;
	}
}