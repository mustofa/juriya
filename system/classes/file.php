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
	 * Get the contents of a file.
	 *
	 * @param  string  File path
	 * @return string
	 */
	public static function get($path)
	{
		if (file_exists($path)) {
			return file_get_contents($path);
		}

		return FALSE;
	}

	/**
	 * Write to a file.
	 *
	 * @param  string  File path
	 * @param  string  Content
	 * @return int
	 */
	public static function set($path, $data)
	{
		return file_put_contents($path, $data, LOCK_EX);
	}

	/**
	 * Append to a file.
	 *
	 * @param  string  File path
	 * @param  string  content
	 * @return int
	 */
	public static function append($path, $data)
	{
		return file_put_contents($path, $data, LOCK_EX | FILE_APPEND);
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