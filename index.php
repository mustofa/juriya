<?php

/**
 * Juriya - RAD PHP 5 Micro Framework
 *
 * @package  Juriya
 * @version  0.0.1
 * @author   Taufan Aditya
 */

/**
 *---------------------------------------------------------------
 * Environment
 *---------------------------------------------------------------
 * Prototype :
 * 
 * Values        | Description    
 * --------------|-----------------------------------------------
 * `development` | Development environment
 * `production`  | Production environment
 */
$environment = 'development';

/**
 *---------------------------------------------------------------
 * Path to the application directory.
 *---------------------------------------------------------------
 */
$application = 'application';

/**
 *---------------------------------------------------------------
 * Path to the modules directory.
 *---------------------------------------------------------------
 */
$modules = 'modules';

/**
 *---------------------------------------------------------------
 * Path to the system directory.
 *---------------------------------------------------------------
 */
$system = 'system';

// Launch
require $system . DIRECTORY_SEPARATOR . 'launcher.php';