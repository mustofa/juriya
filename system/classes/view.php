<?php namespace Juriya;

/**
 * Juriya - RAD PHP 5 Micro Framework
 *
 * Base View
 *
 * @package  Juriya
 * @category Core Class
 * @version  0.0.1
 * @author   Taufan Aditya
 */

class View {

	function __construct()
	{
		// Include dwoo Autoloader
		include_once PATH_PKG . 'dwoo' . DIRECTORY_SEPARATOR . 'dwooAutoload' . EXT;
	}

	public function output($tpl = NULL, $data = NULL, $compiler = NULL)
	{
		// Instantiate new Dwoo as template parser, and process the template params
		$parser = new \Dwoo();

		if ( ! empty($tpl) && ! empty($data)) {
			return $parser->output($tpl, $data);
		} elseif ( ! empty($tpl) && ! empty($data) && ! empty($compiler)) {
			return $parser->output($tpl, $data, $compiler);
		}
		
	}

}