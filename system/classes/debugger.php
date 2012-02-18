<?php namespace Juriya;

/**
 * Juriya - RAD PHP Framework
 *
 * Debugger class
 *
 * @package  Juriya
 * @category Core Class
 * @version  0.0.1
 * @author   Taufan Aditya
 */

class Debugger {

	/**
	 * @var string Tunnel (`HTTP` or `CLI`)
	 */
	public  static $tunnel = '';

	/**
	 * @var array Foreground colour dictionary
	 */
	public  static $foregroundColors = array('black'        => '0;30',
	                                         'dark_gray'    => '1;30',
	                                         'blue'         => '0;34',
	                                         'light_blue'   => '1;34',
	                                         'green'        => '0;32',
	                                         'light_green'  => '1;32',
	                                         'cyan'         => '0;36',
	                                         'light_cyan'   => '1;36',
	                                         'red'          => '0;31',
	                                         'light_red'    => '1;31',
	                                         'purple'       => '0;35',
	                                         'light_purple' => '1;35',
	                                         'brown'        => '0;33',
	                                         'yellow'       => '1;33',
	                                         'light_gray'   => '0;37',
	                                         'white'        => '1;37');

	/**
	 * @var array Background colour dictionary
	 */
	public  static $backgroundColors = array('black'      => '40',
	                                         'red'        => '41',
	                                         'green'      => '42',
	                                         'yellow'     => '43',
	                                         'blue'       => '44',
	                                         'magenta'    => '45',
	                                         'cyan'       => '46',
	                                         'light_gray' => '47');

	/**
	 * Dump vars
	 *
	 * @return mixed  Dumped variable output
	 */
	public static function dump()
	{
		// Resolve tunnel
		self::_resolveTunnel();

		// Get all passed variables 
		$vars   = func_get_args();
		$output = array();
		
		// Itterate variables and dump into readable output / Call Tunnel
		foreach ($vars as $var) {
			$output[] = self::dumpVar($var, 1024);
		}

		// Prepare the output, then return appropriate result
		$output = implode("\n\n", $output);

		// Return the formatted dump
		if (self::$tunnel == 'CLI') {
			$line = str_repeat('#', 20);
			$fol  = $line . 'DEBUG START' . $line;
			$eol  = $line . '#DEBUG END#' . $line;

			return "\n$fol\n" . $output . "\n$eol\n";
		} else {
			return '<pre class="debug">' . $output . '</pre>';
		}
	}

	/**
	 * Dump variable
	 *
	 * @param   mixed   the variable to dump
	 * @param   int     length
	 * @param   int     depth level
	 * @return	string  Debugger output chunk
	 */
	public static function dumpVar(&$var, $length = 128, $level = 0)
	{
		// Resolve tunnel
		self::_resolveTunnel();
		$tunnel = self::$tunnel;

		$small = function($var, $tunnel) {
			if ($tunnel == 'CLI') {
				return Debugger::getColoredString($var, 'purple', 'yellow');
			} else {
				return '<small>' . $var . '</small>';
			}
		};

		$span  = function($var, $tunnel) {
			if ($tunnel == 'CLI') {
				return Debugger::getColoredString($var, NULL, 'cyan');
			} else {
				return '<span>(' . $var . ')</span>';
			}
		};

		if ($var === NULL) {
			return $small('NULL', $tunnel);
		}

		if (is_bool($var)) {
			return $small('bool ', $tunnel) . ($var ? 'TRUE' : 'FALSE');
		}
		
		if (is_float($var)) {
			return $small('float ', $tunnel) . $var;
		}

		if (is_resource($var)) {
			return $small('resource ', $tunnel) . $span($var, $tunnel);
		}

		if (is_string($var)) {
			if (strlen($var) > $length) {
				// Encode the truncated string
				if ($tunnel == 'CLI') {
					$str = substr($var, 0, $length) . ' ...';
				} else {
					$str = htmlspecialchars(substr($var, 0, $length), ENT_NOQUOTES, 'utf-8') 
					       . '&nbsp;&hellip;';
				}
			} else {
				// Encode the string
				$str = ($tunnel == 'CLI') ? $var : htmlspecialchars($var, ENT_NOQUOTES, 'utf-8');
			}

			return $small('string ', $tunnel) . $span(strlen($var), $tunnel) . ' "' . $str . '"';
		}

		if (is_array($var)) {
			// Indentation for this variable
			$output = array();
			$space  = str_repeat($s = '    ', $level);
			static $marker;

			if ($marker === NULL) {
				// Make a unique marker
				$marker = uniqid("\x00");
			}

			if (empty($var)) {
				// Do nothing
			} elseif (isset($var[$marker]) && ! empty($var)) {
				$output[] = "(\n$space$s*RECURSION*\n$space)";
			} elseif ($level < 5) {
				$output[]     = ($tunnel == 'CLI') ? "(" : "<span>(";
				$var[$marker] = TRUE;

				foreach ($var as $key => &$val) {
					if ($key === $marker) {
						continue;
					}

					if ( ! is_int($key)) {
						if ($tunnel == 'CLI') {
							$key = '"' . $key . '"';
						} else {
							$key = '"' . htmlspecialchars($key, ENT_NOQUOTES, 'utf-8') . '"';
						} 
					}

					$output[] = "$space$s$key => " . self::dumpVar($val, $length, $level + 1);
				}

				unset($var[$marker]);
				$output[] = ($tunnel == 'CLI') ? "$space)" : "$space)</span>";
			} else {
				// Depth too great
				$output[] = "(\n$space$s...\n$space)";
			}

			return $small('array', $tunnel) . $span(count($var), $tunnel) . implode("\n", $output);
		}

		if (is_object($var)) {
			// Copy the object as an array
			$array = (array) $var and $output = array();

			// Indentation for this variable
			$space = str_repeat($s = '    ', $level);
			$hash  = spl_object_hash($var);

			// Objects that are being dumped
			static $objects = array();

			if (isset($objects[$hash])) {
				$output[] = "{\n$space$s*RECURSION*\n$space}";
			} elseif ($level < 10) {
				$output[] = ($tunnel == 'CLI') ? "{" : "<code>{";
				$objects[$hash] = TRUE;

				foreach ($array as $key => &$val) {
					if ($key[0] === "\x00") {
						// Determine the access and remove the access level from the variable name
						$visibility = $key[1] === '*' ? 'protected' : 'private';
						$access     = ($tunnel == 'CLI') ? $visibility : $small($visibility, $tunnel);
						$key        = substr($key, strrpos($key, "\x00") + 1);
					} else {
						$access = ($tunnel == 'CLI') ? 'public' : $small('public', $tunnel);
					}

					$output[] = "$space$s$access $key => " 
					            . self::dumpVar($val, $length, $level + 1);
				}

				unset($objects[$hash]);
				$output[] = ($tunnel == 'CLI') ? "$space" : "$space}</code>";
			} else {
				// Depth too great
				$output[] = "{\n$space$s...\n$space}";
			}

			if ($tunnel == 'CLI') {
				return $small('object', $tunnel)
				       . ' ' . get_class($var) . '(' . count($array) . ') ' 
				       . implode("\n", $output);
			} else {
				return $small('object', $tunnel)
				       . ' <span>' . get_class($var) . '(' . count($array) . ')</span> ' 
				       . implode("\n", $output);
			}

		}
		
		if ($tunnel == 'CLI') {
			return $small(gettype($var) . ' ', $tunnel)
			       . print_r($var, TRUE);
		} else {
			return $small(gettype($var) . ' ', $tunnel)
		       . htmlspecialchars(print_r($var, TRUE), ENT_NOQUOTES, 'utf-8');
		}
	}

	/**
	 * Serve trace spec
	 *
	 * @param  string  Trace type
	 * @param  string  Trace message
	 * @param  array   Trace stack
	 * @throws SomeException
	 * @return mixed  
	 */
	public static function trace($type = '', $message = '', $stack = array())
	{
		// Resolve tunnel
		self::_resolveTunnel();
		$tunnel = self::$tunnel;

		if ($tunnel == 'CLI') {
			$output  = self::getColoredString(str_pad('TYPE ', 7, " ", STR_PAD_LEFT), 'purple', 'yellow');
			$output .= self::getColoredString($type, NULL, 'cyan') . "\n";
			$output .= self::getColoredString(str_pad('MSG ', 7, " ", STR_PAD_LEFT), 'purple', 'yellow');
			$output .= self::getColoredString($message, NULL, 'cyan') . "\n";
		} else {
			$output  = '<strong>' . str_pad('TYPE ', 7, " ", STR_PAD_LEFT) . '</strong><span>' . $type . '</span>'  . "\n";
			$output .= '<strong>' . str_pad('MSG ', 7, " ", STR_PAD_LEFT) . '</strong><span>' . $message . '</span>'  . "\n";
		}

		if ( ! empty($stack)) {
			// Begin format the stack trace
			$formatedStack = array();
			$paddedSpace   = '        ';

			foreach ($stack as $index => $item) {
				$itemNode = '';

				// Add new line for CLI
				if ($tunnel == 'CLI') {
					$itemNode .= self::getColoredString($paddedSpace . str_pad('Level ' . ($index + 1), 10, ' ', STR_PAD_LEFT),
					                                    NULL, 'cyan');
					$itemNode .= "\n";
				}

				foreach ($item as $head => $body) {
					// Parse the arguments
					if ($head == 'args') {
						$body = var_export($body, TRUE);
						$body = str_replace("\n", '', $body);
					}

					// Build the trace stack
					if ($tunnel == 'CLI') {
						$itemNode .= self::getColoredString($paddedSpace . str_pad(strtoupper($head), 10, " ", STR_PAD_LEFT), 
						                                    'red', 'light_gray'); 
	    				$itemNode .= self::getColoredString($body, NULL, 'cyan') . "\n";
					} else {
	    				$itemNode .= $paddedSpace . '<strong>' 
	    				             . str_pad(strtoupper($head), 10, " ", STR_PAD_LEFT) 
	    				             . ' </strong>';
	    				$itemNode .= '<span>' . $body . ' </span>' . "\n";
						
					}
				}
				
				$formatedStack[] = $itemNode;
			}

			// Return the stack trace based by request's tunnel method
			if ($tunnel == 'CLI') {

				$output .= self::getColoredString(str_pad('STACK ', 7, " ", STR_PAD_LEFT), 'purple', 'yellow') 
				           . self::getColoredString(count($formatedStack) . ' level(s)', NULL, 'cyan')
				           . "\n" . self::getColoredString('<STACK START>', NULL, 'cyan')
				           . "\n" . implode('' , $formatedStack)
				           . self::getColoredString('<STACK END>', NULL, 'cyan');
			} else {
	    		$output .= '<strong>' . str_pad('STACK ', 7, " ", STR_PAD_LEFT) . '</strong>' . "\n"
	    		           . implode("\n", $formatedStack);
			}
		}

		// Return the formatted dump
		if (self::$tunnel == 'CLI') {
			return "\n" . $output . "\n";
		} else {
			return '<pre class="debug">' . $output . '</pre>';
		}
	}

	/**
	 * Returns colored string
	 *
	 * @param  string  The string to output
	 * @param  string  Foreground color
	 * @param  string  background color
	 * @return string  Colored string
	 */
	public static function getColoredString($string, $foregroundColor = NULL, $backgroundColor = NULL) {
		$colored_string = "";

		// Check if given foreground color found
		if (isset(self::$foregroundColors[$foregroundColor])) {
		    $colored_string .= "\033[" . self::$foregroundColors[$foregroundColor] . "m";
		}

		// Check if given background color found
		if (isset(self::$backgroundColors[$backgroundColor])) {
		    $colored_string .= "\033[" . self::$backgroundColors[$backgroundColor] . "m";
		}

		// Add string and end coloring
		$colored_string .=  $string . "\033[0m";

		return $colored_string;
	}

	/**
	 * Returns all foreground color names
	 *
	 * @return array  Array of foreground color
	 */
	public static function getForegroundColors() {
		return array_keys(self::$foregroundColors);
	}

	/**
	 * Returns all background color names
	 * 
	 * @return array  Array of background color
	 */
	public static function getBackgroundColors() {
		return array_keys(self::$backgroundColors);
	}

	/**
	 * Resolve tunnel
	 *
	 * @param  sometype  Explanation
	 * @param  sometype  Explanation
	 * @param  sometype  Explanation
	 * @throws SomeException
	 * @return void  
	 */
	private static function _resolveTunnel()
	{
		if ( ! empty(self::$tunnel)) {
			return;
		} else {
			// Detect environment
			defined('STDIN') and self::$tunnel = 'CLI';
		}
	}
}