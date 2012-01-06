<?php namespace Juriya\Response;

/**
 * Juriya - RAD PHP Framework
 *
 * HTTP class
 *
 * @package  Juriya
 * @category Core Class
 * @version  0.0.1
 * @author   Taufan Aditya
 */

use \Juriya\Output;
use \Juriya\Collection;

class Http implements Output {

    /**
     * @var array Hold HTTP headers statement
     */
	protected $headers;

    /**
     * @var string Hold HTTP content/stream
     */
	protected $content;

     /**
     * @var array HTTP Code Dictionary
     */
	protected static $codes = array(100 => 'Continue',
                                    101 => 'Switching Protocols',
                                    200 => 'OK',
                                    201 => 'Created',
                                    202 => 'Accepted',
                                    203 => 'Non-Authoritative Information',
                                    204 => 'No Content',
                                    205 => 'Reset Content',
                                    206 => 'Partial Content',
                                    207 => 'Multi-Status',
                                    300 => 'Multiple Choices',
                                    301 => 'Moved Permanently',
                                    302 => 'Found',
                                    303 => 'See Other',
                                    304 => 'Not Modified',
                                    305 => 'Use Proxy',
                                    307 => 'Temporary Redirect',
                                    400 => 'Bad Request',
                                    401 => 'Unauthorized',
                                    402 => 'Payment Required',
                                    403 => 'Forbidden',
                                    404 => 'Not Found',
                                    405 => 'Method Not Allowed',
                                    406 => 'Not Acceptable',
                                    407 => 'Proxy Authentication Required',
                                    408 => 'Request Timeout',
                                    409 => 'Conflict',
                                    410 => 'Gone',
                                    411 => 'Length Required',
                                    412 => 'Precondition Failed',
                                    413 => 'Request Entity Too Large',
                                    414 => 'Request-URI Too Long',
                                    415 => 'Unsupported Media Type',
                                    416 => 'Requested Range Not Satisfiable',
                                    417 => 'Expectation Failed',
                                    422 => 'Unprocessable Entity',
                                    423 => 'Locked',
                                    424 => 'Failed Dependency',
                                    500 => 'Internal Server Error',
                                    501 => 'Not Implemented',
                                    502 => 'Bad Gateway',
                                    503 => 'Service Unavailable',
                                    504 => 'Gateway Timeout',
                                    505 => 'HTTP Version Not Supported',
                                    507 => 'Insufficient Storage',
                                    509 => 'Bandwidth Limit Exceeded');

    /**
	 * Constructor
	 *
     * @param   int     HTTP code
     * @param   string  HTTP content
	 * @return  void
	 */
	function __construct($code = 0, $content = '')
	{
		$this->headers = new Collection;
		$this->content = $content;

        empty($code) or $this->code($code);
	}

    /**
     * Render the response to browser
     *
     * @return  void
     */
    public function render()
    {
    	foreach ($this->headers as $header) {
    		header($header);
    	}

    	exit($this->content);
    }

    /**
     * Translate the HTTP code
     *
     * @param   int     HTTP code
     * @return  object  Translated response
     */
    public function code($number = 0)
    {
    	if ( ! array_key_exists($number, self::$codes)) {
    		$number  = 500;
    	}

		$message = self::$codes[$number];
    	$this->headers->set('Code', 'HTTP/1.0 ' . $number . ' ' . $message);

    	return $this;
    }

    /**
     * Append HTTP headers
     *
     * @param   array   HTTP headers
     * @return  object  Appended response
     */
    public function header(Array $headers)
    {
    	foreach ($headers as $headerType => $headerValue) {
    		$this->headers->set($headerType, $headerType . ': ' . $headerValue);
    	}

    	return $this;
    }

    /**
     * Set HTTP content/stream
     *
     * @param   string  The stream
     * @return  object  
     */
    public function content($output = '')
    {
    	$this->content = $output;

    	return $this;
    }
}