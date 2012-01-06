<?php

class HelloTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Juriya launcher instance
     */
    private $launcher;

    public function setUp()
    {
        global $launcher;

        $this->launcher = $launcher;

        unset($launcher);
    }

    public function testHello()
    {
        $_SERVER['argv'] = array('hello');

        ob_start();

        $this->launcher->execute();

        $response = ob_get_contents();

        ob_end_clean();

        $this->assertEquals('Hello World', $response);
    }
}