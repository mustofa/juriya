<?php namespace Juriya;

/**
 * Juriya - RAD PHP 5 Micro Framework
 *
 * Collection class
 *
 * @package  Juriya
 * @category Core Class
 * @version  0.0.1
 * @author   Taufan Aditya
 */

class Collection implements \ArrayAccess, \Iterator, \Countable, Data {

    /**
     * @var array Data collection
     */
	private $collections = array();

    function __construct($collections = array())
    {
        $this->collections = $collections;
    }

    /**
     * Handle array isset
     *
     * @return  bool
     */
    public function offsetExists($offset) 
    {
        return isset($this->collections[$offset]);
    }

    /**
     * Handle array getter
     *
     * @return  mixed
     */
    public function offsetGet($offset) 
    {
        return isset($this->collections[$offset]) ? $this->collections[$offset] : FALSE;
    }

    /**
     * Handle array setter
     *
     * @return  void
     */
    public function offsetSet($offset, $value) 
    {
        if (is_null($offset)) {
            $this->collections[] = $value;
        } else {
            $this->collections[$offset] = $value;
        }
    }
    
    /**
     * Handle array unsetter
     *
     * @return  void
     */
    public function offsetUnset($offset) 
    {
        unset($this->collections[$offset]);
    }

     /**
     * Handle rewind
     *
     * @return  void
     */
    public function rewind() 
    {
        reset($this->collections);
    }

    /**
     * Handle current
     *
     * @return  void
     */
    public function current() 
    {
        return current($this->collections);
    }

    /**
     * Handle key
     *
     * @return  mixed
     */
    public function key() 
    {
        return key($this->collections);
    }

    /**
     * Handle next
     *
     * @return  mixed
     */
    public function next() 
    {
        return next($this->collections);
    }

    /**
     * Handle last
     *
     * @return  mixed
     */
    public function last() 
    {
        end($this->collections) and $last_index = key($this->collections);
        $collections = $this->collections[$last_index];
        reset($this->collections);

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
        return count($this->collections);
    }

    /**
     * Sorting collection Ascending
     *
     * @return  void
     */
    public function ksortAsc() 
    {
        ksort($this->collections);
    }

    /**
     * Sorting collection Descending
     *
     * @return  void
     */
    public function ksortDesc() 
    {
        krsort($this->collections);
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
        $array = $this->collections;

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
                    $this->collections[$key[0]] = $value;

                    break;

                case 2:
                    $this->collections[$key[0]][$key[1]] = $value;

                    break;

                case 3:
                    $this->collections[$key[0]][$key[1]][$key[2]] = $value;

                    break;

                case 4:
                    $this->collections[$key[0]][$key[1]][$key[2]][$key[3]] = $value;

                    break;
            }
        } else {
            $this->collections[$key] = $value;
        }
    }
}