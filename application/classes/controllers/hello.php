<?php namespace App\Controllers;

/**
 * Juriya - RAD PHP Framework
 *
 * Hello controller
 *
 * @package  Juriya
 * @category controller
 * @version  0.0.1
 * @author   Taufan Aditya
 */

use \Juriya\Juriya;
use \Juriya\Controller;
use \Juriya\Request;
use \Juriya\Response\Http;
use \Juriya\Response\Cli;

class Hello extends Controller{
	
	/**
	 * Serve HMVC or/and Unit Testing
	 *
	 * This `Hello` controller `execute` will invoked,
	 * if there are the following calls :
	 * 
	 * 1. From HMVC tunnel, which mean call came from something like:
	 * $hello = Request::factory('hello')->execute();
	 *
	 * 2. Unit testing request which contain bellow server argument :
	 * $_SERVER['argv'] = array('hello');
	 *
	 * @return mixed
	 */
	public function execute($arg1 = NULL, $arg2 = NULL)
	{
		if ( ! empty($arg1) && ! empty($arg1)) {
			return array($arg1, $arg2);
		}

		return 'Hello World';
	}

	/**
	 * Serve HTTP request
	 * 
	 * This `Hello` controller `executeHttp` will invoked,
	 * if there are HTTP request to the following endpoints :
	 * 
	 * GET /hello
	 * GET /hello_world/JURIYA
	 * 
	 * and other request method (`POST`, `PUT` or `DELETE`).
	 *
	 * While the first one is obvious, the second endpoint
	 * which is `/hello_world/JURIYA` was matching with
	 * sample routes rule, you can found under :
	 * 
	 * ./application/config/routes.conf.ini
	 *
	 * If a route matched with request, then the matched argument(s)
	 * will be passed through.
	 *
	 * @return response
	 */
	public function executeHttp($arg1 = NULL, $arg2 = NULL)
	{
		/* Logger mechanism while an executor is running */

		// log_start(__CLASS__);
		// log_write(__CLASS__, 'Some info', 1);
		// log_write(__CLASS__, 'Some Warning', 2);
		// log_write(__CLASS__, 'Some Error', 3);
		// log_write(__CLASS__, 'Just log');
		// log_stop(__CLASS__);

		/* Calling modules controler */

		// $foobar = Request::factory('foo.bar')->execute();

		$response = new Http();
		$response->code(200);
		$response->header(array('Content-Type' => 'text/html; charset=utf-8'));
		$response->content('Hello World');

		return $response;
	}

	/**
	 * Serve CLI execute method
	 * 
	 * This `Hello` controller `executeCli` will invoked,
	 * if there are the following calls from Command-line :
	 * 
	 * $ php index.php hello
	 * $ php index.php hello_world JURIYA
	 *
	 * While the first one is obvious, the second call
	 * which is `php index.php hello_world JURIYA` was matching with
	 * sample routes rule, you can found under :
	 * 
	 * ./application/config/routes.conf.ini
	 *
	 * If a route matched with request, then the matched argument(s)
	 * will be passed through.
	 * 
	 * @return response
	 */
	public function executeCli($arg1 = NULL, $arg2 = NULL)
	{
		$response = new Cli();
		$response->type('OUT');
		$response->content('Hello World');

		return $response;
	}

}