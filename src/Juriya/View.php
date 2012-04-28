<?php namespace Juriya;

/**
 * Juriya - RAD PHP Framework
 *
 * Base View
 *
 * @package  Juriya
 * @category Core Class
 * @version  0.1.1
 * @author   Taufan Aditya
 */

class View {

	/**
	 * @var object Parser instance
	 */
	public static $parser;

	function __construct($parser)
	{
		self::$parser = $parser;
	}

	public function __get($name)
	{
		if ($name == 'parser') return self::$parser;
	}

	public function __call($name, $arguments)
	{
		$methodVariable = array(self::$parser, $name);

		if (is_callable($methodVariable, TRUE)) {
			call_user_func_array($methodVariable, $arguments);
		}
	}

	public static function __callStatic($name, $arguments)
	{
		$methodVariable = array(self::$parser, $name);

		if (is_callable($methodVariable, TRUE)) {
			call_user_func_array($methodVariable, $arguments);
		}
	}

}