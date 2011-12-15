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

class Bar extends \Juriya\Controller {
	
	/**
	 * Serve HTTP execute method
	 * 
	 * @access public
	 * @return string
	 */
	public function executeHTTP()
	{
		echo 'Foo Bar';
	}

	/**
	 * Serve CLI execute method
	 * 
	 * @access public
	 * @return string
	 */
	public function executeCLI()
	{
		echo 'Foo Bar';
	}

}