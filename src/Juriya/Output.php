<?php namespace Juriya;

/**
 * Juriya - RAD PHP Framework
 *
 * Output interface
 *
 * @package  Juriya
 * @category Core Class
 * @version  0.1.1
 * @author   Taufan Aditya
 */

interface Output {

	/**
	 * All Output classes must have render method
	 * 
	 * @return string
	 */
	public function render();
	
}