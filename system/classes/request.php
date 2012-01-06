<?php namespace Juriya;

/**
 * Juriya - RAD PHP Framework
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
	 * @var instance Runtime environment
	 */
	public $method;

	/**
	 * @var mixed    DB prototype
	 */
	public static $db;

	/**
	 * @var mixed    Parser prototype
	 */
	public static $parser;

	/**
	 * @var object   Routes information for corresponding request
	 */
	public $routes;

	function __construct(Collection $config)
	{
		// Initialize the request
		if ($config->valid()) {
			// Get the environment runtime
			$this->method = $config->get('method');
			self::$db     = $config->get('db');
			self::$parser = $config->get('parser');
			include_once    $config->get('controller');
		} else {
			throw new \RuntimeException('Cannot start Request without proper configuration');
		}
	}

	/**
	 * Manufacturing Controller
	 *
	 * Prototype :
	 * 	Request::factory('foo');            // Return \App\Controllers\Foo
	 *	Request::factory('foo.bar');        // Return \Mod\Foo\Controllers\Bar
	 *
	 * @param   string  controller name or path
	 * @throws  object  Juriya exception
	 * @return  object  controller interface
	 */
	public static function factory($controller = '')
	{
		if (($fragments = explode('.', $controller)) && count($fragments) == 2) {
			
		    $ns = NS_MOD . ucfirst($fragments[0]) . '\\';
		    
			if (($class_name = $ns . 'Controllers\\' . $fragments[1]) && class_exists($class_name)) {
				$controller_class = new $class_name();

				// Load corresponding model or/and view classes
				if (($model_name = $ns . 'Models\\' . $fragments[1]) && class_exists($model_name)) {
					$controller_class->model = new $model_name(self::$db);
				}

				$controller_class->view = Juriya::factory('View', self::$parser);

				return $controller_class;
			}
		} else {
			foreach (Juriya::$ns as $ns) {
				if (($class_name = $ns . 'Controllers\\' . $controller) && class_exists($class_name)) {
					$controller_class = new $class_name();

					// Load corresponding model or/and view classes
					if (($model_name = $ns . 'Models\\' . $controller) && class_exists($model_name)) {
						$controller_class->model = new $model_name(self::$db);
					}

					$controller_class->view = Juriya::factory('View', self::$parser);

					return $controller_class;
			    }
			}
		}
	
		throw new \LogicException('Controller not exists');
	}

	/**
	 * Get request routes information
	 *
	 * @return  void
	 */
	public function route()
	{
		// Set router configuration
		$config = new Collection();
		$config->set('server', $_SERVER);
		$config->set('tunnel', $this->method);

		// Exception for Unit Testing environment
		// Use the HMVC tunnel instead `HTTP` or `CLI`
		if (ENVIRONMENT == 'test') {
			$config->set('executor', 'execute');
		} else {
			$config->set('executor', 'execute' . ucfirst(strtolower($this->method)));
		}

		// Instantiate and execute new routing process
		$router       = Juriya::factory('Router', $config);
		$this->routes = $router->detect();

		return $this;
	}

	/**
	 * Execute the request
	 *
	 * @return  mixed   Response
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