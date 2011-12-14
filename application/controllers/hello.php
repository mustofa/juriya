<?php namespace application\controllers;

class Hello implements \Controller{
	
	public function executeHTTP()
	{
		echo 'Hello World';
	}

	public function executeCLI()
	{
		echo 'Hello World';
	}

}