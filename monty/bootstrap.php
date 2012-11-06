<?php
if (!defined('DS')) define('DS', '/');
define('MONTY_TIME', time());
define('MONTY_MICROTIME', microtime());
define('MONTY_DIR', dirname(__FILE__));
define('MONTY_LIB', MONTY_DIR.DS.'phplib');

function __autoload($class_name) {
    $parts = explode('_', $class_name);
    $file_path = implode(DS, $parts).'.php';
    if ($parts[0] == 'Monty') {
        require_once MONTY_LIB.DS.$file_path;
    } else {
        require_once APP_LIB.DS.$file_path;
    }
}
