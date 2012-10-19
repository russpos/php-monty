<?php
echo "<pre>"; print_r($_SERVER); 
require "../bootstrap.php";
$app = new Monty_App(
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

$app->serve($_SERVER, $_GET, $_POST);
echo "</pre>";
