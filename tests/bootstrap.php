<?php 
/**
 *---------------------------------------------------------------
 * Environment and paths
 *---------------------------------------------------------------
 */
$environment = 'test';
$application = './application';
$modules     = './modules';
$packages    = './packages';
$system      = './system';

define('PATH_SYS',   realpath($system)      . DIRECTORY_SEPARATOR);
define('EXT', '.php');

/**
 *---------------------------------------------------------------
 * Load the launcher
 *---------------------------------------------------------------
 */
require_once PATH_SYS . 'launcher' . EXT;