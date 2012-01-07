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
	public function execute()
	{
		return NULL;
	}

	/**
	 * Empty response for HTTP call
	 * 
	 * @return string
	 */
	public function executeHttp()
	{
		$response = Juriya::factory('Response\Http');
		$response->code(204);
		$response->header(array('Content-Type' => 'text/html; charset=utf-8'));
		$response->content('');

		return $response;
	}

	/**
	 * Empty response for CLI call
	 * 
	 * @return string
	 */
	public function executeCli()
	{
		$response = Juriya::factory('Response\Cli');
		$response->type('OUT');
		$response->content('');

		return $response;
	}
	
}