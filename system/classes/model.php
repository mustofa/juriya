<?php namespace Juriya;

/**
 * Juriya - RAD PHP 5 Micro Framework
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
	public $db;

	function __construct()
	{
		// Include NotORM packages, configure DB param and instantiate new DB
		include_once PATH_PKG . 'notorm' . DIRECTORY_SEPARATOR . 'NotORM' . EXT;
		$pdo = new \PDO('mysql:host=localhost;dbname=juriya',
	                    'juriya',
	                    'juriya');

		$this->db  = new \NotORM($pdo);
	}

}