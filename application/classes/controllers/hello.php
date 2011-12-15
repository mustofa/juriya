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

class Hello extends \Juriya\Controller{
	
	/**
	 * Serve HTTP execute method
	 * 
	 * @access public
	 * @return string
	 */
	public function executeHTTP()
	{
		// require PATH_APP . PATH_CLASS . 'juriya' . EXT;

		// $mockupAccident = new \App\Juriya();

		// $mockupAccident->say();

		// require PATH_APP . PATH_CLASS . 'controller' . EXT;

		// $mockupAccident = new \Controller();

		// $mockupAccident->say();
		
		echo 'Hello World';
	}

	/**
	 * Serve CLI execute method
	 * 
	 * @access public
	 * @return string
	 */
	public function executeCLI()
	{
		echo 'Hello World';
	}

}