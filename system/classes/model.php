<?php namespace Juriya;

/**
 * Juriya - RAD PHP Framework
 *
 * Base Model
 *
 * @package  Juriya
 * @category Core Class
 * @version  0.0.1
 * @author   Taufan Aditya
 */

class Model {

	/**
	 * @var object Database instance
	 */
	public static $db;

	function __construct($db)
	{
		self::$db = $db;
	}

	public function __get($name)
	{
		if ($name == 'db') return self::$db;
	}

	public function __call($name, $arguments)
	{
		$methodVariable = array(self::$db, $name);

		if (is_callable($methodVariable, TRUE)) {
			call_user_func_array($methodVariable, $arguments);
		}
	}

	public static function __callStatic($name, $arguments)
	{
		$methodVariable = array(self::$db, $name);

		if (is_callable($methodVariable, TRUE)) {
			call_user_func_array($methodVariable, $arguments);
		}
	}

}