<?php namespace Juriya;

/**
 * Juriya - RAD PHP Framework
 *
 * CLI class
 *
 * @package  Juriya
 * @category Core Class
 * @version  0.1.1
 * @author   Taufan Aditya
 */

use \Juriya\Output;

class Response_Cli implements Output {

    /**
     * @var string Hold CLI output type
     */
    protected $type;

    /**
     * @var string Hold CLI content/stream
     */
    protected $content;

    /**
     * Constructor
     *
     * @param   string  CLI output type
     * @param   string  CLI content
     * @return  void
     */
    function __construct($type = 'OUT', $content = '')
    {
    	$this->type    = $type;
    	$this->content = $content;
    }

    /**
     * Render the response to command-line
     *
     * @return  void
     */
    public function render()
    {
    	if ($this->type == 'OUT') {
            fwrite(STDOUT, $this->content);   
        } else {
            fwrite(STDERR, $this->content);   
        }
    }

    /**
     * Translate the CLI output type 
     *
     * Prototype :
     *  'OUT' => STDOUT
     *  'ERR' => STDERR
     *
     * @param   string  STD type 
     * @return  object  Translated response
     */
    public function type($std)
    {
    	$this->type = (in_array($std, array('OUT', 'ERR'))) ? $std : 'ERR';

    	return $this;
    }

    /**
     * Set CLI content/stream
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