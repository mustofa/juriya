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

    /**
     * Assign a collection data
     *
     * @access  public
     * @param   mixed   Collection key
     * @param   mixed   Collection values
     * @return  void
     */
    public function addCollection($key, $value) 
    {
        // If the key are arrays build associative array, otherwise build one level array
        if (is_array($key))
        {
            switch(count($key))
            {
                case 1:

                    $this->_collections[$key[0]] = $value;

                    break;

                case 2:

                    $this->_collections[$key[0]][$key[1]] = $value;

                    break;

                case 3:

                    $this->_collections[$key[0]][$key[1]][$key[2]] = $value;

                    break;

                case 4:

                    $this->_collections[$key[0]][$key[1]][$key[2]][$key[3]] = $value;

                    break;
            }
            
        }
        else
        {
            $this->_collections[$key] = $value;
        }
    }
}