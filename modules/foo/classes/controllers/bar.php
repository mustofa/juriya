<?php namespace Mod\Foo\Controllers;

/**
 * Juriya - RAD PHP 5 Micro Framework
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

class Bar extends Controller {
	
	/**
	 * Serve HTTP execute method
	 * 
	 * @return string
	 */
	public function executeHttp()
	{
		echo 'Foo Bar';
	}

	/**
	 * Serve CLI execute method
	 * 
	 * @return string
	 */
	public function executeCli()
	{
		echo 'Foo Bar';
	}

}