<?php namespace system\classes;

/**
 * Juriya - RAD PHP 5 Micro Framework
 *
 * Exception class
 *
 * @package  Juriya
 * @category Core Class
 * @version  0.0.1
 * @author   Taufan Aditya
 */

class Exception {

	/**
	 * @var object Exception instance
	 */
	protected $exception;

	/**
	 * Constructor
	 *
	 * @param  object
	 * @return void
	 */
	function __construct($exception)
	{
		$this->exception = $exception;
	}

	/**
	 * Exception handler
	 *
	 * @return string Error description
	 */
	public function handle()
	{
		echo Juriya::debug($this->exception);
	}
}