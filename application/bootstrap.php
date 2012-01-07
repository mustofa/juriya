<?php

return array(
	// This will used as Model DB instance
	'db'     => array('prototype' => function() {
                          // To use NotORM as DB instance,
                          // uncomment below blocks
                          /*
                          // Include NotORM from packages 
                          include_once PATH_PKG . 'notorm' . DIRECTORY_SEPARATOR . 'NotORM' . EXT;

                          // Configure DB param and instantiate new DB
                          $pdo = new \PDO('mysql:host=localhost;dbname=dbname',
                                          'username',
                                          'password');
                          $db  = new \NotORM($pdo);

                          return $db;
                          */
	                  }),
	// This will used as View Parser instance
	'parser' => array('prototype' => function() {
                          // To use dwoo as parser instance,
                          // uncomment below blocks
                          /*
                          // Include dwoo Autoloader from packages
                          include_once PATH_PKG . 'dwoo' . DIRECTORY_SEPARATOR . 'dwooAutoload' . EXT;

                          // Instantiate new Dwoo as template parser, and process the template params
                          $parser = new \Dwoo();

                          return $parser;
                          */
	                  }),
);