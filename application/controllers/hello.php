<?php namespace application\controllers;

class Hello extends \Controller{
	
	public function executeHTTP()
	{
		echo 'Hello World';
	}

	public function executeCLI()
	{
		echo 'Hello World';
	}

}