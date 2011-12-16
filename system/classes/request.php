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
	 * Prototype :
	 *		Request::factory('foo');            // Return \App\Controllers\Foo
	 *		Request::factory('foo.bar');        // Return \Mod\Foo\Controllers\Bar
	 *
	 * @param   string  controller name or path
	 * @throws  object  Juriya exception
	 * @return  object  controller interface
	 */
	public static function factory($controller)
	{
		if (($fragments = explode('.', $controller))
		    and count($fragments) == 2) {
		    //$sub = array_shift($)
		    $ns = NS_MOD . ucfirst($fragments[0]) . '\\';
		    
		    if (($class_name = $ns . 'Controllers\\' . $fragments[1])
				    and class_exists($class_name)) {
			    	return new $class_name;
			}
		} else {
			foreach (Juriya::$ns as $ns) {
				if (($class_name = $ns . 'Controllers\\' . $controller)
				    and class_exists($class_name)) {
			    	return new $class_name;
			    }
			}
		}
	
		throw new \Exception('Controller not exists');
	}

	/**
	 * Get request routes information
	 *
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
	 * @return  mixed   Response to output
	 */
	public function execute()
	{
		$path       = $this->routes->path;
		$controller = self::factory((empty($path) ? $this->routes->controller : $path));
		$executor   = $this->routes->executor;
		$arguments  = $this->routes->arguments;
		
		return call_user_func_array(array($controller, $executor), $arguments);
	}
}