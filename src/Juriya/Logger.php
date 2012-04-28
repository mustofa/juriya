<?php namespace Juriya;

/**
 * Juriya - RAD PHP Framework
 *
 * Logger class
 *
 * @package  Juriya
 * @category Core Class
 * @version  0.1.1
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
	private static $init;

	/**
	 * Report all logger data with appropriate output
	 *
	 * @param   mixed
	 * @return  mixed
	 */
	public static function report()
	{
		$logs       = array();
		$profiler   = self::$profiler;
		$log        = self::$log;
		
		foreach ($log as $header => $content) {
			// Initial empty message
			$message = '';

			if ($content->isEmpty()) {
				// Do nothing
			} else {
				// Iterate over all content and place as lines
				foreach ($content as $line) {
					$timestamp = key($line);
					$message  .= $timestamp . '-' . $line[$timestamp] . '(' . $header . ')' . "\n";
				}
				
				$logs[] = $message;
			}
		}

		// Just log if there are something to report
		if ( ! empty($logs)) {
			$path   = PATH_IDX . 'log' . DIRECTORY_SEPARATOR . date('Y-m-d') . '.txt';
			$report = implode("\n", $logs);
			File::append($path, $report);
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
		$identifiers  = array_fill(0, 2, $identifier);

		array_map(function($pointer, $value, $identifier) {
			$start   = Logger::$profiler[$identifier][$pointer . '_start'];
			$elapsed = $value - $start;
			Logger::$profiler[$identifier]->set($pointer . '_end', $value);
			Logger::$profiler[$identifier]->set($pointer . '_elapsed', $elapsed);
		}, $pointers, $values, $identifiers);

		unset($pointers, $values, $identifiers);
	}

	/**
	 * Checking logger state
	 *
	 * @param   string
	 * @return  void
	 */
	public static function check($identifier)
	{
		if (self::$init == FALSE or ! isset(self::$profiler[$identifier])) {
			self::$init = TRUE;
			self::$profiler[$identifier] = new Collection()
			and self::$log[$identifier]  = new Collection();

			return;
		}
	}
}