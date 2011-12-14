<?php namespace system\classes;

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

interface Controller {

	/**
	 * All controller classes must have HTTP execute method
	 * 
	 * @access public
	 * @return string
	 */
	public function executeHTTP();

	/**
	 * All controller classes must have CLI execute method
	 * 
	 * @access public
	 * @return string
	 */
	public function executeCLI();
	
}