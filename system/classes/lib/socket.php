<?php namespace Juriya;

/**
 * Juriya - RAD PHP 5 Micro Framework
 *
 * Socket interface
 *
 * @package  Juriya
 * @category Core Class
 * @version  0.0.1
 * @author   Taufan Aditya
 */

interface Socket {

	/**
	 * All socket classes must have HTTP execute method
	 * 
	 * @access public
	 * @return string
	 */
	public function executeHttp();

	/**
	 * All socket classes must have CLI execute method
	 * 
	 * @access public
	 * @return string
	 */
	public function executeCli();
	
}