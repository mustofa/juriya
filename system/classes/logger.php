<?php namespace Juriya;

/**
 * Juriya - RAD PHP 5 Micro Framework
 *
 * Logger class
 *
 * @package  Juriya
 * @category Core Class
 * @version  0.0.1
 * @author   Taufan Aditya
 */

class Logger {

	/**
	 * @var array Logger code
	 */
	public static $code = array( 1 => 'INFO',
	                             2 => 'WARNING',
	                             3 => 'ERROR' );

	/**
	 * @var array Profiler log
	 */
	public static $profiler;

	/**
	 * @var array Log
	 */
	public static $log;

	/**
	 * @var bool Logger status
	 */
	private static $_init;

	/**
	 * Report all logger data with appropriate output
	 *
	 * @param   mixed
	 * @return  mixed
	 */
	public static function report($identifier = NULL)
	{
		if (is_null($identifier)) {
			echo Juriya::debug(self::$profiler, self::$log);
		} else {
			self::$profiler[$identifier]->ksortDesc();
			$profiler   = self::$profiler[$identifier];
			$log        = self::$log[$identifier];
			echo Juriya::debug($profile, $log);
		}
	}

	/**
	 * Write a log
	 *
	 * @param   string
	 * @return  void
	 */
	public static function write($identifier, $msg = 'Empty message.', $code = 0)
	{
		self::check($identifier);
		$key  = date('H:i:s', time());
		$code = isset(self::$code[$code]) ? self::$code[$code] : 'UNKNOWN';
		$log  = '[' . str_pad($code, 7, ' ', STR_PAD_BOTH) . '] ' . $msg;
		self::$log[$identifier][] = array($key => $log);
	}

	/**
	 * Start the profiling
	 *
	 * @param   string
	 * @return  void
	 */
	public static function start($identifier)
	{
		self::check($identifier);
		self::$profiler[$identifier]['time_start'] = microtime(TRUE);
		self::$profiler[$identifier]['memory_start'] = memory_get_usage(TRUE);
	}

	/**
	 * Stop the profiling
	 *
	 * @param   string
	 * @return  void
	 */
	public static function stop($identifier)
	{
		self::check($identifier);
		$pointers     = array('time', 'memory');
		$values       = array(microtime(TRUE), memory_get_usage(TRUE));
		Juriya::$temp = $identifier;

		array_map(function($pointer, $value) {
			$start   = Logger::$profiler[Juriya::$temp][$pointer . '_start'];
			$elapsed = $value - $start;
			Logger::$profiler[Juriya::$temp]->add($pointer . '_end', $value);
			Logger::$profiler[Juriya::$temp]->add($pointer . '_elapsed', $elapsed);
		}, $pointers, $values);
	}

	/**
	 * Checking logger state
	 *
	 * @param   string
	 * @return  void
	 */
	public static function check($identifier)
	{
		if (self::$_init == FALSE or is_null(self::$profiler[$identifier])) {
			self::$_init = TRUE and self::$profiler[$identifier] = new Data()
			and self::$log[$identifier] = new Data();
			return;
		}
	}
}