<?php namespace Juriya;

/**
 * Juriya - RAD PHP 5 Micro Framework
 *
 * Data interface
 *
 * @package  Juriya
 * @category Core Class
 * @version  0.0.1
 * @author   Taufan Aditya
 */

interface Data {

	/**
	 * All Data classes must have set method
	 * 
	 * @return void
	 */
	public function set($key, $value);

	/**
	 * All Data classes must have get method
	 * 
	 * @return mixed
	 */
	public function get($path, $default);
	
}