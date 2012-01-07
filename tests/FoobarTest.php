<?php

/**
 * Juriya - RAD PHP Framework
 *
 * Test case for Foo\Bar controller (Dummy Module)
 *
 * @package  Juriya
 * @category Unit Test
 * @version  0.0.1
 * @author   Taufan Aditya
 */

class FoobarTest extends PHPUnit_Framework_TestCase
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

    public function testFoobar()
    {
        // Mimic a request which sent 'hello'
        $_SERVER['argv'] = array('foo', 'bar');

        // Get the response
        $response = $this->launcher->execute();

        // See corresponding controller method which serve this call : 
        // File : ./application/classes/controllers/hello.php
        // Line : 36
        $this->assertEquals('Foo Bar', $response);
    }
}