<?php

// Step 1: Require the Monty bootstrap file
require "../../monty/bootstrap.php";

// Step 2: Define these constants
define('APP_WWW', dirname(__FILE__));
define('APP_DIR', dirname(APP_WWW));
define('APP_LIB', APP_DIR.DS.'phplib');
define('APP_TPL', APP_DIR.DS.'templates');
define('APP_SCHEMA', APP_DIR.DS.'schema');
define('APP_ENV', isset($_ENV['APP_ENV']) ? $_ENV['APP_ENV'] : 'development');

// Optional: Do any config ovrrides!
ini_set("log_errors" , "1");
ini_set("display_errors" , "0");

// Step 4. Create an instance of your application
$app = new SampleApp(
    array(

        // Database configs
        'database' => array(
            'user' => 'root',
            'password' => 'monty',
            'host' => 'localhost',
            'database' => 'monty',
        )
    )
);

// Step 5. SERVE!
$app->serve($_SERVER, $_GET, $_POST);
