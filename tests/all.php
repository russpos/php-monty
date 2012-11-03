<?php
$dir = dirname(__FILE__);
function run_test($name) {
    $args = array('verbose' => true);
    require_once $name.".php";
}
require "$dir/../tpt/tpt/tpt.php";
require "$dir/../monty/bootstrap.php";

run_test("Model");
