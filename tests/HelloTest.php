<?php

/**
 * Juriya - RAD PHP Framework
 *
 * Test case for Hello controller
 *
 * @package  Juriya
 * @category Unit Test
 * @version  0.0.1
 * @author   Taufan Aditya
 */

class HelloTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Juriya launcher instance
     */
    private $launcher;

    public function setUp()
    {
        // Grab the launcher which instantiated by bootstrap
        global $launcher;

        $this->launcher = $launcher;

        unset($launcher);
    }

    public function testHello()
    {
        // Mimic a request which sent 'hello'
        $_SERVER['argv'] = array('hello');

        // Get the response
        $response = $this->launcher->execute();

        // See corresponding controller method which serve this call : 
        // File : ./application/classes/controllers/hello.php
        // Line : 36
        $this->assertEquals('Hello World', $response);
    }

    public function testHelloRoute()
    {
        // Mimic a request which sent 'hello_world/juriya'
        // See corresponding route config which match with this call : 
        // File : ./application/config/routes.conf.ini
        $_SERVER['argv'] = array('hello_world', 'juriya');

        // Get the response
        $response = $this->launcher->execute();

        // See corresponding controller method which serve this call : 
        // File : ./application/classes/controllers/hello.php
        // Line : 36
        $this->assertStringMatchesFormat('%s', $response[1]);
        $this->assertEquals('hello_world from juriya', implode(' from ', $response));
    }
}