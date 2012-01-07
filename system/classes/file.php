<?php namespace Juriya;

/**
 * Juriya - RAD PHP Framework
 *
 * File class
 *
 * @package  Juriya
 * @category Core Class
 * @version  0.0.1
 * @author   Taufan Aditya
 */

class File {

	/**
	 * Read the contents of a file.
	 *
	 * @param  string  File path
	 * @return string
	 */
	public static function read($path)
	{
		$content = '';

		if (file_exists($path) and $handle = fopen($path, 'r')) {
	        $content = fread($handle, filesize($path));
		    fclose($handle);
		}

		return $content;
	}

	/**
	 * Get the contents of a file as lines.
	 *
	 * @param  string  File path
	 * @return array
	 */
	public static function getLines($path)
	{
		$lines = array();

		if (file_exists($path) and $handle = fopen($path, 'r')) {

	        while (($line = fgets($handle, 4096)) !== false) {
	        	$lines[] = $line;
		    }

		    if ( ! feof($handle)) {
		    	return $lines;
		    }

		    fclose($handle);
		}

	    return $lines;
	}

	/**
	 * Write to a file.
	 *
	 * @param  string    File path
	 * @param  string    Content
	 * @throws exception Permission denied or not writable
	 * @return int
	 */
	public static function write($path, $data)
	{
		if ( ! $handle = fopen($path, 'w+')) {
	         throw new \UnexpectedValueException($path . ' is not writable file');
	    }

	    fwrite($handle, $data);
	    fclose($handle);
	    chmod($path, 0755);

		return;
	}

	/**
	 * Append to a file.
	 *
	 * @param  string    File path
	 * @param  string    content
	 * @throws exception Permission denied or not writable
	 * @return int
	 */
	public static function append($path, $data)
	{
		if ( ! $handle = fopen($path, 'a+')) {
	         throw new \UnexpectedValueException($path . ' is not writable file');
	    }

	    fwrite($handle, $data);
	    fclose($handle);
	    chmod($path, 0755);

		return;
	}

	/**
	 * Delete a file.
	 *
	 * @param  string  File path
	 * @return void
	 */
	public static function delete($path)
	{
		if (file_exists($path)) @unlink($path);
	}

	/**
	 * Extract the file extension from a file path.
	 * 
	 * @param  string  File path
	 * @return string
	 */
	public static function extension($path)
	{
		return pathinfo($path, PATHINFO_EXTENSION);
	}
}