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

/**
 *---------------------------------------------------------------
 * Load the launcher
 *---------------------------------------------------------------
 */
require_once realpath($system) . DIRECTORY_SEPARATOR . 'launcher.php';