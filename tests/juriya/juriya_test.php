<?php

class Juriya_Test extends PHPUnit_Framework_TestCase {

	public function test_invalid_init()
	{
		$config = new Mock_Collection();

		$this->setExpectedException('RuntimeException', 'Cannot start Juriya proccess without proper configuration');
		$juriya = new Mock_Juriya(array(), $config);
	}

	public function test_valid_init()
	{
		// Mock configuration
		$configs = array(
			'MODULES' => array(
				'foo.namespace' => 'Mod\Foo',
				'foo.path' => PATH_MOD.'foo'.DIRECTORY_SEPARATOR,
			),
			'ROUTES' => array(
				'default.controller' => 'Hello',
				'default.arguments.0' => 'hello_world',
				'default.arguments.1' => '/^[a-zA-Z0-9_:@\-\s]+$/',
			)
		);

		$config  = new Mock_Collection();
		$config->set('configuration', $configs);

		$juriya = new Mock_Juriya(array(), $config);

		$this->assertInstanceOf('Juriya\\Juriya', $juriya);
	}

}