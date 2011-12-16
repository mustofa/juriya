<?php namespace Juriya;

/**
 * Juriya - RAD PHP 5 Micro Framework
 *
 * Data interface
 *
 * @package  Juriya
 * @category Core Class
 * @version  0.0.1
 * @author   Taufan Aditya
 */

class Data implements \ArrayAccess, \Iterator, \Countable {

    /**
     * @var array Data collection
     */
	private $_collections = array();

    function __construct($collections = array())
    {
        $this->_collections = $collections;
    }

    /**
     * Handle array isset
     *
     * @return  bool
     */
    public function offsetExists($offset) 
    {
        return isset($this->_collections[$offset]);
    }

    /**
     * Handle array getter
     *
     * @return  mixed
     */
    public function offsetGet($offset) 
    {
        return isset($this->_collections[$offset]) ? $this->_collections[$offset] : FALSE;
    }

    /**
     * Handle array setter
     *
     * @return  void
     */
    public function offsetSet($offset, $value) 
    {
        if (is_null($offset)) {
            $this->_collections[] = $value;
        } else {
            $this->_collections[$offset] = $value;
        }
    }
    
    /**
     * Handle array unsetter
     *
     * @return  void
     */
    public function offsetUnset($offset) 
    {
        unset($this->_collections[$offset]);
    }

     /**
     * Handle rewind
     *
     * @return  void
     */
    public function rewind() 
    {
        reset($this->_collections);
    }

    /**
     * Handle current
     *
     * @return  void
     */
    public function current() 
    {
        return current($this->_collections);
    }

    /**
     * Handle key
     *
     * @return  mixed
     */
    public function key() 
    {
        return key($this->_collections);
    }

    /**
     * Handle next
     *
     * @return  mixed
     */
    public function next() 
    {
        return next($this->_collections);
    }

    /**
     * Handle last
     *
     * @return  mixed
     */
    public function last() 
    {
        end($this->_collections) and $last_index = key($this->_collections);
        $collections = $this->_collections[$last_index];
        reset($this->_collections);

        return $collections;
    }

    /**
     * Handle valid
     *
     * @return  bool
     */
    public function valid() 
    {
        return $this->current() !== false;
    }    

    /**
     * Handle counter
     *
     * @return  int
     */
    public function count() 
    {
        return count($this->_collections);
    }

    /**
     * Sorting collection Ascending
     *
     * @return  void
     */
    public function ksortAsc() 
    {
        ksort($this->_collections);
    }

    /**
     * Sorting collection Descending
     *
     * @return  void
     */
    public function ksortDesc() 
    {
        krsort($this->_collections);
    }

    /**
     * Collection getter
     *
     * @param   string
     * @param   mixed
     * @return  int
     */
    public function get($path = null, $default = FALSE) 
    {
        // Create new array for processing
        $array = $this->_collections;

        if (is_null($path)) {
            return $array;
        }

        // Remove outer dots, wildcards, or spaces and split the keys
        $path = trim($path, '.* ');
        $keys = explode('.', $path);

        do {
            $key = array_shift($keys);

            if (ctype_digit($key)) {
                // Make the key an integer
                $key = (int) $key;
            }

            if (isset($array[$key])) {
                if ($keys) {
                    if (is_array($array[$key])) {
                        // Dig down into the next part of the path
                        $array = $array[$key];
                    } else {
                        // Unable to dig deeper
                        break;
                    }
                } else {
                    // Found the path requested
                    return $array[$key];
                }
            } elseif ($key === '*') {
                // Handle wildcards
                if (empty($keys)) {
                    return $array;
                }

                $values = array();

                foreach ($array as $arr) {
                    if ($value = self::get($arr, implode('.', $keys))) {
                        $values[] = $value;
                    }
                }

                if ($values) {
                    // Found the values requested
                    return $values;
                } else {
                    // Unable to dig deeper
                    break;
                }
            } else {
                // Unable to dig deeper
                break;
            }
        } while ($keys);

        // Unable to find the value requested
        return $default;
    }

    /**
     * Set/assign a collection data
     *
     * @param   mixed   Collection key
     * @param   mixed   Collection values
     * @return  void
     */
    public function set($key, $value) 
    {
        // If the key are arrays build associative array, otherwise build one level array
        if (is_array($key)) {
            switch(count($key)) {
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
        } else {
            $this->_collections[$key] = $value;
        }
    }
}