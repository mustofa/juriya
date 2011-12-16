<?php namespace Juriya;

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
	 * @var array Human-readable error levels and descriptions.
	 */
	private $levels = array(
		0                  => 'Error',
		E_ERROR            => 'Error',
		E_WARNING          => 'Warning',
		E_PARSE            => 'Parsing Error',
		E_NOTICE           => 'Notice',
		E_CORE_ERROR       => 'Core Error',
		E_CORE_WARNING     => 'Core Warning',
		E_COMPILE_ERROR    => 'Compile Error',
		E_COMPILE_WARNING  => 'Compile Warning',
		E_USER_ERROR       => 'User Error',
		E_USER_WARNING     => 'User Warning',
		E_USER_NOTICE      => 'User Notice',
		E_STRICT           => 'Runtime Notice'
	);

	/**
	 * Constructor
	 *
	 * @param  object
	 * @return void
	 */
	function __construct($e)
	{
		// Set appropriate properties
		if (is_array($e)) {
			$this->error     = $e;
		} else {
			$this->exception = $e;
		}
	}

	/**
	 * Accepting the exception/error and create new instance
	 *
	 * @param  object
	 * @return object
	 */
	public static function accept($e)
	{
		return new static($e);
	}

	/**
	 * Error handler
	 *
	 * @return string Error description
	 */
	public function handleError()
	{
		if ( ! (error_reporting() & $this->error[0])) {
	        // This error code is not included in error_reporting
	        return;
	    }

	    $header  = $this->severity($this->error[0]);
		$message = $this->message($this->error[1],
		                          $this->error[2],
		                          $this->error[3]);

	    switch ($this->error[0]) {
		    case E_USER_ERROR:
		        Logger::write('Juriya\\Juriya', $message, 3);
				echo Juriya::debug($header, $message);
		        exit(1);
		        break;

		    default:
				Logger::write('Juriya\\Juriya', $message, 2);
				echo Juriya::debug($header, $message);
		        break;
	    }

    	// Don't execute PHP internal error handler
    	return true;
	}

	/**
	 * Exception handler
	 *
	 * @return string Exception description
	 */
	public function handleException()
	{
		$severity = $this->severity($this->exception->getCode());
		$message  = $this->message($this->exception->getMessage(),
		                          $this->exception->getFile(),
		                          $this->exception->getLine());
		Logger::write('Juriya\\Juriya', $message, 3);
		echo Juriya::debug($severity, $message);
	}

	/**
	 * Get a human-readable version of the exception error code.
	 *
	 * @return string
	 */
	public function severity($code)
	{
		// Output human readable exception code level
		if (array_key_exists($code, $this->levels)) {
			return $this->levels[$code];
		}

		return $code;
	}

	/**
	 * Get the exception/error message formatted.
	 *
	 * @param  string Message string
	 * @param  string File pointer
	 * @param  string Line pointer
	 * @return string Formatted message
	 */
	public function message($message, $file, $line)
	{
		// Show fake path and corresponding error line
		$realpath = array(PATH_APP, PATH_MOD, PATH_SYS);
		$fakepath = array('PATH_APP' . DIRECTORY_SEPARATOR, 
                          'PATH_MOD' . DIRECTORY_SEPARATOR, 
                          'PATH_SYS' . DIRECTORY_SEPARATOR);
		$file     = str_replace($realpath, $fakepath, $file);

		// Filtering non-useful fraction and send the message
		if (preg_match('/\[([^\n]+):/', $message, $matches) 
		    and count($matches) == 2) {
			$message = str_replace($matches[0], ':', $message);
		}

		return rtrim($message, '.')
				. ' in ' . $file . ' on line ' . $line . '.';
	}
}