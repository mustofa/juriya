<?php namespace App\Controllers;

/**
 * Juriya - RAD PHP 5 Micro Framework
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

class Hello extends Controller{
	
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
	 * ./config/routes.conf.ini
	 *
	 * If a route matched with request, then the matched argument(s)
	 * will be passed through.
	 *
	 * @return string
	 */
	public function executeHttp($arg1 = NULL, $arg2 = NULL)
	{
		/* Logger mechanism while an executor is running */

		// log_start(__CLASS__);
		// log_write(__CLASS__, 'Some info', 1);
		// log_write(__CLASS__, 'Some Warning', 2);
		// log_write(__CLASS__, 'Some Error', 3);
		// log_write(__CLASS__, 'Just log');

		echo 'Hello World';

		// If the route matched, output the passed arguments
		if ( ! empty($arg1) and ! empty($arg2)) {
			echo '<hr />';
			echo 'Argument 1 was :';
			debug($arg1);
			echo 'Argument 2 was :';
			debug($arg2);
		}

		/* Calling modules controler */

		// Request::factory('foo.bar')->executeHttp();

		/* Logger mechanism to stop and show log report */

		// log_stop(__CLASS__);
		// log_report(__CLASS__);
	}

	/**
	 * Serve CLI execute method
	 * 
	 * @return string
	 */
	public function executeCli()
	{
		echo 'Hello World';
	}

}