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
	 * @access  public
	 * @param   string
	 * @return  mixed
	 */
	public static function report($identifier)
	{
		self::$profiler[$identifier]->ksortDesc();

		echo Juriya::debug(self::$profiler[$identifier]);
	}

	/**
	 * Start the profiling
	 *
	 * @access  public
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
	 * @access  public
	 * @param   string
	 * @return  void
	 */
	public static function stop($identifier)
	{
		self::check($identifier);

		$end = self::$profiler[$identifier]['time_end'] = microtime(TRUE);

		self::$profiler[$identifier]['time_elapsed'] = $end - self::$profiler[$identifier]['time_start'];

		self::$profiler[$identifier]['memory_end'] = memory_get_usage(TRUE);
	}

	/**
	 * Checking logger state
	 *
	 * @access  public
	 * @param   string
	 * @return  void
	 */
	public static function check($identifier)
	{
		if (self::$_init == FALSE)
		{
			self::$_init = TRUE 

			and self::$log[$identifier] = self::$profiler[$identifier] = new Data();
		}
	}
}