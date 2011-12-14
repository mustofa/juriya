<?php namespace application\classes\controllers;

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

class Hello implements \system\classes\Controller{
	
	/**
	 * Serve HTTP execute method
	 * 
	 * @access public
	 * @return string
	 */
	public function executeHTTP()
	{
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