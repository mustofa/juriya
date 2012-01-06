<?php namespace Juriya;

/**
 * Juriya - RAD PHP Framework
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
	 * @var object Corresponding model instance
	 */
	public $model;

	/**
	 * @var object Corresponding view instance
	 */
	public $view;

	/**
	 * Empty response
	 * 
	 * @return string
	 */
	public function executeHttp()
	{
		echo 'Empty Response';
	}

	/**
	 * Empty response
	 * 
	 * @return string
	 */
	public function executeCli()
	{
		echo 'Empty Response';
	}
	
}