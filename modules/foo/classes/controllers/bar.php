<?php namespace Mod\Foo\Controllers;

/**
 * Juriya - RAD PHP Framework
 *
 * Foo Bar module controller
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

class Bar extends Controller {
	
	/**
	 * Serve HMVC or/and Unit Testing
	 *
	 * This `Bar` controller `execute` will invoked,
	 * if there are the following calls :
	 * 
	 * 1. From HMVC tunnel, which mean call came from something like:
	 * $foobar = Request::factory('foo.bar')->execute();
	 *
	 * 2. Unit testing request which contain bellow server argument :
	 * $_SERVER['argv'] = array('foo', 'bar');
	 *
	 * @return mixed
	 */
	public function execute()
	{
		return 'Foo Bar';
	}

	/**
	 * Serve HTTP request
	 * 
	 * This `Bar` controller `executeHttp` will invoked,
	 * if there are HTTP request to the following endpoints :
	 * 
	 * GET /foo/bar
	 *
	 * @return response
	 */
	public function executeHttp()
	{
		$response = new Http();
		$response->code(200);
		$response->header(array('Content-Type' => 'text/html; charset=utf-8'));
		$response->content('Foo Bar');

		return $response;
	}

	/**
	 * Serve CLI execute method
	 * 
	 * This `Bar` controller `executeCli` will invoked,
	 * if there are the following calls from Command-line :
	 * 
	 * $ php index.php foo bar
	 * 
	 * @return response
	 */
	public function executeCli()
	{
		$response = new Cli();
		$response->type('OUT');
		$response->content('Foo Bar' . "\n");

		return $response;
	}

}