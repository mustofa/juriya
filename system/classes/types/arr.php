<?php namespace system\classes\types;

/**
 * Juriya - RAD PHP 5 Micro Framework
 *
 * Array Data Types
 *
 * @package  Juriya
 * @category Core Class
 * @version  0.0.1
 * @author   Taufan Aditya
 */

class Arr implements \ArrayAccess{

    /**
     * @var array Data collection
     */
	private $_collections = array();

    /**
     * Handle array isset
     *
     * @access  public
     * @return  bool
     */
    public function offsetExists($offset) 
    {
        return isset($this->_collections[$offset]);
    }

    /**
     * Handle array getter
     *
     * @access  public
     * @return  mixed
     */
    public function offsetGet($offset) 
    {
        return isset($this->_collections[$offset]) ? $this->_collections[$offset] : FALSE;
    }

    /**
     * Handle array setter
     *
     * @access  public
     * @return  void
     */
    public function offsetSet($offset, $value) 
    {
        if (is_null($offset)) 
        {
            $this->_collections[] = $value;
        } 
        else
        {
            $this->_collections[$offset] = $value;
        }
    }
    
    /**
     * Handle array unsetter
     *
     * @access  public
     * @return  void
     */
    public function offsetUnset($offset) 
    {
        unset($this->_collections[$offset]);
    }
}