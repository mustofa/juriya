<?php namespace Juriya;

/**
 * Juriya - RAD PHP Framework
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
	 * All socket classes must have execute method
	 * 
	 * @return mixed  
	 */
	public function execute();

	/**
	 * All socket classes must have HTTP execute method
	 * 
	 * @return response HTTP response
	 */
	public function executeHttp();

	/**
	 * All socket classes must have CLI execute method
	 * 
	 * @return response CLI Response
	 */
	public function executeCli();
	
}