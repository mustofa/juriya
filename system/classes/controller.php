<?php namespace Juriya;

/**
 * Juriya - RAD PHP 5 Micro Framework
 *
 * Base Controller interface
 *
 * @package  Juriya
 * @category Core Class
 * @version  0.0.1
 * @author   Taufan Aditya
 */

class Controller implements Socket{

	/**
	 * Empty response
	 * 
	 * @access public
	 * @return string
	 */
	public function executeHttp()
	{
		echo 'Empty Response';
	}

	/**
	 * Empty response
	 * 
	 * @access public
	 * @return string
	 */
	public function executeCli()
	{
		echo 'Empty Response';
	}
	
}