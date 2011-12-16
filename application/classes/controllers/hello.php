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
	 * @return string
	 */
	public function executeHttp()
	{
		/* Mock up clashes class name */

		// require PATH_APP . PATH_CLASS . 'juriya' . EXT;

		// $mockupAccident = new \App\Juriya();
		// $mockupAccident->say();

		// require PATH_APP . PATH_CLASS . 'controller' . EXT;

		// $mockupAccident = new \Controller();
		// $mockupAccident->say();
		
		/* Logger mechanism while an executor is running */

		// \Juriya\Logger::start(__CLASS__);
		// \Juriya\Logger::write(__CLASS__, 'Some info', 1);
		// \Juriya\Logger::write(__CLASS__, 'Some Warning', 2);
		// \Juriya\Logger::write(__CLASS__, 'Some Error', 3);
		// \Juriya\Logger::write(__CLASS__, 'Just log');

		echo 'Hello World';

		/* Logger mechanism to stop and show log report */

		// \Juriya\Logger::stop(__CLASS__);
		// \Juriya\Logger::report(__CLASS__);
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